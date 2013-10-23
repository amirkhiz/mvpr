<?php
/**
 * Table Definition for country
 */
require_once 'DB/DataObject.php';

class DataObjects_Country extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'country';             // table name
    public $country_id;                      // int(11)  not_null primary_key
    public $name;                            // string(128)  not_null
    public $iso_code_2;                      // string(2)  not_null
    public $iso_code_3;                      // string(3)  not_null
    public $address_format;                  // blob(65535)  not_null blob
    public $postcode_required;               // int(1)  not_null
    public $status;                          // int(1)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Country',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
