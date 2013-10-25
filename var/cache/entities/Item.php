<?php
/**
 * Table Definition for item
 */
require_once 'DB/DataObject.php';

class DataObjects_Item extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'item';                // table name
    public $item_id;                         // int(11)  not_null primary_key
    public $item_category_id;                // int(11)  multiple_key
    public $item_type_id;                    // int(11)  not_null multiple_key
    public $created_by_id;                   // int(11)  
    public $updated_by_id;                   // int(11)  
    public $date_created;                    // datetime(19)  binary
    public $last_updated;                    // datetime(19)  binary
    public $start_date;                      // datetime(19)  binary
    public $expiry_date;                     // datetime(19)  binary
    public $status;                          // int(6)  
    public $hits;                            // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Item',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
