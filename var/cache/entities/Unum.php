<?php
/**
 * Table Definition for unum
 */
require_once 'DB/DataObject.php';

class DataObjects_Unum extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'unum';                // table name
    public $unum_id;                         // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $unumber;                         // int(15)  not_null
    public $sort_id;                         // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Unum',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
