<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2008, Demian Turner                                         |
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
// | Seagull 0.6                                                               |
// +---------------------------------------------------------------------------+
// | ProductMgr.php                                                            |
// +---------------------------------------------------------------------------+
// | Author: Siavash AmirKhiz <amirkhiz@gmail.com>                             |
// +---------------------------------------------------------------------------+

require_once 'DB/DataObject.php';
require_once SGL_MOD_DIR . '/product/classes/ProductMgr.php';
include_once SGL_MOD_DIR  . '/gallery/classes/Gallery.php';

/**
 * Allow admins to manage Products.
 *
 * @package Product
 * @author  Siavash AmirKhiz <amirkhiz@gmail.com>
 * @version $Revision: 1.26 $
 * @since   PHP 4.1
 */
class AdminproductMgr extends productMgr
{

    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle = 'Product Manager';
        $this->template  = 'productList.html';

        $this->_aActionsMapping =  array(
            'add'           => array('add'),
            'insert'        => array('insert', 'redirectToDefault'),
            'edit'          => array('edit'),
            'update'        => array('update', 'redirectToDefault'),
            'reorder'       => array('reorder'),
            'reorderUpdate' => array('reorderUpdate', 'redirectToDefault'),
            'delete'        => array('delete', 'redirectToDefault'),
            'list'          => array('list'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        parent::validate($req, $input);
        $input->productId   = $req->get('frmProductID');
        $input->items     	= $req->get('_items');
        $input->product     = (object)$req->get('product');

        // Misc.
        $input->submitted   = $req->get('submitted');
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->aDelete     = $req->get('frmDelete');
    
        if ($input->submitted && in_array($input->action, array('insert'))) {
            if (empty($input->product->title)) {
                $aErrors['title'] = 'Please fill in a title';
            }
        }    
        if ($input->submitted && in_array($input->action, array('update'))) {
            if (empty($input->product->title)) {
                $aErrors['title'] = 'Please fill in a title';
            }
        } 
            
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->error    = $aErrors;
            $input->template = 'productEdit.html';
            $this->validated = false;

            // save currect title
            if ($input->action == 'insert') {
                $input->pageTitle .= ' :: Add';
            } elseif ($input->action == 'update') {
                $input->pageTitle .= ' :: Edit';
            }
        }
        
    }

    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template   = 'productEdit.html';
        $output->action     = 'insert';
        $output->pageTitle  = $this->pageTitle . ' :: Add';
        $output->productLang = SGL_Translation::getFallbackLangID();
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        $usrId = SGL_Session::getUid();

        SGL_DB::setConnection();
        //  get new order number
        $product = DB_DataObject::factory($this->conf['table']['product']);
        $product->selectAdd();
        $product->selectAdd('MAX(order_id) AS new_order');
        $product->groupBy('order_id');
        $maxItemOrder = $product->find(true);
        unset($product);

        //  insert record
        $product = DB_DataObject::factory($this->conf['table']['product']);
        $product->setFrom($input->product);
        $product->product_id 	= $this->dbh->nextId('product');
        $product->usr_id 		= $usrId;
        $product->last_updated 	= $product->date_created = SGL_Date::getTime(true);
        $product->order_id 	= $maxItemOrder + 1;
		
        $success = $product->insert();
        
        if ($success) {
            SGL::raiseMsg('product saved successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('There was a problem inserting the record',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template  = 'productEdit.html';
        $output->action    = 'update';
        $output->pageTitle = $this->pageTitle . ' :: Edit';
        $product = DB_DataObject::factory($this->conf['table']['product']);

        //  get product data
        $product->get($input->productId);
        $output->product = $product;
        
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $product = DB_DataObject::factory($this->conf['table']['product']);
        $product->get($input->product->product_id);
        $product->setFrom($input->product);
        $product->last_updated = SGL_Date::getTime(true);
        $product->usr_id = SGL_Session::getUid();
        
        $success = $product->update();
        if ($success) {
            SGL::raiseMsg('product updated successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('There was a problem updating the record',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $productId) {
                $product = DB_DataObject::factory($this->conf['table']['product']);
                $product->get($productId);
                unlink(SGL_WEB_ROOT.$product->image1);
                $product->delete();
                unset($product);
            }
            SGL::raiseMsg('product deleted successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }

    function _cmd_reorder(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = $this->pageTitle . ' :: Reorder';
        $output->template  = 'productReorder.html';
        $output->action    = 'reorderUpdate';
        $productList = DB_DataObject::factory($this->conf['table']['product']);
        $productList->orderBy('order_id');
        $result = $productList->find();
        if ($result > 0) {
            $aproducts = array();
            while ($productList->fetch()) {
                $aproducts[$productList->product_id] = SGL_String::summarise($productList->title, 40);
            }
            $output->aproducts = $aproducts;
        }
    }

    function _cmd_reorderUpdate(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (!empty($input->items)) {
            $aNewOrder = explode(',', $input->items);
            //  reorder elements
            $pos = 1;
            foreach ($aNewOrder as $productId) {
                $product = DB_DataObject::factory($this->conf['table']['product']);
                $product->get($productId);
                $product->order_id = $pos;
                $success = $product->update();
                unset($product);
                $pos++;
            }
            SGL::raiseMsg('products reordered successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template = 'productList.html';

        $query = "
        	SELECT p.*, u.username
        	FROM {$this->conf['table']['product']} AS p
        	LEFT JOIN {$this->conf['table']['user']} AS u
        	ON (u.usr_id = p.usr_id)
        	ORDER BY order_id
        ";

        $limit = $_SESSION['aPrefs']['resPerPage'];
        $pagerOptions = array(
            'mode'     => 'Sliding',
            'delta'    => 3,
            'perPage'  => $limit,
            'spacesBeforeSeparator' => 0,
            'spacesAfterSeparator'  => 0,
            'curPageSpanPre'        => '<span class="currentPage">',
            'curPageSpanPost'       => '</span>',
        );
        $aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);

        //  determine if pagination is required
        $output->aPagedData = $aPagedData;
        if (is_array($aPagedData['data']) && count($aPagedData['data'])) {
            $output->pager = ($aPagedData['totalItems'] <= $limit) ? false : true;
        }

        $user = DB_DataObject::factory($this->conf['table']['user']);
        $user->selectAdd('usr_id');
        $usrId = $user->find(true);
        
        //echo "<pre>"; print_r($output->aPagedData); echo "</pre>"; exit;

        $output->addOnLoadEvent("switchRowColorOnHover()");
    }
}

?>
