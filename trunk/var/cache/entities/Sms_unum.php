<?php
/**
 * Table Definition for sms_unum
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms_unum extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms_unum';            // table name
    public $sms_unum_id;                     // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $unumber;                         // string(20)  not_null
    public $sort_id;                         // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms_unum',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
