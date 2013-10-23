<?php
/**
 * Table Definition for adminTest
 */
require_once 'DB/DataObject.php';

class DataObjects_AdminTest extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'adminTest';           // table name
    public $test_id;                         // int(11)  not_null primary_key
    public $titr;                            // string(100)  not_null
    public $boxing1;                         // blob(65535)  not_null blob
    public $sinas;                           // string(100)  not_null
    public $sanatr;                          // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_AdminTest',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
