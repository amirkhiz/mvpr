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
// | faqMgr.php                                                    |
// +---------------------------------------------------------------------------+
// | Author: Sina Saderi <sina.saderi@gmail.com>                                  |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
/**
 * Type your class description here ...
 *
 * @package faq
 * @author  Sina Saderi <sina.saderi@gmail.com>
 */
class FaqMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'Faq Manager';
        $this->template     = 'faqList.html';

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
        $input->faq = (object)$req->get('faq');
        $input->faqId = $req->get('frmFaqID');
        
    	if (($input->action == 'insert' or $input->action == 'update') && $input->action != 'reorder' ) {
            // validate input data
            if (empty($input->faq->question)) {
                $aErrors['question'] = 'Please fill in a question';
            }
            
    		if (empty($input->faq->answer)) {
                $aErrors['answer'] = 'Please fill in a answer';
            }
    	}
    	
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->template = 'faqEdit.html';
            $input->error = $aErrors;
            $this->validated = false;
        	    
        	if ($input->action == 'insert') {
                $input->pageTitle .= ' :: Add';
            } elseif ($input->action == 'update') {
                $input->pageTitle .= ' :: Edit';
            }
        }
    }

    function display($output)
    {
        if ($this->conf['FaqMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'faqEdit.html';
        $output->pageTitle = 'FaqMgr :: Add';
        $output->action    = 'insert';
        $output->wysiwyg   = true;    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $faq = DB_DataObject::factory($this->conf['table']['faq']);
        $faq->setFrom($input->faq);
        $faq->faq_id = $this->dbh->nextId($this->conf['table']['faq']);
        $success = $faq->insert();

        if ($success !== false) {
            SGL::raiseMsg('faq insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('faq insert NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'faqEdit.html';
        $output->pageTitle = 'FaqMgr :: Edit';
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $faq = DB_DataObject::factory($this->conf['table']['faq']);
        $faq->get($input->faqId);
        $output->faq = $faq;    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $faq = DB_DataObject::factory($this->conf['table']['faq']);
        $faq->faq_id = $input->faqId;
        $faq->find(true);
        $faq->setFrom($input->faq);
        $success = $faq->update();

        if ($success !== false) {
            SGL::raiseMsg('faq update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('faq update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'faqList.html';
        $output->pageTitle = 'FaqMgr :: List';

        //  only execute if CRUD option selected
        if (true) {
            $query = "  SELECT
                             *
                        FROM {$this->conf['table']['faq']}
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
            foreach ($input->aDelete as $index => $faqId) {
                $faq = DB_DataObject::factory($this->conf['table']['faq']);
                $faq->get($faqId);
                $faq->delete();
                unset($faq);
            }
            SGL::raiseMsg('faq delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('faq delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }    }


}
?>