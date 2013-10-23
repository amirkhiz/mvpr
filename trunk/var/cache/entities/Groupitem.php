<?php
/**
 * Table Definition for groupitem
 */
require_once 'DB/DataObject.php';

class DataObjects_Groupitem extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'groupitem';           // table name
    public $groupitem_id;                    // int(11)  not_null primary_key
    public $sms_group_id;                    // int(11)  not_null
    public $first_name;                      // string(100)  not_null
    public $last_name;                       // string(100)  not_null
    public $mobile;                          // string(13)  not_null
    public $telephone;                       // string(13)  not_null
    public $email;                           // string(50)  not_null
    public $website;                         // string(30)  not_null
    public $city;                            // string(30)  not_null
    public $region;                          // string(30)  not_null
    public $zone;                            // string(30)  not_null
    public $address;                         // blob(65535)  not_null blob
    public $postcode;                        // string(20)  not_null
    public $birthday;                        // datetime(19)  not_null binary
    public $gender;                          // int(1)  not_null
    public $job;                             // string(30)  not_null
    public $education;                       // string(30)  not_null
    public $evidence;                        // string(30)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Groupitem',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
