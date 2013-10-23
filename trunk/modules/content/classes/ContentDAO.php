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

class ContentDAO extends SGL_Manager
{
	var $inputTypes = array();
	var $_params  = array();
	var $usrId;
	var $a = "ssss";
	
	/**
     * Constructor - set default resources.
     *
     * @return ContentDAO
     */
    function __construct()
    {
        parent::__construct();
        
        $this->inputTypes = array(
			//"textbox" => "Text input",
		    //"textarea" => "Text area",
		    //"checkbox" => "Checkbox",
		    //"radio" => "Radio",
			"select" => "Selectbox",
        	"multipleselect" => "Multiple Selectbox",
			//"file" => "File",
			//"image" => "Image",
			//"date" => "Date"
		);
        
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
    
    function inputTypeArray()
    {
    	return $this->inputTypes;
    }
    
    /**
     * Send sms
     * 
     * @param	array	$sms	Array of sms data
     * @access	public		
     * @return	status of sending sms
     */
    function sendSms($smsdata)
    {
    	$sms = DB_DataObject::factory($this->conf['table']['sms']);
        $sms->setFrom($smsdata);
        $sms->sms_id 	= $this->dbh->nextId($this->conf['table']['sms']);
        $sms->usr_id 	= SGL_Session::getUid();
        $sms->sms_date 	= SGL_Date::getTime(true);
    	return $sms->insert();
    }
    
}
?>
