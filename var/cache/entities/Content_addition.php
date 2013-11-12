<?php
/**
 * Table Definition for content_addition
 */
require_once 'DB/DataObject.php';

class DataObjects_Content_addition extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content_addition';    // table name
    public $content_addition_id;             // int(11)  not_null primary_key
    public $product_id;                      // int(11)  not_null
    public $content_type_mapping_id;         // int(11)  not_null
    public $value;                           // string(255)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content_addition',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
