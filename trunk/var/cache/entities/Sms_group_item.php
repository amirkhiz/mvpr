<?php
/**
 * Table Definition for sms_group_item
 */
require_once 'DB/DataObject.php';

class DataObjects_Sms_group_item extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'sms_group_item';      // table name
    public $sms_group_item_id;               // int(11)  not_null primary_key
    public $sms_group_id;                    // string(100)  not_null
    public $usr_id;                          // int(11)  not_null
    public $mobile;                          // string(13)  
    public $national_code;                   // string(15)  
    public $first_name;                      // string(100)  
    public $last_name;                       // string(100)  
    public $gender;                          // int(6)  
    public $birth_date;                      // date(10)  binary
    public $birth_sms;                       // int(6)  not_null
    public $marriage_date;                   // date(10)  binary
    public $marriage_sms;                    // int(6)  not_null
    public $telephone;                       // string(13)  not_null
    public $postcode;                        // string(20)  
    public $email;                           // string(50)  not_null
    public $website;                         // string(30)  not_null
    public $city;                            // string(30)  not_null
    public $region;                          // string(30)  not_null
    public $zone;                            // string(30)  not_null
    public $address;                         // blob(65535)  not_null blob
    public $greetings;                       // string(30)  not_null
    public $blood;                           // string(3)  not_null
    public $job;                             // string(30)  not_null
    public $education;                       // string(30)  not_null
    public $evidence;                        // string(30)  not_null
    public $description;                     // blob(65535)  not_null blob

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Sms_group_item',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
