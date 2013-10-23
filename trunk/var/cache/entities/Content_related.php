<?php
/**
 * Table Definition for content_related
 */
require_once 'DB/DataObject.php';

class DataObjects_Content_related extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content_related';     // table name
    public $item_related_id;                 // int(11)  not_null primary_key
    public $item_id;                         // int(11)  not_null
    public $related_id;                      // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content_related',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
