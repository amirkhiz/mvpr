<?php
/**
 * Table Definition for content_rated
 */
require_once 'DB/DataObject.php';

class DataObjects_Content_rated extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content_rated';       // table name
    public $item_rated_id;                   // int(11)  not_null primary_key
    public $item_id;                         // int(11)  not_null
    public $role_id;                         // int(11)  not_null
    public $points;                          // int(8)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content_rated',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
