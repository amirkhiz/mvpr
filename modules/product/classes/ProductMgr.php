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
// | ProductMgr.php                                                             |
// +---------------------------------------------------------------------------+
// | Author:   Siavash AmirKhiz <amirkhiz@gmail.com>                           |
// +---------------------------------------------------------------------------+
// $Id: productMgr.php,v 1.26 2005/06/12 17:57:57 demian Exp $

require_once 'DB/DataObject.php';

/**
 * Allow users to see products.
 *
 * @package Product
 * @author  Siavash AmirKhiz <amirkhiz@gmail.com>
 * @version 1.0
 * @since   PHP 4.1
 */
class productMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle = 'products';
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
        	'search'        => array('search'),
        	'view'       	=> array('view'),
        );
        
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::validate($req, $input);
        
        $this->validated       = true;
        $input->pageTitle      = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template       = $this->template;
        
        $input->categoryId  = $req->get('frmCategoryID');
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

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $productList = DB_DataObject::factory($this->conf['table']['product']);
        $user = DB_DataObject::factory($this->conf['table']['user']);
        $productList->joinAdd($user, 'LEFT', 'AS u', 'usr_id');
        $productList->orderBy('order_id');
        $result = $productList->find();
        $aproducts  = array();
        if ($result > 0) {
            while ($productList->fetch()) {
                $productList->title = $productList->title;
                $aproducts[]        = clone($productList);
            }
        }
        $output->results = $aproducts = $this->objectToArray($aproducts);
        //echo '<pre>';print_r($aproducts);echo '</pre>';die;
    
    }
    
    function _cmd_add(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$output->template   = 'productEdit.html';
    	$output->action     = 'insert';
    	$output->pageTitle  = $this->pageTitle . ' :: Add';
    	$output->productLang = SGL_Translation::getFallbackLangID();
    	
    	$this->edit_display($output);
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
    	$product->product_id 	= $productId = $this->dbh->nextId('product');
    	$product->usr_id 		= $usrId;
    	$product->last_updated 	= $product->date_created = SGL_Date::getTime(true);
    	$product->status	 	= 1;
    	$product->order_id 	= $maxItemOrder + 1;
    
    	$success = $product->insert();
    	
    	//  insert options in content_addition table
    	foreach ($input->product->prop as $key => $value)
    	{
    		foreach ($value as $opKey => $opValue)
    		{
		    	$cAddition = DB_DataObject::factory($this->conf['table']['content_addition']);
		    	$cAddition->setFrom($input->product->prop);
		    	$cAddition->content_addition_id				= $this->dbh->nextId('content_addition');
		    	$cAddition->product_id 						= $productId;
		    	$cAddition->content_type_mapping_data_id 	= $opValue;
		    	
		    	$cASuccess = $cAddition->insert();
    		}
    	}
    
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
    	
    	$productId = $input->productId;
    
    	$output->template  = 'productEdit.html';
    	$output->action    = 'update';
    	$output->pageTitle = $this->pageTitle . ' :: Edit';
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	//  get product data
    	$product->get($productId);
    	
		$query = "
			SELECT c1.category_id AS `brandId`, c2.category_id AS `optionId`, c3.category_id AS `groupId`, c4.category_id AS `categoryId`, cmd.content_type_mapping_data_id AS cmdId, cm.content_type_mapping_id AS cmId, c.content_type_id AS cId, ca.content_addition_id AS caId,
			c1.title AS `brand`, c2.title AS `option`, c3.title AS `group`, c4.title AS `category`, cmd.title AS cmdTitle, cm.title AS cmTitle, c.type_name AS cTitle
			FROM {$this->conf['table']['product']} AS p
			JOIN {$this->conf['table']['content_addition']} AS ca
			ON ca.product_id = p.product_id
			JOIN {$this->conf['table']['content_type_mapping_data']} AS cmd
			ON cmd.content_type_mapping_data_id = ca.content_type_mapping_data_id
			JOIN {$this->conf['table']['content_type_mapping']} AS cm
			ON cm.content_type_mapping_id = cmd.content_type_mapping_id
			JOIN {$this->conf['table']['content_type']} AS c
			ON c.content_type_id = cm.content_type_id
			JOIN {$this->conf['table']['category']} AS c1
			ON c1.category_id = p.category_id
			JOIN {$this->conf['table']['category']} AS c2
			ON c2.category_id = c1.parent_id
			JOIN {$this->conf['table']['category']} AS c3 
			ON c3.category_id = c2.parent_id
			JOIN {$this->conf['table']['category']} AS c4 
			ON c4.category_id = c3.parent_id
			WHERE p.product_id = {$productId}
		";
		
		$cats =  $this->dbh->getAll($query);
		
		//echo '<pre>'; print_r($cats); echo '</pre>';die;
		
		$aCats = array();
		$aOpts = array();
		foreach ($cats as $value)
		{
			$aCats['category'] 		= $value->categoryId;
			$aCats['group'] 		= $value->groupId;
			$aCats['option']		= $value->optionId;
			$aCats['brand'] 		= $value->brandId;

			$aOpts['selected'][] 	= $value->cmdId;
		}
		//echo '<pre>'; print_r($aCAId); echo '</pre>';die;
		
		foreach ($aCats as $key => $value)
		{
			$query = "
				SELECT category_id, title
				FROM {$this->conf['table']['category']}
				WHERE parent_id = {$value}
			";
			$cats =  $this->dbh->getAll($query);
			foreach ($cats as $catId => $catVal)
			{
				
				switch ($key)
				{
					case 'category':
						$aGroup[$catVal->category_id] = $catVal->title;
					break;
					
					case 'group':
						$aOptions[$catVal->category_id] = $catVal->title;
					break;
					
					case 'option':
						$aBrands[$catVal->category_id] = $catVal->title;
					break;
				}
			}
		}
		
		$query = "
			SELECT cm.content_type_mapping_id as cmId, cm.title AS cmTitle, cmd.content_type_mapping_data_id AS cmdID, cmd.title AS cmdTitle
			FROM {$this->conf['table']['content_type']} as c
			JOIN {$this->conf['table']['content_type_mapping']} AS cm
			ON cm.content_type_id = c.content_type_id
			JOIN {$this->conf['table']['content_type_mapping_data']} AS cmd
			ON cmd.content_type_mapping_id = cm.content_type_mapping_id
			WHERE c.category_id = {$aCats['option']}
			";
		$cmData =  $this->dbh->getAll($query);
		
		$aOptList = array();
		foreach ($cmData as $key => $value)
		{
			$aOptList[$value->cmId]['title'] = $value->cmTitle;
			$aOptList[$value->cmId]['ops'][$value->cmdID] = $value->cmdTitle;
		}
		//echo '<pre>'; print_r($aOptions); echo '</pre>';
		
		$output->aOptList 	= $aOptList;
		$output->aGroup 	= $aGroup;
		$output->aOptions 	= $aOptions;
		$output->aBrands 	= $aBrands;
		$output->aCats 		= $aCats;
		$output->aOpts 		= $aOpts;
		$output->product 	= $product;
    }
    
    function _cmd_update(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	$product->get($input->product->product_id);
    	$product->setFrom($input->product);
    	$product->last_updated = SGL_Date::getTime(true);
    	$product->usr_id = SGL_Session::getUid();
    	
    	//Delete Product Properties For Update in content addition table
    	$cAddition = DB_DataObject::factory($this->conf['table']['content_addition']);
    	$cAddition->whereAdd('product_id = ' . $input->product->product_id);
    	$cAddition->find();
    	while ($cAddition->fetch())
    	{
    		$cAddition->delete();
    	}
    	unset($cAddition);
    	
    	foreach ($input->product->prop as $key => $value)
    	{
    		foreach ($value as $opKey => $opValue)
    		{
    			//Insert Product New properties in content addition table
    			$cAddition = DB_DataObject::factory($this->conf['table']['content_addition']);
		    	$cAddition->setFrom($input->product->prop);
		    	$cAddition->content_addition_id				= $this->dbh->nextId('content_addition');
		    	$cAddition->product_id 						= $input->product->product_id;
		    	$cAddition->content_type_mapping_data_id 	= $opValue;
		    	
		    	$cASuccess = $cAddition->insert();
    		}
    	}
    
    	$success = $product->update();
    	if ($success) {
    		SGL::raiseMsg('product updated successfully', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('There was a problem updating the record',
    				SGL_ERROR_NOAFFECTEDROWS);
    	}
    }
    
    function edit_display(&$output) 
    {
    	$currency = DB_DataObject::factory($this->conf['table']['currency']);
        $result = $currency->find();
        
        $aCurrency  = array();
        if ($result > 0) {
        	while ($currency->fetch()) {
        		$curTitle = $currency->title;
        		$aCurrency[$currency->currency_id] = $curTitle;
        	}
        }
        
        $output->aCurrency = $aCurrency;
        
        $category = DB_DataObject::factory($this->conf['table']['category']);
        
        $category->whereAdd("level_id = 2");
        $category->find();
        $aCategories = array();
        while($category->fetch()){
        	$aCategories[$category->category_id] = $category->title;
        }
        $output->aGroup= $aCategories;
        
        //echo '<pre>';print_r($aCategories); echo '</pre>';die;
        
        $output->aTax = array(
        		1 => 'Taxable Goods',
        		2 => 'Downloadable Products'
        		);
        
        $output->aDim = array(
        		1 => 'Centimeter',
        		2 => 'Millimeter',
        		3 => 'Inch',
        );
        
        $output->aWeight = array(
        		1 => 'Kilogram',
        		2 => 'Gram',
        		3 => 'Pound',
        		4 => 'Ounce',
        );
        
        
        
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
    			
    			//Delete Product Properties from content addition table
    			$cAddition = DB_DataObject::factory($this->conf['table']['content_addition']);
    			$cAddition->whereAdd('product_id = ' . $productId);
    			$cAddition->find();
    			while ($cAddition->fetch())
    			{
    				$cAddition->delete();
    			}
    			unset($cAddition);
    		}
    		SGL::raiseMsg('product deleted successfully', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
    				__FUNCTION__, SGL_ERROR_INVALIDARGS);
    	}
    }
    
    function _cmd_search(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$query = "
    			SELECT c.content_type_id AS cId, c.type_name AS cTitle, cm.content_type_mapping_id AS cmId, cm.title AS cmTitle, 
    			cmd.content_type_mapping_data_id AS cmdId, cmd.title AS cmdTitle, ca.*
    			FROM {$this->conf['table']['content_type']} AS c
    			JOIN {$this->conf['table']['content_type_mapping']} AS cm
				ON cm.content_type_id = c.content_type_id
				JOIN {$this->conf['table']['content_type_mapping_data']} AS cmd
				ON cmd.content_type_mapping_id = cm.content_type_mapping_id
				JOIN {$this->conf['table']['content_addition']} AS ca
				ON ca.content_type_mapping_data_id = cmd.content_type_mapping_data_id
				WHERE c.category_id = {$input->categoryId}
    	";
    	
    	$result = $this->dbh->getAll($query);
    	
    	//echo '<pre>'; print_r($result); echo '</pre>';die;
    	
    	$searchFields = array();
    	$proCounter = array();
    	$aProductId = array();
		foreach ($result as $key => $value)
		{
			
			$proCounter[$value->cmdId]['title'] = $value->cmdTitle;
			$proCounter[$value->cmdId]['count']++ ;
			
			$searchFields[$value->cmId]['title'] = $value->cmTitle;
			$searchFields[$value->cmId]['ops'][$value->cmdId] = $value->cmdTitle . '(' .$proCounter[$value->cmdId]['count'] . ')';
			
			$aProductId[$value->product_id] = $value->product_id;
			
		}
		
		$query = "
				SELECT pro.*, FLOOR(minmax.minPrice) AS minPrice, FLOOR(minmax.maxPrice) AS maxPrice
				FROM 
				(
				     SELECT p.*, (p.price * cu.value) AS tlPrice
				     FROM {$this->conf['table']['product']} AS p
				     JOIN {$this->conf['table']['currency']} AS cu 
				     ON p.currency_id = cu.currency_id
				     WHERE p.product_id IN (" . implode(',',$aProductId) . ") 
				) AS pro,
				(
				     SELECT MIN(pr.price * cur.value) AS minPrice, MAX(pr.price * cur.value) AS maxPrice
				     FROM {$this->conf['table']['product']} AS pr
				     JOIN {$this->conf['table']['currency']} AS cur
				     ON pr.currency_id = cur.currency_id
				     WHERE pr.product_id IN (" . implode(',',$aProductId) . ")
				) AS minmax
			";
		
		$limit = $_SESSION['aPrefs']['resPerPage'];
		$pagerOptions = array(
			'mode'      => 'Sliding',
			'delta'     => 8,
			'perPage'   => 1000,
			
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
		
		$currency = DB_DataObject::factory($this->conf['table']['currency']);
		$currency->find();
		
		$aCur = array();
		while ($currency->fetch())
		{
			$aCur[$currency->currency_id] = $currency->code;
		}
    	
		//echo '<pre>'; print_r($aPagedData); echo '</pre>';die;
    	
    	$output->pageTitle 		= $this->pageTitle . ' :: Reorder';
    	$output->template  		= 'productSearch.html';
    	$output->catId			= $input->categoryId;
    	$output->searchFields 	= $searchFields;
    	$output->products 		= $productcs;
    	$output->minPrice 		= $aPagedData['data']['0']['minPrice'];
		$output->maxPrice 		= $aPagedData['data']['0']['maxPrice'];
    	$output->aCur 			= $aCur;
    	
    }
    
    function _cmd_view(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$output->pageTitle = $this->pageTitle . ' :: View';
    	$output->template  = 'productView.html';
    	
    	$productId = $input->productId;
    	
    	$query = "
    			SELECT pro.*, cur.code AS curCode, cur.title AS curTitle, cur.symbol_left AS curLeft, cur.symbol_right AS curRight
    			FROM
    			(
			    	SELECT p.*, c1.category_id AS `brandId`, c2.category_id AS `optionId`, c3.category_id AS `groupId`, c4.category_id AS `categoryId`, cmd.content_type_mapping_data_id AS cmdId, cm.content_type_mapping_id AS cmId, c.content_type_id AS cId, ca.content_addition_id AS caId,
			    	c1.title AS `brand`, c2.title AS `option`, c3.title AS `group`, c4.title AS `category`, cmd.title AS cmdTitle, cm.title AS cmTitle, c.type_name AS cTitle
			    	FROM {$this->conf['table']['product']} AS p
			    	JOIN {$this->conf['table']['content_addition']} AS ca
			    	ON ca.product_id = p.product_id
			    	JOIN {$this->conf['table']['content_type_mapping_data']} AS cmd
			    	ON cmd.content_type_mapping_data_id = ca.content_type_mapping_data_id
			    	JOIN {$this->conf['table']['content_type_mapping']} AS cm
			    	ON cm.content_type_mapping_id = cmd.content_type_mapping_id
			    	JOIN {$this->conf['table']['content_type']} AS c
			    	ON c.content_type_id = cm.content_type_id
			    	JOIN {$this->conf['table']['category']} AS c1
			    	ON c1.category_id = p.category_id
			    	JOIN {$this->conf['table']['category']} AS c2
			    	ON c2.category_id = c1.parent_id
			    	JOIN {$this->conf['table']['category']} AS c3
			    	ON c3.category_id = c2.parent_id
			    	JOIN {$this->conf['table']['category']} AS c4
			    	ON c4.category_id = c3.parent_id
			    	WHERE p.product_id = {$productId}
    			) AS pro
    			JOIN {$this->conf['table']['currency']} AS cur
    			ON cur.currency_id = pro.currency_id
	    	";
    	
    	$product =  $this->dbh->getAll($query);
    	
    	$optionId = $product['0']->optionId;
    	
    	$aOptList = array();
    	foreach ($product as $key => $value)
    	{
    		$aOptList[$value->cmId]['title'] = $value->cmTitle;
    		$aOptList[$value->cmId]['value'][$value->cmdId] = $value->cmdTitle;
    	}
    	
    	//echo '<pre>'; print_r($product); echo '</pre>';die;
    	
    	$output->product = $product['0'];
    	$output->pOptions = $aOptList;
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
    
    function objectToArray( $data )
    {
    	if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value)
	        {
	            $result[$key] = $this->objectToArray($value);
	        }
	        return $result;
	    }
	    return $data;
    }
}

?>
