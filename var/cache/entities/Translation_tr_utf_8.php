<?php
/**
 * Table Definition for translation_tr_utf_8
 */
require_once 'DB/DataObject.php';

class DataObjects_Translation_tr_utf_8 extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'translation_tr_utf_8';    // table name
    public $page_id;                         // string(50)  multiple_key
    public $translation_id;                  // blob(65535)  not_null multiple_key blob
    public $tr_utf_8;                        // blob(65535)  blob

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Translation_tr_utf_8',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
