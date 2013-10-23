<?php
/**
 * Table Definition for faq
 */
require_once 'DB/DataObject.php';

class DataObjects_Faq extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'faq';                 // table name
    public $faq_id;                          // int(11)  not_null primary_key
    public $question;                        // string(255)  not_null
    public $answer;                          // blob(65535)  not_null blob
    public $date_created;                    // datetime(19)  not_null binary
    public $date_updated;                    // datetime(19)  not_null binary
    public $created_by;                      // int(11)  not_null
    public $updated_by;                      // int(11)  not_null
    public $item_order;                      // int(11)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Faq',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
