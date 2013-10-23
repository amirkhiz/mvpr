<?php
/**
 * Table Definition for group_h
 */
require_once 'DB/DataObject.php';

class DataObjects_Group_h extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'group_h';             // table name
    public $group_id;                        // int(11)  not_null primary_key
    public $title;                           // string(200)  not_null
    public $usr_id;                          // int(11)  not_null
    public $created_by;                      // int(11)  not_null
    public $date_created;                    // datetime(19)  not_null binary
    public $order_id;                        // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Group_h',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
