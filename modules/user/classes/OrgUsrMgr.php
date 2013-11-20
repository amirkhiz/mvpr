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
        	'edit'       => array('edit'),
        	'update'     => array('update', 'redirectToDefault'),
        	'delete'     => array('delete', 'redirectToDefault'),
        	'requestPasswordReset'     	=> array('requestPasswordReset'),
        	'resetPassword'     		=> array('resetPassword', 'redirectToDefault'),
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
        $input->userID      = $req->get('frmUserID');
        $input->user        = (object)$req->get('user');
        $input->aDelete     = $req->get('frmDelete');
        $input->user->is_acct_active = (isset($input->user->is_acct_active)) ? 1 : 0;
        $input->user->gender = (isset($input->user->gender)) ? 1 : 0;

        //  get referer details if present
        $input->redir = $req->get('redir');

    }

    function display($output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->isAcctActive = ($output->user->is_acct_active) ? ' checked="checked"' : '';
        $output->isMale		  = ($output->user->gender) ? ' checked="checked"' : '';
    }

    function _cmd_list($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$usrId = SGL_Session::getUid();
    	$usrOrgId = SGL_Session::getOrganisationId();
    	
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
	    		WHERE u.organisation_id = {$usrOrgId} and u.role_id in (3,4)
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
    
    function _cmd_edit($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	$output->pageTitle = $this->pageTitle . ' :: Edit';
    	$output->template = 'userAdd.html';
    	$oUser = $this->da->getUserById($input->userID);
    	$output->user = $oUser;
    	$output->user->username_orig = $oUser->username;
    	$output->user->email_orig = $oUser->email;
    	$output->user->birth_date = date("d.m.Y", strtotime($output->user->birth_date));
    	$output->user->categories = explode(",", $output->user->categories);
    	
    	if((SGL_Session::getRoleId()) != SGL_MANAGER || SGL_Session::getOrganisationId() != $oUser->organisation_id){
    		SGL::raiseMsg("authorisation failed");
	        $options = array(
			    'moduleName' => 'default',
	        	'managerName' => 'default',
			);
			SGL_HTTP::redirect($options);
    	}
    	
    	$output->aManagerRoles = array(3 => SGL_String::translate("Product manager"), 4 => SGL_String::translate("Order manager"));
    }
    
    function _cmd_update($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	//echo "<pre>"; print_r($input->user); echo "</pre>"; exit;
    	$oUser = $this->da->getUserById($input->user->usr_id);
    	$oUser->setFrom($input->user);
    	$oUser->last_updated = SGL_Date::getTime();
    	$oUser->updated_by = SGL_Session::getUid();
    	$oUser->birth_date = date("Y-m-d", strtotime($oUser->birth_date));
    	//echo "<pre>"; print_r($oUser); echo "</pre>"; exit;
    	
    	$success = $this->da->updateUser($oUser, $input->user->role_id_orig,
    			$input->user->organisation_id_orig);
    
    	if (!PEAR::isError($success)) {
    		SGL::raiseMsg('details successfully updated', true, SGL_MESSAGE_INFO);
    	} else {
    		SGL::raiseError('There was a problem inserting the record',
    				SGL_ERROR_NOAFFECTEDROWS);
    	}
    }
    
    function _cmd_delete($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	//Get Current user Id
    	$curUsrId = SGL_Session::getUid();
    
    	$results = array();
    	if (is_array($input->aDelete)) {
    		foreach ($input->aDelete as $index => $userId) {
    
    			//  don't allow admin to be deleted
    			if ($userId == $curUsrId) {
    				continue;
    			}
    			$ret = $this->da->deletePrefsByUserId($userId);
    			$ret = $this->da->deletePermsByUserId($userId);
    			if (!empty($this->conf['cookie']['rememberMeEnabled'])) {
    				$ret = $this->da->deleteUserLoginCookiesByUserId($userId);
    			}
    			$query = "DELETE FROM {$this->conf['table']['user']} WHERE usr_id=$userId";
    			if (is_a($this->dbh->query($query), 'PEAR_Error')) {
    				$results[$userId] = 0; //log result for user
    				continue;
    			}
    			$results[$userId] = 1;
    		}
    	} else {
    		SGL::raiseError('Incorrect parameter passed to ' .
    				__CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
    	}
    	//  could eventually display a list of failed/succeeded user ids--just summarize for now
    	$results = array_count_values($results);
    	$succeeded = array_key_exists(1, $results) ? $results[1] : 0;
    	$failed = array_key_exists(0, $results) ? $results[0] : 0;
    	if ($succeeded && !$failed) {
    		$errorType = SGL_MESSAGE_INFO;
    	} elseif (!$succeeded && $failed) {
    		$errorType = SGL_MESSAGE_ERROR;
    	} else {
    		$errorType = SGL_MESSAGE_WARNING;
    	}
    	//  redirect on success
    	SGL::raiseMsg("$succeeded user(s) successfully deleted. $failed user(s) failed.", false, $errorType);
    }
    
    function _cmd_requestPasswordReset($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    	
    	if (isset($this->conf['tuples']['demoMode'])
    			&& $this->conf['tuples']['demoMode'] == true
    			&& $input->userID == 1) {
    		SGL::raiseMsg('Admin password cannot be reset in demo mode',
    				false, SGL_MESSAGE_WARNING);
    		return false;
    	}
    	$output->pageTitle = $this->pageTitle . ' :: Reset password';
    	$output->template = 'userPasswordReset.html';
    	$oUser = $this->da->getUserById($input->userID);
    	$output->user = $oUser;
    	
    	if((SGL_Session::getRoleId()) != SGL_MANAGER || SGL_Session::getOrganisationId() != $oUser->organisation_id){
    		SGL::raiseMsg("authorisation failed");
	        $options = array(
			    'moduleName' => 'default',
	        	'managerName' => 'default',
			);
			SGL_HTTP::redirect($options);
    	}
    	
    }
    
    function _cmd_resetPassword($input, $output)
    {
    	SGL::logMessage(null, PEAR_LOG_DEBUG);
    
    	require_once 'Text/Password.php';
    	$oPassword = new Text_Password();
    	$passwd = $oPassword->create();
    	$oUser = $this->da->getUserById($input->userID);
    	$oUser->passwd = md5($passwd);
    	$success = $oUser->update();
    	if ($input->passwdResetNotify && $success !== false) {
    		require_once SGL_MOD_DIR . '/user/classes/PasswordMgr.php';
    		$success = PasswordMgr::sendPassword($oUser, $passwd);
    	}
    	//  redirect on success
    	if ($success) {
    		SGL::raiseMsg('Password updated successfully', true, SGL_MESSAGE_INFO);
    	} else {
    		$output->template = 'userManager.html';
    		SGL::raiseError('There was a problem inserting the record',
    				SGL_ERROR_NOAFFECTEDROWS);
    	}
    }
}
?>
