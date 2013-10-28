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
	        $output->levelId = $aPagedData['data'][0]['level_id'];
	        $output->query = $query; 
	   		$output->data = $this->_renderTemplate($output, 'searchBtnList.html');
	   		
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

}

?>