<?php
/**
 * Table Definition for document_type
 */
require_once 'DB/DataObject.php';

class DataObjects_Document_type extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'document_type';       // table name
    public $document_type_id;                // int(11)  not_null primary_key
    public $name;                            // string(32)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Document_type',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
