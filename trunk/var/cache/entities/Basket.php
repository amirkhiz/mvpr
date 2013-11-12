<?php
/**
 * Table Definition for basket
 */
require_once 'DB/DataObject.php';

class DataObjects_Basket extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'basket';              // table name
    public $basket_id;                       // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $product_id;                      // int(11)  not_null
    public $quantity;                        // int(11)  not_null
    public $date_created;                    // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Basket',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
