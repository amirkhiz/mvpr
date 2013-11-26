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
// | BasketMgr.php                                                    		   |
// +---------------------------------------------------------------------------+
// | Author: Siavash Habil Amirkhiz <amirkhiz@gmail.com>                       |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
/**
 * Type your class description here ...
 *
 * @package basket
 * @author  Siavash Habil Amirkhiz <amirkhiz@gmail.com>
 */
class BasketMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'BasketMgr';
        $this->template     = 'basketList.html';

        $this->_aActionsMapping =  array(
            'add'       	=> array('add'),
            'insert'    	=> array('insert', 'redirectToDefault'),
            'edit'      	=> array('edit'), 
            'update'    	=> array('update', 'redirectToDefault'),
            'list'      	=> array('list'),
            'delete'    	=> array('delete', 'redirectToDefault'),
        	'orders'		=> array('orders'),
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
        $input->basket 		= (object)$req->get('basket');
        $input->basketId 	= $req->get('frmBasketID');
        $input->productId 	= $req->get('productId');
        $input->basket      = (object)$req->get('basket');
        
    }

    function display($output)
    {
        if ($this->conf['BasketMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }


    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'basketEdit.html';
        $output->pageTitle = 'BasketMgr :: Add';
        $output->action    = 'insert';
        $output->wysiwyg   = true;    
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        $usrId = SGL_Session::getUid();
        
        SGL_DB::setConnection();
        
        $basket = DB_DataObject::factory($this->conf['table']['basket']);
        $basket->setFrom($input->basket);
        $basket->basket_id = $this->dbh->nextId($this->conf['table']['basket']);
        $basket->usr_id = $usrId;
        $basket->date_created = SGL_Date::getTime(true);
        $success = $basket->insert();

        if ($success !== false) {
            SGL::raiseMsg('basket insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError("basket insert NOT successfull",
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'basketEdit.html';
        $output->pageTitle = 'BasketMgr :: Edit';
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $basket = DB_DataObject::factory($this->conf['table']['basket']);
        $basket->get($input->basketId);
        $output->basket = $basket;    
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $basket = DB_DataObject::factory($this->conf['table']['basket']);
        $basket->basket_id = $input->basketId;
        $basket->find(true);
        $basket->setFrom($input->basket);
        $basket->date_updated 	= SGL_Date::getTime(true);
        $basket->updated_by 	= SGL_Session::getUid();
        $success = $basket->update();

        if ($success !== false) {
            SGL::raiseMsg('basket update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('basket update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }    
    }
    
    function _cmd_orders(&$input, &$output)
    {

    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$usrId = SGL_Session::getUid();
    	$orgId = SGL_Session::getOrganisationId();
        $output->template  = 'basketList.html';
        $output->pageTitle = 'BasketMgr :: List';
        //  only execute if CRUD option selected

        if (true) {
            $query = "select b.*, p.title, pi.title as imageUrl, cu.code, p.dprice 
        			from basket as b
					left join product as p on p.product_id = b.product_id
					left join product_image as pi on p.product_id = pi.product_id 
					left join currency as cu on cu.currency_id = p.currency_id
					left join usr as u on u.usr_id = p.usr_id 
					where u.organisation_id = '$orgId' 
					group by basket_id 
					order by date_created desc";

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
            //echo "<pre>"; print_r($aPagedData['data']); echo "</pre>";
        }
    }
    
    function _cmd_list(&$input, &$output)
    {

    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$usrId = SGL_Session::getUid();
    	
        $output->template  = 'basketList.html';
        $output->pageTitle = 'BasketMgr :: List';
        
        //  only execute if CRUD option selected

        if (true) {
        	/*
            $query = "
            		SELECT 
            			p.*,
            			pi.title AS image,
            			ba.quantity AS basQuantity, p.quantity as pQuantity, ba.date_created AS basCreate,
            			cur.symbol_left AS curSymLeft, cur.symbol_right AS curSymRight
            		FROM {$this->conf['table']['product']} AS p
                    LEFT JOIN {$this->conf['table']['basket']} AS ba
                    ON p.product_id = ba.product_id
	        		LEFT JOIN {$this->conf['table']['product_image']} AS pi
	        		ON pi.product_id = p.product_id
	       			LEFT JOIN {$this->conf['table']['currency']} AS cur
	       			ON cur.currency_id = p.currency_id
                    WHERE ba.usr_id = {$usrId}
            		GROUP BY p.product_id
                    ORDER BY ba.date_created
				";
			*/
        	$query = "select b.*, p.title, pi.title as imageUrl, cu.code, p.dprice 
        			from basket as b
					left join product as p on p.product_id = b.product_id
					left join product_image as pi on p.product_id = pi.product_id 
					left join currency as cu on cu.currency_id = p.currency_id
					where p.usr_id = '$usrId' 
					group by basket_id 
					order by date_created desc";
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
            //echo "<pre>"; print_r($aPagedData['data']); echo "</pre>";
        }
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $basketId) {
                $basket = DB_DataObject::factory($this->conf['table']['basket']);
                $basket->get($basketId);
                $basket->delete();
                unset($basket);
            }
            SGL::raiseMsg('basket delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('basket delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }    
    }

}
?>