<?php
/**
 * Table Definition for slider
 */
require_once 'DB/DataObject.php';

class DataObjects_Slider extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'slider';              // table name
    public $slider_id;                       // int(11)  not_null primary_key
    public $date_created;                    // datetime(19)  binary
    public $last_updated;                    // datetime(19)  binary
    public $image1;                          // string(255)  
    public $title;                           // string(255)  
    public $order_id;                        // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Slider',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
