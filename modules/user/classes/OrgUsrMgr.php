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
// | RegisterMgr.php                                                           |
// +---------------------------------------------------------------------------+
// | Author: Demian Turner <demian@phpkitchen.com>                             |
// +---------------------------------------------------------------------------+
// $Id: RegisterMgr.php,v 1.38 2005/06/05 23:14:43 demian Exp $

require_once SGL_MOD_DIR . '/user/classes/LoginMgr.php';
require_once SGL_MOD_DIR . '/user/classes/UserDAO.php';
require_once SGL_CORE_DIR . '/Observer.php';
require_once SGL_CORE_DIR . '/Emailer.php';
require_once 'Validate.php';
require_once 'DB/DataObject.php';

/**
 * Manages User objects.
 *
 * @package User
 * @author  Demian Turner <demian@phpkitchen.com>
 * @version $Revision: 1.38 $
 */
class OrgUsrMgr extends SGL_Manager
{
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'User List';
        $this->template     = 'userManager.html';
        $this->da           =  UserDAO::singleton();

        $this->_aActionsMapping =  array(
            'list'       => array('list'),
        );
    }

    function validate(&$req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';

        //  get referer details if present
        $input->redir = $req->get('redir');

       
        $aErrors = array();

        //  if errors have occured
        if (is_array($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->error = $aErrors;
            $input->template = 'userAdd.html';
            $this->validated = false;
        }

    }

    function display($output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

    }

    function _cmd_list($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$usrId = SGL_Session::getUid();
    	$usr = DB_DataObject::factory($this->conf['table']['user']);
    	$usr->whereAdd('usr_id = ' . $usrId);
    	$usr->find(true);
    	$usrOrgId = $usr->organisation_id;
    	
    	//echo '<pre>';print_r($usr->organisation_id);echo '</pre>';die;
    	
    	$output->pageTitle = $this->pageTitle . ' :: Browse';
    	$allowedSortFields = array('usr_id','username','is_acct_active');
    	if (      !empty($input->sortBy)
    			&& !empty($input->sortOrder)
    			&& in_array($input->sortBy, $allowedSortFields)) 
    	{
    		$orderBy_query = ' ORDER BY ' . $input->sortBy . ' ' . $input->sortOrder ;
    	} else {
    		$orderBy_query = ' ORDER BY u.usr_id ASC ';
    	}
    
    	if ($this->conf['OrgMgr']['enabled']) {
    		$query = "
	    		SELECT  u.*, o.name AS org_name, r.name AS role_name
	    		FROM    {$this->conf['table']['user']} u
	    		LEFT JOIN   {$this->conf['table']['organisation']} o
	    		ON u.organisation_id = o.organisation_id
	    		JOIN {$this->conf['table']['role']} r
	    		ON r.role_id = u.role_id
	    		WHERE u.organisation_id = {$usrOrgId}
    			$orderBy_query";
    	} else {
    		$query = "
	    		SELECT  u.*, r.name AS role_name
	    		FROM    {$this->conf['table']['user']} u, {$this->conf['table']['role']} r
	    		WHERE   r.role_id = u.role_id
	    		AND     u.usr_id <> 0
	    		$orderBy_query";
    	}
    
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

   		$output->aPagedData = $aPagedData;
   		if (is_array($aPagedData['data']) && count($aPagedData['data'])) {
   			$output->pager = ($aPagedData['totalItems'] <= $limit) ? false : true;
   		}
   		
    	$output->totalItems = $aPagedData['totalItems'];
		$output->addOnLoadEvent("switchRowColorOnHover()");
    }
}
?>
