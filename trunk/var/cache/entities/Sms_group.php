<?php
/**
 * Table Definition for sms_group
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms_group extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms_group';           // table name
    public $sms_group_id;                    // int(11)  not_null primary_key
    public $title;                           // string(200)  not_null
    public $usr_id;                          // int(11)  not_null
    public $date_created;                    // datetime(19)  not_null binary
    public $description;                     // string(255)  not_null
    public $birth_sms;                       // int(6)  not_null
    public $marriage_sms;                    // int(6)  not_null
    public $item_order;                      // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms_group',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
