<?php
/**
 * Table Definition for content_discount
 */
require_once 'DB/DataObject.php';

class DataObjects_Content_discount extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content_discount';    // table name
    public $item_discount_id;                // int(11)  not_null primary_key
    public $item_id;                         // int(11)  not_null multiple_key
    public $role_id;                         // int(11)  not_null
    public $quantity;                        // int(4)  not_null
    public $priority;                        // int(5)  not_null
    public $price;                           // real(17)  not_null
    public $discount_type;                   // int(6)  not_null
    public $date_start;                      // date(10)  not_null binary
    public $date_end;                        // date(10)  not_null binary
    public $status;                          // int(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content_discount',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
