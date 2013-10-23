<?php
/**
 * Table Definition for content
 */
require_once 'DB/DataObject.php';

class DataObjects_Content extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'content';             // table name
    public $content_id;                      // int(11)  not_null primary_key
    public $content_type_id;                 // int(11)  not_null
    public $title;                           // string(255)  not_null
    public $usr_id;                          // int(11)  not_null
    public $category_id;                     // int(11)  not_null
    public $date_created;                    // datetime(19)  not_null binary
    public $date_updated;                    // datetime(19)  not_null binary
    public $update_id;                       // int(11)  not_null
    public $metakeys;                        // string(255)  not_null
    public $is_active;                       // int(6)  not_null
    public $status;                          // int(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Content',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
