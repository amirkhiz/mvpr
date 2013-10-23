<?php
/**
 * Table Definition for content_alias
 */
require_once 'DB/DataObject.php';

class DataObjects_Content_alias extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content_alias';       // table name
    public $item_alias_id;                   // int(11)  not_null primary_key
    public $item_id;                         // int(11)  not_null
    public $alias;                           // string(200)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content_alias',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
