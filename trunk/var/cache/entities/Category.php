<?php
/**
 * Table Definition for category
 */
require_once 'DB/DataObject.php';

class DataObjects_Category extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'category';            // table name
    public $category_id;                     // int(11)  not_null primary_key
    public $title;                           // string(255)  not_null
    public $parent_id;                       // int(11)  not_null multiple_key
    public $level_id;                        // int(11)  not_null
    public $image;                           // string(255)  not_null
    public $description;                     // blob(65535)  not_null blob
    public $metakeys;                        // string(255)  not_null
    public $order_id;                        // int(11)  not_null
    public $status;                          // int(11)  not_null
    public $created_date;                    // datetime(19)  not_null binary
    public $created_by;                      // int(11)  not_null
    public $updated_date;                    // datetime(19)  not_null binary
    public $updated_by;                      // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Category',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
