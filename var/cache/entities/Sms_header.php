<?php
/**
 * Table Definition for sms_header
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms_header extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms_header';          // table name
    public $sms_header_id;                   // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $title;                           // string(20)  not_null
    public $default;                         // int(4)  not_null
    public $item_order;                      // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms_header',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
