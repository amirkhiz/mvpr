<?php
/**
 * Table Definition for currency
 */
require_once 'DB/DataObject.php';

class DataObjects_Currency extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'currency';            // table name
    public $currency_id;                     // int(11)  not_null primary_key
    public $title;                           // string(32)  not_null
    public $code;                            // string(3)  not_null
    public $symbol_left;                     // string(12)  not_null
    public $symbol_right;                    // string(12)  not_null
    public $decimal_place;                   // string(1)  not_null
    public $value;                           // real(15)  not_null
    public $status;                          // int(6)  not_null
    public $date_updated;                    // datetime(19)  not_null binary

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Currency',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
