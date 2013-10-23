<?php
/**
 * Table Definition for manufacturer
 */
require_once 'DB/DataObject.php';

class DataObjects_Manufacturer extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'manufacturer';        // table name
    public $manufacturer_id;                 // int(11)  not_null primary_key auto_increment
    public $title;                           // string(64)  not_null
    public $image;                           // string(255)  
    public $sort_order;                      // int(3)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Manufacturer',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
