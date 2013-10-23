<?php
/**
 * Table Definition for smsunum
 */
require_once 'DB/DataObject.php';

class DataObjects_Smsunum extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'smsunum';             // table name
    public $sms_unum_id;                     // int(11)  not_null primary_key
    public $usr_id;                          // int(11)  not_null
    public $unumber;                         // int(15)  not_null
    public $sort_id;                         // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Smsunum',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
