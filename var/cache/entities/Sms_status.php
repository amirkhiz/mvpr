<?php
/**
 * Table Definition for sms_status
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms_status extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms_status';          // table name
    public $sms_status_id;                   // int(11)  not_null primary_key
    public $title;                           // string(100)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms_status',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
