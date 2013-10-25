<?php
/**
 * Table Definition for item_category
 */
require_once 'DB/DataObject.php';

class DataObjects_Item_category extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'item_category';       // table name
    public $item_category_id;                // int(11)  not_null primary_key multiple_key
    public $label;                           // string(32)  
    public $perms;                           // string(32)  
    public $parent_id;                       // int(11)  multiple_key
    public $root_id;                         // int(11)  multiple_key
    public $left_id;                         // int(11)  multiple_key
    public $right_id;                        // int(11)  multiple_key
    public $order_id;                        // int(11)  multiple_key
    public $level_id;                        // int(11)  multiple_key

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Item_category',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
