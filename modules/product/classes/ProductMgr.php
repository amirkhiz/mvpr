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
include_once SGL_MOD_DIR  . '/product/classes/ProductImage.php';
include_once SGL_CORE_DIR . '/Image.php';

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
	static protected $catLevel;
	
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
        $input->pImages     = $req->get('pImages');
        $input->pDelImg 	= $req->get('pDeletedImg');
        
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
    
	function display(&$output)
    {
    	$output->addJavascriptFile("js/jquery/plugins/jslider/jquery.slider.js");
    	
    	$output->roleId = SGL_Session::getRoleId() == false;
    }

    function _cmd_list(&$input, &$output)
    {
    	$this->checkRole();
        SGL::logMessage(null, PEAR_LOG_DEBUG);
		$usrId = SGL_Session::getUId();
        $productList = DB_DataObject::factory($this->conf['table']['product']);
        $user = DB_DataObject::factory($this->conf['table']['user']);
        $productList->joinAdd($user, 'LEFT', 'AS u', 'usr_id');
        $productList->whereAdd("product.usr_id = '$usrId'");
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
    	$this->checkRole();
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$output->template   = 'productEdit.html';
    	$output->action     = 'insert';
    	$output->pageTitle  = $this->pageTitle . ' :: Add';
    	$output->productLang = SGL_Translation::getFallbackLangID();
    	$output->isAdd = 1;
    	$this->edit_display($output);
    }
    
    function _cmd_insert(&$input, &$output)
    {
    	$this->checkRole();
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$usrId = SGL_Session::getUid();
    	//echo "<pre>"; print_r($input->product->prop); echo "<pre>";

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
    	$product->category_id	= $input->product->brands;
    	$product->last_updated 	= $product->date_created = SGL_Date::getTime(true);
    	$product->status	 	= 1;
    	$product->order_id 		= $maxItemOrder + 1;
    
    	$proImgIds = $this->getProImgArr($input->pImages, $productId);
    	$product->product_image_id = $proImgIds;
    	
    	$success = $product->insert();
    	 
    	//  insert options in content_addition table
    	
    	foreach($input->product->prop as $key => $value){
    		foreach($value as $vKey => $vValue){
	    		$cAddition = DB_DataObject::factory($this->conf['table']['content_addition']);
	    		$cAddition->content_addition_id				= $this->dbh->nextId('content_addition');
		    	$cAddition->product_id 						= $productId;
	    		$cAddition->content_type_mapping_id			= $key;
		    	$cAddition->value							= $vValue;
		    	$success = $cAddition->insert();
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
    	$this->checkRole();
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	
    	
    	$productId = $input->productId;
    	$output->isAdd = 0;
    	$output->template  = 'productEdit.html';
    	$output->action    = 'update';
    	$output->pageTitle = $this->pageTitle . ' :: Edit';
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	//  get product data
    	$product->get($productId);
    	if(SGL_Session::getUId() != $product->usr_id){
    		$options = array(
			    'moduleName' => 'default',
			);
			SGL_HTTP::redirect($options);
    	}
    	$query = "
			SELECT c1.title as brandCat , c2.title as propCat, c2.category_id as propId, c3.title as groupCat, c4.title as categoryCat
			FROM {$this->conf['table']['category']} as c1 
			left join {$this->conf['table']['category']} as c2 on c2.category_id = c1.parent_id 
			left join {$this->conf['table']['category']} as c3 on c3.category_id = c2.parent_id 
			left join {$this->conf['table']['category']} as c4 on c4.category_id = c3.parent_id 
			where c1.category_id = '{$product->category_id}'
		";
    	
		$cats = $this->dbh->getRow($query);
		
		
		$query = "select * from category where parent_id = '{$cats->propId}'";
    	$brands = $this->dbh->getAll($query);
    	$aBrands = array();
    	foreach($brands as $value)
    	{
    		$aBrands[$value->category_id] = $value->title;
    	}
    	
    	$query = "
			SELECT pi.product_image_id AS imgId, pi.title AS imgTitle
			FROM {$this->conf['table']['product']} AS p
			JOIN {$this->conf['table']['product_image']} AS pi
			ON pi.product_id = p.product_id
			";
		$aProImgs =  $this->dbh->getAll($query);
		$aImgs = array();
		foreach ($aProImgs as $key => $value)
		{
			$aImgs['img'][$value->imgId]	= $value->imgTitle;
		}
		
		$output->aProImgs	= $aImgs;
		$output->product 	= $product;
		$output->cats = $cats;
		$output->brands = $aBrands;
    	$this->edit_display($output);
		
    }
    
    function _cmd_update(&$input, &$output)
    {
    	$this->checkRole();
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	$productId = $input->product->product_id;
    	
    	$product = DB_DataObject::factory($this->conf['table']['product']);
    	$product->get($productId);
    	$product->setFrom($input->product);
    	$product->last_updated = SGL_Date::getTime(true);
    	$product->usr_id = SGL_Session::getUid();
    	
    	//Delete Product Properties For Update in content addition table
    	$cAddition = DB_DataObject::factory($this->conf['table']['content_addition']);
    	$cAddition->whereAdd('product_id = ' . $productId);
    	$cAddition->find();
    	while ($cAddition->fetch())
    	{
    		$cAddition->delete();
    	}
    	unset($cAddition);
    	
    	foreach($input->product->prop as $key => $value){
    		foreach($value as $vKey => $vValue){
	    		$cAddition = DB_DataObject::factory($this->conf['table']['content_addition']);
	    		$cAddition->content_addition_id				= $this->dbh->nextId('content_addition');
		    	$cAddition->product_id 						= $productId;
	    		$cAddition->content_type_mapping_id			= $key;
		    	$cAddition->value							= $vValue;
		    	$success = $cAddition->insert();
    		}
    	}
    	
    	//insert new images to product images table
    	if (!$input->pImages['error']['0'])
    	{
	    	$proImgIds = $this->getProImgArr($input->pImages, $productId);
	    	$product->product_image_id = $proImgIds;
    	}
    	
    	//delete images sent from form FROM product images
    	$aImgDel = explode(',', $input->pDelImg);
    	unset($aImgDel['0']);
    	
    	$proImage = new ProductImage();
    	$proImage->delete($aImgDel);
    
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
        
        $category->whereAdd("level_id = 1");
        $category->find();
        $aCategories = array();
        while($category->fetch()){
        	$aCategories[$category->category_id] = $category->title;
        }
        $output->aCategory= $aCategories;
        
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
    	$this->checkRole();
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
    			
    			//Delete Product Images from product_image table
    			$pImage = DB_DataObject::factory($this->conf['table']['product_image']);
    			$pImage->whereAdd('product_id = ' . $productId);
    			$pImage->find();
    			
    			$aImgs = array();
    			while ($pImage->fetch())
    			{
    				$aImgs[] = $pImage->product_image_id;
    			}
    			$proImage = new ProductImage();
    			$result = $proImage->delete($aImgs);
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
    	$output->template = "productSearch.html";
    	$categoryId = $input->categoryId;
    	
    	##############################################
    	#############  load breadcrambs ##############
    	##############################################
    	
    	$query = "
			SELECT c1.title as brandCat , c2.title as propCat, c2.category_id as propId, c3.title as groupCat, c4.title as categoryCat
			FROM {$this->conf['table']['category']} as c1 
			left join {$this->conf['table']['category']} as c2 on c2.category_id = c1.parent_id 
			left join {$this->conf['table']['category']} as c3 on c3.category_id = c2.parent_id 
			left join {$this->conf['table']['category']} as c4 on c4.category_id = c3.parent_id 
			where c1.category_id = '{$categoryId}'
		";
    	
		$cats = $this->dbh->getRow($query);
		
		##############################################
		################# load products ##############
		##############################################
    	
    	$query = "
	    	select p.*, pi.title as proImgTitle, cu.title as curTitle
			from {$this->conf['table']['product']} as p 
			left join {$this->conf['table']['product_image']} as pi on pi.product_id = p.product_id 
			left join {$this->conf['table']['currency']} as cu on cu.currency_id = p.currency_id
			where p.category_id in ({$categoryId}) order by p.date_created desc
    	";
    	
    	$limit = $_SESSION['aPrefs']['resPerPage'];
		$pagerOptions = array(
			'mode'      => 'Sliding',
			'delta'     => 8,
			'perPage'   => 1000,
			);
		$aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
		$output->aPagedData = $aPagedData;
		
		####################################
		#####    load currency types #######
		####################################
    	
		$currency = DB_DataObject::factory($this->conf['table']['currency']);
		$currency->find();
		
		$aCur = array();
		while ($currency->fetch())
		{
			$aCur[$currency->currency_id] = $currency->title;
		}
		$output->aCur = $aCur;
    	
		
		####################################
		#####     Min and Max prices #######
		####################################
		
		$query = "
	    	select min(p.price) as minPrice, max(p.price) as maxPrice
			from {$this->conf['table']['product']} as p 
			left join {$this->conf['table']['product_image']} as pi on pi.product_id = p.product_id 
			left join {$this->conf['table']['currency']} as cu on cu.currency_id = p.currency_id
			where p.category_id in ({$categoryId}) order by p.date_created desc
    	";
		
		$prices = $this->dbh->getRow($query);
		$output->minPrice = $prices->minPrice;
    	$output->maxPrice = $prices->maxPrice;
    	
    	
    	############################################
    	########### load product properties ########
    	############################################
    	
    	$query = "SELECT cm.*, ca.* FROM `content_type_mapping` as cm 
					join content_type as ct on ct.content_type_id = cm.content_type_id
					left join content_addition as ca on ca.content_type_mapping_id = cm.content_type_mapping_id
					where ct.category_id = '{$cats->propId}'
					group by ca.value";
    	
    	$limit = $_SESSION['aPrefs']['resPerPage'];
		$pagerOptions = array(
			'mode'      => 'Sliding',
			'delta'     => 8,
			'perPage'   => 1000,
			);
		$aProps = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
		
		$searchFields = array();
		foreach($aProps['data'] as $pKey => $pValue)
		{
			$ctmId = $pValue['content_type_mapping_id'];
			$searchFields[$ctmId]['title'] = $pValue['title'];
			$searchFields[$ctmId]['ops'][$pValue['value']] = $pValue['value'];
		}
		
		//echo "<pre>"; print_r($searchFields); echo "</pre>";
		$output->searchFields = $searchFields;
		
    	/*
    	foreach($props as $pKey => $pValue)
    	{
    		echo $pValue;
    		echo "<br />";
    	}
    	*/
    	
    	/*
    	$catTree = $this->catTree($categoryId);
    	$aCats = array();
    	$aOptions = array();
    	$aBrands = array();
    	foreach ($catTree as $key => $value)
    	{
    		#$aCats[$value->ParentCatID]['title'] = $value->ParentCatTitle;
    		#$aCats[$value->ParentCatID]['groups'][$value->GroupCatID]['title'] = $value->GroupCatTitle;
    		#$aCats[$value->ParentCatID]['groups'][$value->GroupCatID]['options'][$value->OptionCatID]['title'] = $value->OptionCatTitle;
    		#$aCats[$value->ParentCatID]['groups'][$value->GroupCatID]['options'][$value->OptionCatID]['brands'][$value->BrandCatID] = $value->BrandCatTitle; 
    		
    		$aGroups['group']['title'] = SGL_String::translate('Groups');;
    		$aGroups['group']['ops'][$value->GroupCatID] = $value->GroupCatTitle;
    		$aOptions[] = $value->OptionCatID;
    		$aBrands[] = $value->BrandCatID;
    	}
    	
    	$propertyRes = $this->proPropertySearch($aOptions);
    	
    	$searchFields = array();
    	$proCounter = array();
    	$aProductId = array();
		foreach ($propertyRes as $key => $value)
		{
			
			$proCounter[$value->cmdId]['title'] = $value->cmdTitle;
			$proCounter[$value->cmdId]['count']++ ;
			
			$searchFields[$value->cmId]['title'] = $value->cmTitle;
			$searchFields[$value->cmId]['ops'][$value->cmdId] = $value->cmdTitle . '(' .$proCounter[$value->cmdId]['count'] . ')';
			
			$aProductId[$value->product_id] = $value->product_id;
			
		}
		$query = "
				SELECT 
					pro.*, 
					FLOOR(minmax.minPrice) AS minPrice, FLOOR(minmax.maxPrice) AS maxPrice, 
					cat.title AS catTitle
				FROM 
				(
				     SELECT p.*, (p.price * cu.value) AS tlPrice
				     FROM {$this->conf['table']['product']} AS p
				     JOIN {$this->conf['table']['currency']} AS cu 
				     ON p.currency_id = cu.currency_id
				     WHERE p.product_id IN (" . implode(',',$aProductId) . ") AND p.category_id IN (" . implode(',',$aBrands) . ")
				) AS pro,
				(
				     SELECT MIN(pr.price * cur.value) AS minPrice, MAX(pr.price * cur.value) AS maxPrice
				     FROM {$this->conf['table']['product']} AS pr
				     JOIN {$this->conf['table']['currency']} AS cur
				     ON pr.currency_id = cur.currency_id
				     WHERE pr.product_id IN (" . implode(',',$aProductId) . ") AND pr.category_id IN (" . implode(',',$aBrands) . ")
				) AS minmax,
				{$this->conf['table']['category']} AS cat
				WHERE cat.category_id = pro.category_id
				ORDER BY pro.product_id
			";
		
		$limit = $_SESSION['aPrefs']['resPerPage'];
		$pagerOptions = array(
			'mode'      => 'Sliding',
			'delta'     => 8,
			'perPage'   => 1000,
			
			);
		$aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
			if (PEAR::isError($aPagedData)) {
			$options = array(
			    'moduleName' => 'default',
			);
			SGL_HTTP::redirect($options);
		}
		
		$brandCounter = array();
		$aBrand = array();
		foreach ($aPagedData['data'] as $key => $value)
		{
			$brandCounter[$value['category_id']]['title'] = $value['catTitle'];
			$brandCounter[$value['category_id']]['count']++ ;
				
			$aBrand['10']['title'] = SGL_String::translate('Brands');
			$aBrand['10']['ops'][$value['category_id']] = $value['catTitle'] . '(' .$brandCounter[$value['category_id']]['count'] . ')';
			
			$proID = $value['product_id'];
			$query = "
					SELECT pi.title AS proImgTitle
					FROM
						{$this->conf['table']['product_image']} AS pi
					WHERE pi.product_id = {$proID}
				";
			$proImgs = $this->dbh->getRow($query);
			$aPagedData['data'][$key]['proImgTitle'] = $proImgs->proImgTitle;
		}
		//echo '<pre>'; print_r($aPagedData['data']); echo '</pre>';die;
		
		array_unshift($searchFields, $aBrand['10']);
		
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
    	
    	$output->pageTitle 		= $this->pageTitle . ' :: Reorder';
    	$output->template  		= 'productSearch.html';
    	$output->catId			= $input->categoryId;
    	$output->products 		= $productcs;
    	$output->minPrice 		= $aPagedData['data']['0']['minPrice'];
		$output->maxPrice 		= $aPagedData['data']['0']['maxPrice'];
    	$output->aCur 			= $aCur;
    	if (self::$catLevel === '1'){
    		$output->searchFields = $aGroups;
    	}else{
    		$output->searchFields = $searchFields;
    	}
    	*/
    	//echo '<pre>'; print_r($output->searchFields); echo '</pre>';die;
    }
    
    function _cmd_view(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$output->pageTitle = $this->pageTitle . ' :: View';
    	$output->template  = 'productView.html';
    	
    	$productId = $input->productId;
    	
    	$query = "
    	SELECT p.*, cu.*, c1.title as brandTitle , c2.title as propCat, c2.category_id as propId, c3.title as groupCat, c4.title as categoryCat
    	FROM {$this->conf['table']['category']} as c1
    	left join {$this->conf['table']['category']} as c2 on c2.category_id = c1.parent_id
    	left join {$this->conf['table']['category']} as c3 on c3.category_id = c2.parent_id
    	left join {$this->conf['table']['category']} as c4 on c4.category_id = c3.parent_id
    	left join product as p on p.category_id = c1.category_id 
    	left join currency as cu on cu.currency_id = p.currency_id
    	where p.product_id = '$productId'
    	";
    	 
    	$cats = $this->dbh->getRow($query);
    	
    	$output->cats = $cats;
    	/*
    	$query = "
    				SELECT p.*, p.title as productTitle, cm.*, ca.*, cu.title as curTitle
					FROM `content_type_mapping` as cm 
					join content_type as ct on ct.content_type_id = cm.content_type_id left 
					join content_addition as ca on ca.content_type_mapping_id = cm.content_type_mapping_id 
					product as p on p.product_id = ca.product_id 
					join currency as cu on cu.currency_id = p.currency_id 
					where p.product_id = {$productId}
	    	";
    	
    	$query = "select p.* 
    				from product as p
    				left join content_addition as ca on ca.product_id = p.product_id 
    				left join content_type_mapping as cm on cm.content_type_mapping_id = ca.content_type_mapping_id 
    				left join content_type as ct on ct.content_type_id = cm.content_type_id
    				where p.product_id = {$productId}
    	";
    	*/
    	/* SELECT pro.*, cur.code AS curCode, cur.title AS curTitle, cur.symbol_left AS curLeft, cur.symbol_right AS curRight
    	FROM
    	(
    			SELECT p.*, 
    			c1.category_id AS `brandId`, 
    			c2.category_id AS `optionId`, 
    			c3.category_id AS `groupId`, 
    			c4.category_id AS `categoryId`, 
    			cmd.content_type_mapping_data_id AS cmdId, 
    			cm.content_type_mapping_id AS cmId, 
    			c.content_type_id AS cId, 
    			ca.content_addition_id AS caId,
    			c1.title AS `brand`, 
    			c2.title AS `option`, 
    			c3.title AS `group`, 
    			c4.title AS `category`, 
    			cmd.title AS cmdTitle, 
    			cm.title AS cmTitle, 
    			c.type_name AS cTitle
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
    			WHERE p.product_id = {
    		$productId}
    		) AS pro
    		JOIN {$this->conf['table']['currency']} AS cur
    		ON cur.currency_id = pro.currency_id */
    		 
    	$query = "SELECT cm.*, ca.* FROM `content_type_mapping` as cm
	    	join content_type as ct on ct.content_type_id = cm.content_type_id
	    	left join content_addition as ca on ca.content_type_mapping_id = cm.content_type_mapping_id
	    	where ct.category_id = '{$cats->propId}' and product_id = '$productId'  
	    	";
    	 
    	$limit = $_SESSION['aPrefs']['resPerPage'];
    	$pagerOptions = array(
    	'mode'      => 'Sliding',
    	'delta'     => 8,
    	'perPage'   => 1000,
    	);
    	$aProps = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
    	
    	
    	$searchFields = array();
    	foreach($aProps['data'] as $pKey => $pValue)
    	{
    		$ctmId = $pValue['content_type_mapping_id'];
    		$searchFields[$ctmId]['title'] = $pValue['title'];
    		$searchFields[$ctmId]['value'][$pValue['value']] = $pValue['value'];
    	}
    	
    	//echo "<pre>"; print_r($searchFields); echo "</pre>";
    	$output->searchFields = $searchFields;
    	
    	$product =  $this->dbh->getAll($query);
    	
    	$aOptList = array();
    	foreach ($product as $key => $value)
    	{
    		$aOptList[$value->cmId]['title'] = $value->cmTitle;
    		$aOptList[$value->cmId]['value'][$value->cmdId] = $value->cmdTitle;
    		
    	}
    	
    	$productId = intval($productId);
    	if($productId){
	    	$query = "
			    	SELECT pi.title AS proImgTitle
			    	FROM
			    	{$this->conf['table']['product_image']} AS pi
			    	WHERE pi.product_id = {$productId}
	    		";
			
	    	$aProImgs = $this->objectToArray($this->dbh->getAll($query));
	    	
	    	//echo '<pre>';print_r($aProImgs);echo '</pre>';die;
	    	
	    	$output->product 	= $product['0'];
	    	$output->pOptions 	= $aOptList;
	    	$output->proImages 	= $aProImgs;
    	}else{
    		$options = array(
		    	'moduleName' => 'default',
			);
			SGL_HTTP::redirect($options);
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
    
    function objectToArray( $data )
    {
    	if (is_array($data) || is_object($data))
    	//if (count($data))
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
    
    function proPropertySearch($categoryId)
    {
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
		    	WHERE c.category_id IN (" . implode(',', $categoryId) . ")
    		";
    	 
    	return $this->dbh->getAll($query);
    }
    
    function catLevel($categoryId)
    {
    	$category = DB_DataObject::factory($this->conf['table']['category']);
    	$category->selectAdd();
    	$category->selectAdd('level_id');
    	$category->whereAdd('category_id = ' . $categoryId);
    	$category->find(true);
    	self::$catLevel = $category->level_id;
    	return $category->level_id;
    }
    
    function catTree($categoryId)
    {
    	$catLevel = $this->catLevel($categoryId);
    	
    	$catConId = $catLevel + 1;
    	$catCondition = "c{$catConId}.parent_id = {$categoryId}";
    	if ($catLevel == 4){
    		$catCondition = "c{$catLevel}.category_id = {$categoryId}";
    	}
    	 
    	$query = "
	    	SELECT
		    	c4.category_id AS BrandCatID, c4.title AS BrandCatTitle,
		    	c3.category_id AS OptionCatID, c3.title AS OptionCatTitle,
		    	c2.category_id AS GroupCatID, c2.title AS GroupCatTitle,
		    	c1.category_id AS ParentCatID, c1.title AS ParentCatTitle
	    	FROM
		    	{$this->conf['table']['category']} AS c4,
		    	{$this->conf['table']['category']} AS c3,
		    	{$this->conf['table']['category']} AS c2,
		    	{$this->conf['table']['category']} AS c1
	    	WHERE
		    	{$catCondition}
		    	AND c3.category_id = c4.parent_id
		    	AND c2.category_id = c3.parent_id
		    	AND c1.category_id = c2.parent_id
    		";
    	$result = $this->dbh->getAll($query);
    	
    	return $result;
    }
    
    function getProImgArr($aImg, $productId)
    {
    	$aImages = array();
    	foreach ($aImg['name'] as $key => $value)
    	{
    		$aImages[$key]['name'] 		= $aImg['name'][$key];
    		$aImages[$key]['type'] 		= $aImg['type'][$key];
    		$aImages[$key]['tmp_name'] 	= $aImg['tmp_name'][$key];
    		$aImages[$key]['error'] 	= $aImg['error'][$key];
    		$aImages[$key]['size'] 		= $aImg['size'][$key];
    	}
    	 
    	//insert images
    	$proImage = new ProductImage();
    	$result = $proImage->create($aImages, $productId);
    	 
    	$proImgIds = implode(',', $result);
    	return  $proImgIds;
    }
    
    function checkRole(){
    	if(!SGL_Session::getRoleId()){
    		$options = array(
		    	'moduleName' => 'default',
			);
			SGL_HTTP::redirect($options);
    	}
    }
}

?>
