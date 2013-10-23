<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2010, Demian Turner                                         |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | o Redistributions of source code must retain the above copyright          |
// |   notice, this list of conditions and the following disclaimer.           |
// | o Redistributions in binary form must reproduce the above copyright       |
// |   notice, this list of conditions and the following disclaimer in the     |
// |   documentation and/or other materials provided with the distribution.    |
// | o The names of the authors may not be used to endorse or promote          |
// |   products derived from this software without specific prior written      |
// |   permission.                                                             |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Seagull 1.1                                                               |
// +---------------------------------------------------------------------------+
// | content_typeMgr.php                                                    |
// +---------------------------------------------------------------------------+
// | Author: Sina Saderi <sina.saderi@gmail.com>                                  |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
require_once SGL_MOD_DIR  . '/content/classes/ContentDAO.php';
require_once SGL_CORE_DIR . '/Delegator.php';
/**
 * Type your class description here ...
 *
 * @package content
 * @author  Sina Saderi <sina.saderi@gmail.com>
 */
class TypeMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'Type Manager';
        $this->template     = 'type/typeList.html';
        
        $daContent	= ContentDAO::singleton();
        $this->da	= new SGL_Delegator();
        $this->da->add($daContent);

        $this->_aActionsMapping =  array(
            'add'       => array('add'),
            'insert'    => array('insert', 'redirectToDefault'),
            'edit'      => array('edit'), 
            'update'    => array('update', 'redirectToDefault'),
            'list'      => array('list'),
            'delete'    => array('delete', 'redirectToDefault'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->aDelete     = $req->get('frmDelete');
        $input->submitted   = $req->get('submitted');
        $input->type 		= (object)$req->get('type');
        $input->typeId 		= $req->get('frmTypeID');

        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->error = $aErrors;
            $this->validated = false;
        }
    }

    function display($output)
    {
        if ($this->conf['TypeMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }

    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'type/typeAdd.html';
        $output->pageTitle = 'TypeMgr :: Add';
        $output->action    = 'insert';
        
        $output->inputTypes = $this->da->inputTypeArray();
        
        $category = DB_DataObject::factory($this->conf['table']['category']);
        
        $category->whereAdd("level_id = 3");
        $category->find();
        $aCategories = array();
        while($category->fetch()){
        	$aCategories[$category->category_id] = $category->title;
        }
        $output->categories = $aCategories;
        
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $type = DB_DataObject::factory($this->conf['table']['content_type']);
        $type->setFrom($input->type);
        $typeId = $this->dbh->nextId($this->conf['table']['content_type']);
        $type->content_type_id	= $typeId;
        $type->usr_id = SGL_Session::getUid();
        $success = $type->insert();

        
        $aTags = array();
        foreach($input->type as $tKey => $tArray)
        {
        	foreach($tArray as $key => $value)
        	{
        		$i = 0;
				foreach($input->type->{$tKey}[$key] as $vKey => $vValue)
				{
					$aTags[$tKey][$vKey][$key] = $input->type->{$tKey}[$key][$vKey];
				}
        	}
        }
        foreach($aTags as $aKey => $aValue)
        {
        	foreach($aValue as $key => $value)
        	{
        		
        		$value['type'] = $aKey;
        		$typeMap = DB_DataObject::factory($this->conf['table']['content_type_mapping']);
        		$typeMap->content_type_mapping_id = $this->dbh->nextId($this->conf['table']['content_type_mapping']);
        		$typeMap->title = $value['label'];
        		$typeMap->content_type_id = $typeId;
        		$typeMap->options 	= serialize($value);
        		$typeMap->tag_order	= $value['order'];
        		$typeMap->insert();
        		
        		//echo "<pre>"; print_r($value); echo "</pre>"; exit;
        		$aMapDatas = explode("~~||~~", $value['multipleselects']);
				foreach($aMapDatas as $mKey => $mValue)
				{
					$mValue = explode("|",$mValue);
					$mTitle = $mValue[1];
					$mapData = DB_DataObject::factory($this->conf['table']['content_type_mapping_data']);
					$mapData->content_type_mapping_data_id = $this->dbh->nextId($this->conf['table']['content_type_mapping_data']);
					$mapData->content_type_mapping_id = $typeMap->content_type_mapping_id;
					$mapData->content_type_id = $typeId;
					$mapData->order_id = $mKey + 1;
					$mapData->title = $mTitle;
					$mapData->insert();
				}
        	}
        }
        
        if ($success !== false) {
            SGL::raiseMsg('content_type insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('content_type insert NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'type/typeEdit.html';
        $output->pageTitle = 'TypeMgr :: Edit';
        $output->action    = 'update';
        $output->inputTypes = $this->da->inputTypeArray();

        $type = DB_DataObject::factory($this->conf['table']['content_type']);
        $type->get($input->typeId);
        $output->type = $type;
        unset($type);
        
        $type = DB_DataObject::factory($this->conf['table']['content_type_mapping']);
        $type->whereAdd("content_type_id='".$input->typeId."'");
        $type->orderBy("tag_order");
        $type->find();
		$aTypes = array();
		while($type->fetch())
		{
			$aTypes[]=$type->options;
		}
		$output->tags = $aTypes;
		
		
		$category = DB_DataObject::factory($this->conf['table']['category']);
        
        $category->whereAdd("level_id = 3");
        $category->find();
        $aCategories = array();
        while($category->fetch()){
        	$aCategories[$category->category_id] = $category->title;
        }
        $output->categories = $aCategories;
		
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $type = DB_DataObject::factory($this->conf['table']['content_type']);
        $type->content_type_id = $input->typeId;
        $type->find(true);
        $type->setFrom($input->type);
        $success = $type->update();

        if ($success !== false) {
            SGL::raiseMsg('content_type update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('content_type update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }    
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'type/typeList.html';
        $output->pageTitle = 'TypeMgr :: List';

        //  only execute if CRUD option selected
        if (true) {
            $query = "  SELECT
                             *
                        FROM {$this->conf['table']['content_type']}
                        ";

            $limit = $_SESSION['aPrefs']['resPerPage'];
            $pagerOptions = array(
                'mode'      => 'Sliding',
                'delta'     => 3,
                'perPage'   => $limit,
            );
            $aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
            if (PEAR::isError($aPagedData)) {
                return false;
            }
            $output->aPagedData = $aPagedData;
            $output->totalItems = $aPagedData['totalItems'];

            if (is_array($aPagedData['data']) && count($aPagedData['data'])) {
                $output->pager = ($aPagedData['totalItems'] <= $limit) ? false : true;
            }
            
        }
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $typeId) {
                $content_type = DB_DataObject::factory($this->conf['table']['content_type']);
                $content_type->get($typeId);
                $content_type->delete();
                unset($content_type);
                
                $this->dbh->query("delete from {$this->conf['table']['content_type_mapping']} where content_type_id = '$typeId'");
                $this->dbh->query("delete from {$this->conf['table']['content_type_mapping_data']} where content_type_id = '$typeId'");

                /*
                $content_type_mapping = DB_DataObject::factory($this->conf['table']['content_type_mapping']);
                $content_type_mapping->whereAdd("content_type_id = " . $typeId);
                $content_type_mapping->find();
                $content_type_mapping->delete();
                unset($content_type_mapping);
                
                $content_type_mapping_data = DB_DataObject::factory($this->conf['table']['content_type_mapping_data']);
                $content_type_mapping_data->whereAdd("content_type_id = " . $typeId);
                $content_type_mapping_data->find();
                $content_type_mapping_data->delete();
                unset($content_type_mapping_data);
                */
            }
            SGL::raiseMsg('content_type delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('content_type delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }    
    }


}
?>
