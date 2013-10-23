<?php
/**
 * Table Definition for sms_saved
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms_saved extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms_saved';           // table name
    public $sms_saved_id;                    // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $title;                           // string(200)  not_null
    public $description;                     // blob(65535)  not_null blob
    public $saved_type;                      // int(11)  not_null
    public $item_order;                      // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms_saved',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
