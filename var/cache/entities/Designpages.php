<?php
/**
 * Table Definition for designpages
 */
require_once 'DB/DataObject.php';

class DataObjects_Designpages extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'designpages';         // table name
    public $section_id;                      // int(11)  not_null primary_key unsigned
    public $height;                          // int(6)  not_null
    public $width;                           // int(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Designpages',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
