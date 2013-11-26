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
        	'productslist'	=> array('productslist'),
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
        $input->productDesc = $req->get('productDesc', $allowTags = true);
        
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
    
    function _cmd_productslist(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
		$usrId = SGL_Session::getUId();
		$orgId = SGL_Session::getOrganisationId();
        $query = "SELECT p . * , c.title AS cTitle, u.username, cu.title as cuTitle
			FROM {$this->conf['table']['product']} AS p
			JOIN {$this->conf['table']['user']} AS u ON u.usr_id = p.usr_id 
			JOIN {$this->conf['table']['category']} as c on c.category_id = p.category_id 
			JOIN {$this->conf['table']['currency']} as cu on cu.currency_id = p.currency_id 
			WHERE u.organisation_id =  '$orgId'";
    	
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
        $output->results = $aPagedData['data'];
    }
    
    function _cmd_list(&$input, &$output)
    {
    	$this->checkRole();
        SGL::logMessage(null, PEAR_LOG_DEBUG);
		$usrId = SGL_Session::getUId();
		
        $query = "SELECT p . * , c.title AS cTitle, u.username, cu.title as cuTitle
			FROM {$this->conf['table']['product']} AS p
			JOIN {$this->conf['table']['user']} AS u ON u.usr_id = p.usr_id 
			JOIN {$this->conf['table']['category']} as c on c.category_id = p.category_id 
			JOIN {$this->conf['table']['currency']} as cu on cu.currency_id = p.currency_id 
			WHERE p.usr_id =  '$usrId'";
    	
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
            
        $output->results = $aPagedData['data'];
        
        //$output->brand = $this->dbh->getOne("select * from {$this->conf['table']['category']} where category_id = ");
        #echo '<pre>';print_r($aproducts);echo '</pre>';die;
    
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
    	$product->description = $input->productDesc;
    
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
    	$output->wysiwyg = true;
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
			where p.product_id = '$productId'
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
    	$product->description = $input->productDesc;
    	$product->category_id	= $input->product->brands;
    	
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
    
    function findChilds($catId)
    {
    	return $cats = $this->dbh->getOne("select group_concat(category_id) from {$this->conf['table']['category']} where parent_id in($catId)");
    }
    
    function findCats($catId, $levelId)
    {
    	$aCats = array();
    	if($levelId == 4){
    		$aCats['chils'] = "";
    		$aCats['props'] = $this->dbh->getOne("select parent_id from {$this->conf['table']['category']} where category_id = '$catId'");
    		$aCats['cats'] = $catId;
    		return $aCats;
    	}
    	
    	if($levelId == 3){
    		$aCats['childs'] = $this->getChildsArray($catId);
    		$aCats['props'] = $catId;
    		$aCats['cats'] = $this->findChilds($catId);
    		return $aCats;
    	}

    	if($levelId == 2){
    		$aCats['childs'] = $this->getChildsArray($catId);
    		$cat = $this->findChilds($catId);
    		$aCats['props'] = $cat;
    		$aCats['cats'] = $this->findChilds($cat);
    		return $aCats;
    	}
    	
    	if($levelId == 1){
    		$aCats['childs'] = $this->getChildsArray($catId);
    		$cat = $this->findChilds($catId);
    		$cat = $this->findChilds($cat);
    		$aCats['props'] = $cat;
    		$aCats['cats'] = $this->findChilds($cat);
    		return $aCats;
    	}
    }
    
    function getChildsArray($catId){
    	$cats = $this->dbh->getAll("select category_id, title from {$this->conf['table']['category']} where parent_id = '$catId'");
    	$aCats = array();
    	foreach($cats as $key => $value)
    	{
    		$aCats[$value->category_id] = $value->title;
    	}
    	return $aCats;
    }
    
    
    function _cmd_search(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$output->template = "productSearch.html";
    	$categoryId = $input->categoryId;
    	
    	##############################################
    	#############  load breadcrambs ##############
    	##############################################
    	if($categoryId){
    		
	    	$query = "
				SELECT 
				c1.title as brandTitle,  c1.category_id as brandId , c1.level_id, 
				c2.title as propTitle,   c2.category_id as propId, 
				c3.title as groupTitle,  c3.category_id as groupId,
				c4.title as parentTitle, c4.category_id as parentId
				FROM {$this->conf['table']['category']} as c1 
				left join {$this->conf['table']['category']} as c2 on c2.category_id = c1.parent_id 
				left join {$this->conf['table']['category']} as c3 on c3.category_id = c2.parent_id 
				left join {$this->conf['table']['category']} as c4 on c4.category_id = c3.parent_id 
				where c1.category_id = '{$categoryId}'
			";
	    	
			$cats = $this->dbh->getRow($query);
			$output->cats = $cats;
			$aCats = $this->findCats($categoryId, $cats->level_id);
			$categoryId = $aCats['cats'];
			$props = $aCats['props'];
			if(!empty($cats->level_id)){
				$output->childs = $aCats['childs'];
			}
			
			
			//echo "<pre>"; print_r($cats); echo "</pre>";
			
			##############################################
			################# load products ##############
			##############################################
	    	
	    	$query = "
		    	select p.*, REPLACE(p.title, ' ', '-') as seoTitle, pi.title as proImgTitle, cu.title as curTitle, c.title as brand
				from {$this->conf['table']['product']} as p 
				left join {$this->conf['table']['product_image']} as pi on pi.product_id = p.product_id 
				left join {$this->conf['table']['currency']} as cu on cu.currency_id = p.currency_id
				left join {$this->conf['table']['category']} as c on c.category_id = p.category_id 
				where p.category_id in ({$categoryId}) group by p.product_id order by p.date_created desc
	    	";
	    	
	    	$limit = $_SESSION['aPrefs']['resPerPage'];
			$pagerOptions = array(
				'mode'      => 'Sliding',
				'delta'     => 8,
				'perPage'   => 1000,
				);
			$aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
			//echo "<pre>"; print_r($aPagedData); echo "</pre>";
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
						where ct.category_id in ({$props}) 
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
			$output->catId = $categoryId;
    	}
    	
    }
    
    function _cmd_view(&$input, &$output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
		    	
    	$output->pageTitle = $this->pageTitle . ' :: View';
    	$output->template  = 'productView.html';
    	
    	$productId = $input->productId;
    	
    	$query = "
    	SELECT p.*, cu.*, 
    	c1.title as brandTitle,  c1.category_id as brandId,
		c2.title as propTitle,   c2.category_id as propId, 
		c3.title as groupTitle,  c3.category_id as groupId,
		c4.title as parentTitle, c4.category_id as parentId
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
    	
    	$query = "SELECT cm.*, ca.*
    		FROM `content_type_mapping` as cm
	    	join content_type as ct on ct.content_type_id = cm.content_type_id
	    	left join content_addition as ca on ca.content_type_mapping_id = cm.content_type_mapping_id
	    	where ct.category_id = '{$cats->propId}' and ca.product_id = '$productId'
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
			    	WHERE pi.product_id = '{$productId}'
	    		";
			
	    	$aProImgs = $this->objectToArray($this->dbh->getAll($query));
	    	
	    	//echo '<pre>';print_r($aProImgs);echo '</pre>';die;
	    	
	    	$query = "select p.*, cu.title as cuTitle
	    				from 
	    				product as p
	    				left join category as c on c.category_id = p.category_id
	    				left join currency as cu on cu.currency_id = p.currency_id 
	    				where p.product_id = '{$productId}'    
	    				";
	    	
	    	$product = $this->dbh->getRow($query);
	    	//print_r($product);
	    	
	    	$output->product 	= $product;
	    	
	    	if(SGL_Session::getRoleId() && $product->dprice){
	    		$output->isRoleDprice = 1;
	    	}else{
	    		$output->isRoleDprice = 0;
	    	}
	    	
	    	$aDim = array(
        		1 => 'Centimeter',
        		2 => 'Millimeter',
        		3 => 'Inch',
        	);
        	
        	$aWeight = array(
        		1 => 'Kilogram',
        		2 => 'Gram',
        		3 => 'Pound',
        		4 => 'Ounce',
        	);
        	
	    	$demi = "";
	    	$demi .= ($product->dim_l != "") ? $product->dim_l . " X " : "";
	    	$demi .= ($product->dim_w != "") ? $product->dim_w . " X " : "";
	    	$demi .= ($product->dim_h != "") ? $product->dim_h . " "  : "";
	    	$output->demi = $demi . $aDim[$product->dim_id];
	    	
	    	$output->weight = ($product->weight != "") ? $product->weight . " " . $aWeight[$product->weight_id] : "";
	    	$output->currKeys = ($product->meta_keyword != "") ? ", " . $product->meta_keyword : "";
	    	$output->currDesc = ($product->meta_desc != "") ? ":: " . $product->meta_desc : "";
	    	
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
    
    function checkRole($roles = array(0)){
    	if(!SGL_Session::getRoleId()){
    		$options = array(
		    	'moduleName' => 'default',
			);
			SGL_HTTP::redirect($options);
    	}
    	/*
    	if(!(in_array($roles, SGL_Session::getRoleId()))){
    		$options = array(
		    	'moduleName' => 'default',
			);
			SGL_HTTP::redirect($options);
    	}
    	*/
    
    }
}

?>
