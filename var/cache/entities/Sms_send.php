<?php
/**
 * Table Definition for sms_send
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms_send extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms_send';            // table name
    public $sms_send_id;                     // int(11)  not_null
    public $usr_id;                          // int(11)  not_null
    public $identifier;                      // int(11)  not_null
    public $fnumber;                         // int(11)  not_null
    public $tnumber;                         // int(11)  not_null
    public $context;                         // int(11)  not_null
    public $cost;                            // int(11)  not_null
    public $type;                            // int(11)  not_null
    public $status;                          // int(11)  not_null
    public $sms_date;                        // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms_send',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
