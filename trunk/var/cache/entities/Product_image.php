<?php
/**
 * Table Definition for product_image
 */
require_once 'DB/DataObject.php';

class DataObjects_Product_image extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'product_image';       // table name
    public $product_image_id;                // int(11)  not_null primary_key
    public $product_id;                      // int(11)  not_null
    public $title;                           // string(255)  not_null
    public $url;                             // blob(65535)  not_null blob
    public $description;                     // int(11)  not_null
    public $order_id;                        // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Product_image',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
