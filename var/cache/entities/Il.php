<?php
/**
 * Table Definition for il
 */
require_once 'DB/DataObject.php';

class DataObjects_Il extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'il';                  // table name
    public $il_id;                           // int(11)  not_null primary_key auto_increment
    public $title;                           // string(100)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Il',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
