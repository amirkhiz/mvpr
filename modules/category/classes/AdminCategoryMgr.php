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
// | Seagull 1.0                                                               |
// +---------------------------------------------------------------------------+
// | categoryMgr.php                                                    |
// +---------------------------------------------------------------------------+
// | Author: Sina Saderi <sina.saderi@gmail.com>                                  |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
/**
 * Type your class description here ...
 *
 * @package category
 * @author  Sina Saderi <sina.saderi@gmail.com>
 */
class AdminCategoryMgr extends SGL_Manager
{
	var $catTree = "";
	var $aLevelTitle = array(); 
	
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();
        
        $this->aLevelTitle = array(
        				1 => "Category",
        				2 => "Product group",
        				3 => "Product property",
        				4 => "Brand"
        );

        $this->pageTitle    = 'CategoryMgr';
        $this->template     = 'categoryList.html';

        $this->_aActionsMapping =  array(
            'add'       	=> array('add'),
            'insert'    	=> array('insert'),
            'edit'      	=> array('edit'), 
            'update'    	=> array('update'),
            'list'      	=> array('list'),
            'delete'    	=> array('delete'),
        	'reorder'       => array('reorder'),
            'reorderUpdate' => array('reorderUpdate'),
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
        $input->category 	= (object)$req->get('category');
        $input->parentId    = $req->get('frmParentId');
        $input->categoryId 	= $req->get('frmCategoryID');
        $input->levelId 	= $req->get('frmLevelID');
        $input->items     	= $req->get('_items');
        
        /*
    	if (($input->action == 'insert' or $input->action == 'update') && $input->action != 'reorder' ) {
            // validate input data
            if (empty($input->category->question)) {
                $aErrors['question'] = 'Please fill in a question';
            }
            
    		if (empty($input->category->answer)) {
                $aErrors['answer'] = 'Please fill in a answer';
            }
            
    	}
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->template = 'categoryEdit.html';
            $input->error = $aErrors;
            $this->validated = false;
            
        	if ($input->action == 'insert') {
                $input->pageTitle .= ' :: Add';
            } elseif ($input->action == 'update') {
                $input->pageTitle .= ' :: Edit';
            }
        }
        */
    }

    function display($output)
    {
        if ($this->conf['CategoryMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'categoryEdit.html';
        
        $output->pageTitle = "Add " . $this->aLevelTitle[$input->levelId];
        
        $output->action    = 'insert';
        $output->wysiwyg   = true;
        
        $parentLevelId = $input->levelId - 1;
        
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->whereAdd("level_id = " . $parentLevelId);
        $category->find();
        $aCategories = array();
        while($category->fetch()){
        	$aCategories[$category->category_id] = $category->title;
        }
        $output->categories = $aCategories;
        
        $output->levelId = $input->levelId;
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        #echo "<pre>"; print_r($input->category); echo "</pre>"; exit;
        
        
        SGL_DB::setConnection();
        //  get new order number
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->selectAdd();
        $category->selectAdd('MAX(order_id) AS new_order');
        $category->groupBy('order_id');
        $maxOrderId = $category->find(true);
        unset($category);
        
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->setFrom($input->category);
        $category->category_id 		= $this->dbh->nextId($this->conf['table']['category']);

        $category->status 			= (isset($input->category->status)) ? 1 : 0;
        
        $category->created_date 	= SGL_Date::getTime(true);
        $category->updated_date 	= SGL_Date::getTime(true);
        $category->created_by 		= SGL_Session::getUid();
        $category->updated_by 		= SGL_Session::getUid();
        $category->order_id 		= $maxOrderId;
        $success = $category->insert();

        if ($success !== false) {
            SGL::raiseMsg('category insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError("category insert NOT successfull",
                SGL_ERROR_NOAFFECTEDROWS);
        }
        
        $options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		    'frmLevelID' => $input->levelId
		);
		SGL_HTTP::redirect($options);
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'categoryEdit.html';
        
        
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->get($input->categoryId);
        $output->category = $category;
        
        $output->levelId = $category->level_id;
        $output->pageTitle = "Edit " . $this->aLevelTitle[$category->level_id];
        
        $output->isPublished = empty($output->category->status) ? '' : 'checked';
        
        $parentLevelId = $category->level_id - 1;
        
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->whereAdd("level_id = " . $parentLevelId);
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
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $category->category_id = $input->categoryId;
        $category->find(true);
        $category->setFrom($input->category);
        
        $category->status 			= (isset($input->category->status)) ? 1 : 0;
        
        $category->updated_date 	= SGL_Date::getTime(true);
        $category->updated_by 		= SGL_Session::getUid();
        $success = $category->update();

        if ($success !== false) {
            SGL::raiseMsg('category update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('category update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }
        
        $options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		    'frmLevelID' => $category->level_id
		);
		SGL_HTTP::redirect($options);
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'categoryList.html';
        
        $output->pageTitle = $this->aLevelTitle[$input->levelId] . " list";
        $output->btnTitle = "New " . $this->aLevelTitle[$input->levelId];
        
        $output->parentTitle = $this->aLevelTitle[$input->levelId - 1];
        
        $output->searchCategory = "Search " . $this->aLevelTitle[$input->levelId - 1];
        
        $parentSearch = "";
        if(!empty($input->parentId)){
        	$parentSearch = " and c.parent_id = ' " . $input->parentId . " '";
        	$output->parentId = $input->parentId;
        }
        echo "<br />";
        if(!empty($input->categoryId)){
        	$topestCats = $this->dbh->getRow("select c1.category_id as topId, c2.parent_id as topestId 
        							from category as c1
        							join category as c2 on c2.category_id = c1.parent_id    
        							where c1.parent_id = '" . $input->categoryId . "'");
        	$parentSearch = " and c.parent_id = ' " . $topestCats->topestId . " '";
        	$output->parentId = $topestCats->topestId;
        }
    	$query = " SELECT 
    					c.*, m.title as pTitle 
    				FROM `category` as c left join category as m on c.parent_id = m.category_id
    				where c.level_id = ".$input->levelId."
    				$parentSearch
    				order by c.order_id
    				";

            $limit = $_SESSION['aPrefs']['resPerPage'];
            
            $pagerOptions = array(
                'mode'      => 'Sliding',
                'delta'     => 8,
                'perPage'   => 10, 

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
            $output->parentBox = "";
            if(!empty($input->parentId)){
            	$output->parentBox = $aPagedData['data'][0]['pTitle'];
            }
            $output->levelId = $input->levelId;
            $output->parentLevel = $input->levelId - 1;
            if($input->levelId != 4){
            	$output->childLevel = $input->levelId + 1;
            }

        //  only execute if CRUD option selected
        /*    
        $this->dbh->setFetchMode(DB_FETCHMODE_OBJECT);
            
		$query = "select * from category where parent_id='$id'";
	    $res =& $this->dbh->query($query);
	    $n = '';
		while($row =& $res->fetchRow()) 
		{ 
		    $this->catTree .= '
		    	<tr>
		    		<td align="center"><input type="checkbox" name="frmDelete[]" value="'.$row->category_id.'" /></td>
					<td nowrap>'.$row->category_id.'</td>
					<td class="left">'.$n.$row->title.'</td>
					<td nowrap>'.SGL_Output::formatDate($row->created_date).'</td>
					<td nowrap>';
		    			if($row->status){
		    				$this->catTree .= '<span class="btn btn-xs btn-primary">&nbsp;&nbsp;'  .SGL_Output::translate("Active") . '&nbsp;&nbsp;</span>';
		    			}else{
		    				$this->catTree .= '<span type="button" class="btn btn-xs btn-danger">' . SGL_Output::translate("inactive") . '</span>';
		    			}
	    		$this->catTree .= '
	    			<td width="10%" nowrap>
			            <a href="'.SGL_Output::makeUrl("edit","admincategory","category","","frmCategoryID|".$row->category_id).'" />
			                <span class="glyphicon glyphicon-edit"></span> '. SGL_Output::translate("edit") .'
			            </a> 
		            </td>';
				$this->catTree .= '
					</td>
		    	</tr>'; 
		    $this->multilevel($row->category_id); 
		} 
		
		
		$output->catTree = $this->catTree;
		*/
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $categoryId) {
                $category = DB_DataObject::factory($this->conf['table']['category']);
                $category->get($categoryId);
                $levelId = $category->level_id;
                $category->delete();
                unset($category);
            }
            SGL::raiseMsg('category delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('category delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
        
        $options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		    'frmLevelID' => $levelId
		);
		SGL_HTTP::redirect($options);
    }
    
    function _cmd_reorder(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = $this->pageTitle . ' :: Reorder';
        $output->template  = 'categoryReorder.html';
        $output->action    = 'reorderUpdate';
        $categoryList = DB_DataObject::factory($this->conf['table']['category']);
        $categoryList->whereAdd("level_id = " . $input->levelId);
        $categoryList->orderBy('order_id');
        $result = $categoryList->find();
        if ($result > 0) {
            $aFaqs = array();
            while ($categoryList->fetch()) {
                $aCategorys[$categoryList->category_id] = $categoryList->title;
            }
            $output->aCategorys = $aCategorys;
        }
    }
    
	function _cmd_reorderUpdate(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (!empty($input->items)) {
            $aNewOrder = explode(',', $input->items);
            //  reorder elements
            $pos = 1;
            foreach ($aNewOrder as $categoryId) {
                $category = DB_DataObject::factory($this->conf['table']['category']);
                $category->get($categoryId);
                $category->order_id = $pos;
                $levelId = $category->level_id;
                $success = $category->update();
                unset($category);
                $pos++;
            }
            SGL::raiseMsg('categorys reordered successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
        $options = array(
		    'moduleName' => 'category',
		    'managerName' => 'admincategory',
		    'action' => 'list',
		    'frmLevelID' => $levelId
		);
		SGL_HTTP::redirect($options);
    }
    
    function multilevel($id, $n = ' ') 
	{ 
		$this->dbh->setFetchMode(DB_FETCHMODE_OBJECT);
		$query = "select * from category where parent_id='$id'";
	    $res =& $this->dbh->query($query);
	    
	    if($res->numRows() == 0) return; 
	    $n .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	    while($row =& $res->fetchRow()) 
	    { 
		    $this->catTree .= '
			    	<tr>
			    		<td align="center"><input type="checkbox" name="frmDelete[]" value="'.$row->category_id.'" /></td>
						<td nowrap>'.$row->category_id.'</td>
						<td class="left">'.$n.$row->title.'</td>
						<td nowrap>'.SGL_Output::formatDate($row->created_date).'</td>
						<td nowrap>';
			    			if($row->status){
			    				$this->catTree .= '<span class="btn btn-xs btn-primary">&nbsp;&nbsp;'  .SGL_Output::translate("Active") . '&nbsp;&nbsp;</span>';
			    			}else{
			    				$this->catTree .= '<span type="button" class="btn btn-xs btn-danger">' . SGL_Output::translate("inactive") . '</span>';
			    			}
		    		$this->catTree .= '
		    			<td width="10%" nowrap>
				            <a href="'.SGL_Output::makeUrl("edit","admincategory","category","","frmCategoryID|".$row->category_id).'" />
				                <span class="glyphicon glyphicon-edit"></span> '. SGL_Output::translate("edit") .'
				            </a> 
			            </td>';
					$this->catTree .= '
						</td>
			    	</tr>'; 
		    $this->multilevel($row->category_id, $n); 
	    } 
	}



}
?>