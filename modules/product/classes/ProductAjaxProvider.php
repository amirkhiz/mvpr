<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | All rights reserved.                                                      |
// |                                                                           |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Seagull 1.1                                                               |
// +---------------------------------------------------------------------------+
// | CategoryAjaxProvider.php                                                   |
// +---------------------------------------------------------------------------+
// | Authors:   Sina Saderi <sina.saderi@gmail.com>                            |
// +---------------------------------------------------------------------------+


/**
 * Wrapper for various ajax methods.
 *
 * @package sms
 */

require_once SGL_CORE_DIR . '/Delegator.php';
require_once SGL_CORE_DIR . '/AjaxProvider2.php';
include_once 'DB/DataObject.php';



class ProductAjaxProvider extends SGL_AjaxProvider2
{

    public $req;
    public $responseFormat;
    public $aMsg;
    public $aLevelTitle = array(); 

    /**
     *Constructor of SmsAjaxProvider class.
     */
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        parent::__construct();

        $this->req = &SGL_Registry::singleton()->getRequest();

        $this->responseFormat = SGL_RESPONSEFORMAT_HTML;
        
        $this->aLevelTitle = array(
        				1 => "Category",
        				2 => "Product group",
        				3 => "Product property",
        				4 => "Brand"
        );
    }

	function _isOwner($requestedUsrId)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        return true;
    }
    
    function serachCategoryList($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    	$searchVal = $this->req->get('searchVal');
    	$parentVal = $this->req->get('parentVal');
    	$levelId = $this->req->get('levelId');
    	
    	$query = " SELECT 
    					c.*, m.title as pTitle 
    				FROM `category` as c left join category as m on c.parent_id = m.category_id
    				where 
    					c.level_id = '$levelId' and 
    					c.title like '%$searchVal%' and 
    					m.title like '%$parentVal%'  
    				
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
        $output->parentTitle = $this->aLevelTitle[$levelId - 1];
        $output->levelId = $levelId;
        $output->parentLevel = $levelId - 1;
        if($levelId != 4){
        	$output->childLevel = $levelId + 1;
    	}
        
   		$output->data = $this->_renderTemplate($output, 'admin_categoryList.html');
   		
    }

    function getTest($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    	$output->data = $this->req->get('testId');
    }
    
    function getCat($input, $output)
    {
    	
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    	
    	$catLevel 	= $this->req->get('frmCatLevel');
    	$parentId 	= $this->req->get('frmParentId');
    	$title 		= $this->req->get('frmTitle');
    	//echo 'asdasdas';
    	
    	
    	$category = DB_DataObject::factory($this->conf['table']['category']);
    	
    	$category->whereAdd("level_id = " . $catLevel);
    	$category->whereAdd("parent_id = " . $parentId);
    	$category->find();
    	$aCat = array();
    	while($category->fetch()){
    		$aCat[$category->category_id] = $category->title;
    	}
    	
    	$output->aCats = $aCat;
    	$output->title = $title;
    	$output->isMulti = 0;
    	$output->data = $this->_renderTemplate($output, 'propertySelectbox.html');
    	//echo json_encode($aBrands);
    	 
    	 
    }
    
    function getProperty($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	 
    	$catId = $this->req->get('frmCatId');
    	$productId = $this->req->get('frmProductId');
    	
    	$typeId = $this->dbh->getOne("select content_type_id from {$this->conf['table']['content_type']} where category_id = $catId");
    	
    	//$output->template  = 'type/typeEdit.html';
        $output->inputTypes = $this->da->inputTypeArray();

        $type = DB_DataObject::factory($this->conf['table']['content_type']);
        $type->get($typeId);
        $output->type = $type;
        unset($type);
        
        $type = DB_DataObject::factory($this->conf['table']['content_type_mapping']);
        $type->whereAdd("content_type_id='".$typeId."'");
        $type->orderBy("tag_order");
        $type->find();
		$aTypes = array();
		while($type->fetch())
		{
			$type->options = unserialize($type->options); 
			$type->options['content_type_mapping_id'] = $type->content_type_mapping_id;
			$type->options = serialize($type->options);
			//echo "<pre>"; print_r($type->options); echo "</pre>";
            $aTypes[] = clone($type);
            
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
        
        if($productId){
        	$output->productId  = $productId;
        	$addition = DB_DataObject::factory($this->conf['table']['content_addition']);
	        $addition->whereAdd("product_id = '". $productId."'");
	        $addition->find();
	        $aAddition = array();
	        while($addition->fetch()){
	        	$aAddition[] = clone($addition);
	        }
	        //echo "<pre>"; print_r($aAddition); echo "</pre>";
	        $output->addition = $aAddition;
        }
        $output->data = $this->_renderTemplate($output, 'typeLoad.html');
    	
    }
    
    function ajaxSearch($input, $output)
    {
    	$keywords = $this->req->get('keywords');
    	$usrId = SGL_Session::getUId();
		
    	$aFields = array("p.title", "c.title", "p.model", "cu.title");
		
		$aWords = explode(" ", $keywords);

		$searchQuery = "";
		
		foreach($aWords as $wKey => $wValue)
		{
			$searchQuery .= " AND ( ";
			foreach($aFields as $fKey => $fValue)
			{
				$searchQuery .= $fValue . " like '%$wValue%' OR ";
			}
			$searchQuery = substr($searchQuery, 0, -3); 
			$searchQuery .= " )";
		}
    	
    	$query = "SELECT p . * , c.title AS cTitle, u.username, cu.title as cuTitle
			FROM {$this->conf['table']['product']} AS p
			JOIN {$this->conf['table']['user']} AS u ON u.usr_id = p.usr_id 
			JOIN {$this->conf['table']['category']} as c on c.category_id = p.category_id 
			JOIN {$this->conf['table']['currency']} as cu on cu.currency_id = p.currency_id 
			WHERE p.usr_id =  '$usrId' $searchQuery";
    	
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
        
    	
    	/*
    	
        $productList = DB_DataObject::factory($this->conf['table']['product']);
        $user = DB_DataObject::factory($this->conf['table']['user']);
        $category = DB_DataObject::factory($this->conf['table']['category']);
        $productList->joinAdd($user, 'LEFT', 'AS u', 'usr_id');
        $productList->joinAdd($category, 'left', 'as c', 'category_id');
        $productList->selectAdd("product.title as ptitle");
        $productList->whereAdd("product.usr_id = '$usrId' AND ptitle like '%$keywords%'");
        
        $productList->orderBy('product.order_id');
        $result = $productList->find();
        $aproducts  = array();
        if ($result > 0) {
            while ($productList->fetch()) {
                $productList->title = $productList->title;
                $aproducts[]        = clone($productList);
            }
        }
        $output->results = $this->objectToArray($aproducts);
        */
        $output->data = $this->_renderTemplate($output, 'productList.html');
    }
    
    function search($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$cats = $this->req->get('frmCategoryID');
    	$whereCondition = '';
    	$havingCondition = '';
    	$fields = '';
    	$proSearchViewTemplate = 'search' . $this->req->get('frmViewType') . '.html';
    	
    	if ($this->req->get('frmCAddition'))
    	{
    		$additionQuery = "";
    		$addition = $this->req->get('frmCAddition');
    		$addition = substr($addition, 0, -3);
    		$addition = str_replace("~~~", "/", $addition);
    		$addition = explode("|||", $addition);
    		//print_r($addition);
    		foreach($addition as $key => $value)
    		{
    			if($key > 0){
    				$value = substr($value, 6);
    			}
    			$aValue = explode("~~||~~",$value);
    			$count = count($aValue) - 1;
    			$id = array_pop($aValue);
    			$valueStr = "";
    			foreach($aValue as $key => $value)
    			{
    				$valueStr .=  " ca.value = '$value' OR ";
    			}
    			$valueStr = substr($valueStr, 0, -3);
    			$additionQuery .= " (ca.content_type_mapping_id = '$id' and ( $valueStr ) ) or ";
    		}
    		$additionQuery = substr($additionQuery, 0, -3);
    	}
    		
    		
    	
    	if ($this->req->get('frmPrices'))
    	{
    		$aPrices = explode(';', $this->req->get('frmPrices'));
    		$minPrice = $aPrices['0'];
    		$maxPrice = $aPrices['1'];
    		$havingCondition .= " (price BETWEEN '{$minPrice}' AND '{$maxPrice}')";
    		
    	}
    	
    	$cur = $this->req->get('frmCur');
    	if($cur){
    		$havingCondition .= " AND (p.currency_id = '$cur')";
    	}
    	
    	
    	$query = "SELECT p.*, p.title as productTitle, REPLACE(p.title, ' ', '-') as seoTitle, pi.title as proImgTitle, cm.*, ca.*, cu.title as curTitle, c.title as brand
					FROM `content_type_mapping` as cm 
					join content_type as ct on ct.content_type_id = cm.content_type_id left 
					join content_addition as ca on ca.content_type_mapping_id = cm.content_type_mapping_id 
					join product as p on p.product_id = ca.product_id 
					left join {$this->conf['table']['product_image']} as pi on pi.product_id = p.product_id 
					join currency as cu on cu.currency_id = p.currency_id 
					join category as c on c.category_id = p.category_id
					
					where p.category_id in ($cats) AND ";
    	if($additionQuery != ""){
    		$query .= $additionQuery;
    	}
    	if($additionQuery != "" && $havingCondition != ""){
    		$query .=  " AND " . $havingCondition;
    	}else if($havingCondition != ""){
    		$query .=  $havingCondition;
    	}
    	
    	if($additionQuery == "" && $havingCondition == ""){
    		$query = substr($query, 0, -4);
    	}
    	$query .= " group by p.product_id order by p.date_created desc";
    	 
    	$limit = $_SESSION['aPrefs']['resPerPage'];
		$pagerOptions = array(
			'mode'      => 'Sliding',
			'delta'     => 8,
			'perPage'   => 1000,
			);
		$aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
		$output->aPagedData = $aPagedData;
    	$output->data = $this->_renderTemplate($output, $proSearchViewTemplate);
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
    
    function catLevel($categoryId)
    {
    	$category = DB_DataObject::factory($this->conf['table']['category']);
    	$category->selectAdd();
    	$category->selectAdd('level_id');
    	
    	if (is_array($categoryId))
    		$category->whereAdd('category_id IN (' . implode(',',$categoryId) . ')');
    	else 
    		$category->whereAdd('category_id = ' . $categoryId);
    	
    	$category->find(true);
    	return $category->level_id;
    }
    
    function catTree($categoryId)
    {
    	$aCategoryId = array();
    	if (!is_array($categoryId)){
    		$aCategoryId[] = $categoryId;
    		$categoryId = $aCategoryId;
    	}
    	
    	$catLevel = $this->catLevel($categoryId);

    	$catConId = $catLevel + 1;
    	$catCondition = "c{$catConId}.parent_id IN (" . implode(',',$categoryId) . ")";
    	if ($catLevel == 4){
    		$catCondition = "c{$catLevel}.category_id IN (" . implode(',',$categoryId) . ")";
    	}
    	//echo '<pre>'; print_r($categoryId); echo '</pre>';
    
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

}

?>