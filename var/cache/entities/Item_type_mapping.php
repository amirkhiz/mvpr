<?php
/**
 * Table Definition for item_type_mapping
 */
require_once 'DB/DataObject.php';

class DataObjects_Item_type_mapping extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'item_type_mapping';    // table name
    public $item_type_mapping_id;            // int(11)  not_null primary_key
    public $item_type_id;                    // int(11)  not_null multiple_key
    public $field_name;                      // string(64)  
    public $field_type;                      // int(6)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Item_type_mapping',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
