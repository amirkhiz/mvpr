<?php
/**
 * Table Definition for sms
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms';                 // table name
    public $sms_id;                          // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $identifier;                      // int(11)  not_null
    public $fnumber;                         // string(20)  not_null
    public $tnumber;                         // string(20)  not_null
    public $context;                         // blob(65535)  not_null blob
    public $cost;                            // int(11)  not_null
    public $sms_type;                        // int(11)  not_null
    public $sms_status_id;                   // int(11)  not_null
    public $sms_date;                        // datetime(19)  not_null binary
    public $send_date;                       // datetime(19)  not_null binary
    public $suspend;                         // int(6)  not_null
    public $active;                          // int(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
