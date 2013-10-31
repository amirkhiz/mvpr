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

    	$query = "
    			SELECT cm.content_type_mapping_id as cmId, cm.title AS cmTitle, cmd.content_type_mapping_data_id AS cmdID, cmd.title AS cmdTitle
    			FROM {$this->conf['table']['content_type']} as c
    			JOIN {$this->conf['table']['content_type_mapping']} AS cm
    			ON cm.content_type_id = c.content_type_id
    			JOIN {$this->conf['table']['content_type_mapping_data']} AS cmd
    			ON cmd.content_type_mapping_id = cm.content_type_mapping_id
				WHERE c.category_id = {$catId}
                ";
		$cmData =  $this->dbh->getAll($query);
		
		$aOptions = array();
		foreach ($cmData as $key => $value)
		{
			$aOptions[$value->cmId]['title'] = $value->cmTitle;
			$aOptions[$value->cmId]['ops'][$value->cmdID] = $value->cmdTitle;
		}
		//echo '<pre>'; print_r($aOptions); echo '</pre>';
		
		$output->aOptions = $aOptions;
		$output->isMulti = 1;
    	$output->data = $this->_renderTemplate($output, 'propertySelectbox.html');
    	
    }
    
    function search($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$whereCondition = '';
    	$havingCondition = '';
    	$fields = '';
    	$proSearchViewTemplate = 'search' . $this->req->get('frmViewType') . '.html';
    	
    	if ($this->req->get('frmCAddition'))
    	{
    		$cAddition = explode(',', $this->req->get('frmCAddition'));
	    	$whereCondition .= " AND ca.content_type_mapping_data_id IN (" . implode(',',$cAddition) . ')' ;
    	}
    	
    	if ($this->req->get('frmBrands'))
    	{
    		$aBrands = explode(',', $this->req->get('frmBrands'));
    		$whereCondition .= " AND p.category_id IN (" . implode(',',$aBrands) . ')' ;
    	}
    	
    	if ($this->req->get('frmGroups'))
    	{
    		$aGroups = explode(',', $this->req->get('frmGroups'));
    		$catTree = $this->catTree($aGroups);
    		
    		$aCatGroups = array();
    		foreach ($catTree as $value)
    		{
    			$aCatGroups[$value->BrandCatID] = $value->BrandCatID;
    		}
    		
    		$whereCondition .= " AND p.category_id IN (" . implode(',',$aCatGroups) . ')' ;
    	}
    	
    	if ($this->req->get('frmCategoryID'))
    	{
    		$categoryId = $this->req->get('frmCategoryID');
    		
    		$catTree = $this->catTree($categoryId);
    		
    		$aCat = array();
    		foreach ($catTree as $value){
    			$aCat[] = $value->BrandCatID;
    		}
    		
    		$whereCondition .= " AND p.category_id IN (" . implode(',',$aCat) . ")";
    	}
    	
    	if ($this->req->get('frmPrices'))
    	{
    		$aPrices = explode(';', $this->req->get('frmPrices'));
    		$minPrice = $aPrices['0'];
    		$maxPrice = $aPrices['1'];
    		$cur = $this->req->get('frmCur');
    		$havingCondition .= " AND (tlPrice BETWEEN {$minPrice} AND {$maxPrice})";
    	}
    	
    	$query = "
    			SELECT p.*, FLOOR(p.price * cu.value) AS tlPrice, cu.symbol_left AS curLeft, cu.symbol_right AS curRight {$fields}
				FROM {$this->conf['table']['content_addition']} as ca
				JOIN {$this->conf['table']['product']} AS p
				ON p.product_id = ca.product_id
				JOIN {$this->conf['table']['currency']} AS cu 
				ON p.currency_id = cu.currency_id
				WHERE 1
				{$whereCondition}
				GROUP BY p.product_id
				HAVING 1
				{$havingCondition}
			";

		$result = $this->dbh->getAll($query);
    	$result = $this->objectToArray($result);
    	
    	foreach ($result as $key => $value)
    	{
    		$proID = $value['product_id'];
    		$query = "
		    		SELECT pi.title AS proImgTitle
		    		FROM
		    		{$this->conf['table']['product_image']} AS pi
		    		WHERE pi.product_id = {$proID}
	    		";
    		$proImgs = $this->dbh->getRow($query);
    		$result[$key]['proImgTitle'] = $proImgs->proImgTitle;
    	}
    	
    	//echo '<pre>'; print_r($result); echo '</pre>';die;
    	
    	$output->products = $result;
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