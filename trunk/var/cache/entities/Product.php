<?php
/**
 * Table Definition for product
 */
require_once 'DB/DataObject.php';

class DataObjects_Product extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'product';             // table name
    public $product_id;                      // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  
    public $category_id;                     // int(11)  
    public $title;                           // string(255)  
    public $description;                     // blob(65535)  blob
    public $meta_desc;                       // blob(65535)  blob
    public $meta_keyword;                    // blob(65535)  blob
    public $model;                           // string(255)  
    public $sku;                             // string(255)  
    public $price;                           // real(15)  
    public $currency_id;                     // int(11)  not_null
    public $tax;                             // int(11)  
    public $warranty;                        // int(11)  not_null
    public $quantity;                        // int(11)  
    public $dim_l;                           // int(11)  not_null
    public $dim_w;                           // int(11)  not_null
    public $dim_h;                           // int(11)  not_null
    public $dim_id;                          // int(11)  not_null
    public $weight;                          // int(11)  not_null
    public $weight_id;                       // int(11)  not_null
    public $manufacturer_id;                 // int(11)  
    public $seo;                             // blob(65535)  blob
    public $product_image_id;                // string(255)  
    public $date_created;                    // datetime(19)  binary
    public $last_updated;                    // datetime(19)  binary
    public $status;                          // int(4)  not_null
    public $order_id;                        // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Product',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
