<?php
/**
 * Table Definition for koy
 */
require_once 'DB/DataObject.php';

class DataObjects_Koy extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'koy';                 // table name
    public $koy_id;                          // int(11)  not_null primary_key auto_increment
    public $ilce_id;                         // int(11)  not_null
    public $title;                           // string(200)  not_null
    public $urltitle;                        // string(100)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Koy',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
