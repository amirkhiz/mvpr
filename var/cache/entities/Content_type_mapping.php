<?php
/**
 * Table Definition for content_type_mapping
 */
require_once 'DB/DataObject.php';

class DataObjects_Content_type_mapping extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content_type_mapping';    // table name
    public $content_type_mapping_id;         // int(11)  not_null primary_key
    public $content_type_id;                 // int(11)  not_null
    public $title;                           // string(200)  not_null
    public $options;                         // blob(65535)  not_null blob
    public $tag_order;                       // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content_type_mapping',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
