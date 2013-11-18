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
// | Seagull 1.1                                                               |
// +---------------------------------------------------------------------------+
// | ContentDAO.php                                                            |
// +---------------------------------------------------------------------------+
// | Authors:   Sina Saderi <sina.saderi@gmail.com>                            |
// +---------------------------------------------------------------------------+

require_once 'DB/DataObject.php';
/**
 * Data access methods for the content module.
 *
 * @package Content
 * @author  Sina Saderi <sina.saderi@gmail.com>
 */

class CategoryDAO extends SGL_Manager
{
	var $_params  = array();
	var $usrId;
	
	/**
     * Constructor - set default resources.
     *
     * @return ContentDAO
     */
    function __construct()
    {
        parent::__construct();
        
        $this->usrId = SGL_Session::getUid();
    }

    /**
     * Returns a singleton SmsDAO instance.
     *
     * example usage:
     * $da =  ContentDAO::singleton();
     * warning: in order to work correctly, the DA
     * singleton must be instantiated statically and
     * by reference
     *
     * @access  public
     * @static
     * @return  ContentDAO reference to ContentDAO object
     */
    function singleton()
    {
        static $instance;

        // If the instance is not there, create one
        if (!isset($instance)) 
        {
            $instance = new self();
        }
        return $instance;
    }
    
    
    /**
     * getParentId
     * 
     * @param	integer		$catId		Id of category
     * @param	integer		$parentRow	number of top parent		
     * @access	public		
     * @return	get parent_id of a row
     */
    function getParentId($catId, $parentRow = 1)
    {
    	
    	switch($parentRow){
    		
    		case "1":
    			$query = "select parent_id from {$this->conf['table']['category']} where category_id = '$catId'";
    		break;
    		
    		case "1":
    			$query = "select c1.category_id as topId, c2.parent_id 
        							from {$this->conf['table']['category']} as c1
        							join {$this->conf['table']['category']} as c2 on c2.category_id = c1.parent_id    
        							where c1.parent_id = '$catId'";
    		break;
    	}
    	
    	$cat = $this->dbh->getRow($query);
    	return $cat->parent_id;
    }
    
}
?>
