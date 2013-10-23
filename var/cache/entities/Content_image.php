<?php
/**
 * Table Definition for content_image
 */
require_once 'DB/DataObject.php';

class DataObjects_Content_image extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content_image';       // table name
    public $item_image_id;                   // int(11)  not_null primary_key
    public $item_id;                         // int(11)  not_null
    public $image;                           // string(255)  
    public $item_order;                      // int(3)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content_image',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
