<?php
/**
 * Table Definition for item_type
 */
require_once 'DB/DataObject.php';

class DataObjects_Item_type extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'item_type';           // table name
    public $item_type_id;                    // int(11)  not_null primary_key
    public $item_type_name;                  // string(64)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Item_type',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
