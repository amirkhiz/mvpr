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



class CategoryAjaxProvider extends SGL_AjaxProvider2
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
    
	function catSearchBoxList($input, $output)
    {
    	$output->data = "frtg";
    }
    
	function laodCatList($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    	
    	$levelId = $this->req->get('levelId');
    	
    	$query = "SELECT * 
    				FROM {$this->conf['table']['category']} 
    				where level_id = '$levelId'  
    				order by order_id 
    				";
        $limit = $_SESSION['aPrefs']['resPerPage'];
            
        $pagerOptions = array(
                'mode'      => 'Sliding',
                'delta'     => 8,
                'perPage'   => 10000, 

            );
            $aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
       //echo "<pre>"; print_r($aPagedData); echo "</pre>";
       //exit;
            if (PEAR::isError($aPagedData)) {
                return false;
            }
            $output->aPagedData = $aPagedData;
            //echo "<pre>"; print_r($aPagedData); echo "</pre>";
            $output->totalItems = $aPagedData['totalItems'];
    	$output->levelId = $levelId;
    	$template = ($levelId == 2) ? 'loadGroupList.html' : 'loadBrandList.html';
    	$output->data = $this->_renderTemplate($output, $template);
    }
    
    function searchTxtList($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    	
    	$keys = $this->req->get('keys');
    	
    	//$output->keys = $keys;
    	
    	$aKeys = explode(" ",$keys);
    	$where = "";
    	foreach($aKeys as $key => $value){
    		$where .= " (p.title like '%$value%' or 
    					c4.title like '%$value%' or
    					c3.title like '%$value%' or
    					c2.title like '%$value%' or
    					c1.title like '%$value%') and ";
    	}
    	$where = substr($where,0,-4);
    	
    	$query = "SELECT 
    					p.*, p.title as pTitle, c4.title as c4Title, c3.title as c3Title, c2.title as c2Title, c1.title as c1Title 
    				FROM {$this->conf['table']['product']} as p
    				left join {$this->conf['table']['category']} as c4 on c4.category_id = p.category_id 
    				left join {$this->conf['table']['category']} as c3 on c4.parent_id = c3.category_id
    				left join {$this->conf['table']['category']} as c2 on c3.parent_id = c2.category_id
    				left join {$this->conf['table']['category']} as c1 on c2.parent_id = c1.category_id
    				where $where 
    				order by p.order_id
    				";
        $limit = $_SESSION['aPrefs']['resPerPage'];
            
        $pagerOptions = array(
                'mode'      => 'Sliding',
                'delta'     => 8,
                'perPage'   => 10000, 

            );
            $aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
       //echo "<pre>"; print_r($aPagedData); echo "</pre>";
       //exit;
            if (PEAR::isError($aPagedData)) {
                return false;
            }
            $output->aPagedData = $aPagedData;
            //echo "<pre>"; print_r($aPagedData); echo "</pre>";
            $output->totalItems = $aPagedData['totalItems'];
    
    	$output->data = $this->_renderTemplate($output, 'searchTxtList.html');
    }
    
	function searchBtnList($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    	
    	$categoryId = $this->req->get('categoryId');
    	
    	$output->catLevelId = $this->dbh->getOne("select level_id from {$this->conf['table']['category']} where category_id = '$categoryId'");
    	
    	if($categoryId != 0){
	    	$catRow = $this->dbh->getAll("select parent_id, level_id from {$this->conf['table']['category']} where parent_id = '$categoryId'");
	    	$parentId = $catRow[0]->parent_id;
    	}else{
    		$parentId = 0;
    	}
    	$parentLevelId = $catRow[0]->level_id;
    	
    	$query = " SELECT 
    					*
    				FROM {$this->conf['table']['category']}
    				where 
    					parent_id = '$parentId' 
    				order by order_id
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
            //echo "<pre>"; print_r($aPagedData); echo "</pre>";
            $output->totalItems = $aPagedData['totalItems'];
	        $output->parentLevelId = $parentLevelId;
	        $output->isBrand = ($aPagedData['data'][0]['level_id'] == 4) ? 1 : 0;

	        $output->levelId = $aPagedData['data'][0]['level_id'];
	        $output->query = $query;
	         
	   		$output->data = $this->_renderTemplate($output, 'searchBtnList.html');
	   		
    }
    
    function searchCategoryList($input, $output)
    {
    	
    	
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	$this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    	$searchVal = $this->req->get('searchVal');
    	$parentId = $this->req->get('parentId');
    	$levelId = $this->req->get('levelId');
    	
    	$output->pageTitle = $this->aLevelTitle[$levelId] . " list";
        $output->btnTitle = "New " . $this->aLevelTitle[$levelId];
        
        $output->parentTitle = $this->aLevelTitle[$levelId - 1];
        
        $output->searchCategory = "Search " . $this->aLevelTitle[$levelId - 1];
        
        $parentSearch = "";
        if(!empty($parentId)){
        	$parentSearch = " and c.parent_id = ' " . $parentId . " '";
        	$output->parentId = $parentId;
        }
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
    				where c.level_id = ".$levelId."
    				$parentSearch 
    				AND c.title like '%{$searchVal}%'  
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
            if(!empty($parentId)){
            	$output->parentBox = $aPagedData['data'][0]['pTitle'];
            }
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

}

?>