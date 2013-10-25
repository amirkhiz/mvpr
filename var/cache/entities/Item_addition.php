<?php
/**
 * Table Definition for item_addition
 */
require_once 'DB/DataObject.php';

class DataObjects_Item_addition extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'item_addition';       // table name
    public $item_addition_id;                // int(11)  not_null primary_key
    public $item_id;                         // int(11)  not_null multiple_key
    public $item_type_mapping_id;            // int(11)  not_null multiple_key
    public $addition;                        // blob(65535)  blob
    public $trans_id;                        // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Item_addition',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
