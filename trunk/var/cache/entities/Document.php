<?php
/**
 * Table Definition for document
 */
require_once 'DB/DataObject.php';

class DataObjects_Document extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'document';            // table name
    public $document_id;                     // int(11)  not_null primary_key
    public $item_category_id;                // int(11)  multiple_key
    public $document_type_id;                // int(11)  not_null multiple_key
    public $name;                            // string(128)  
    public $file_size;                       // int(11)  
    public $mime_type;                       // string(32)  
    public $date_created;                    // datetime(19)  binary
    public $added_by;                        // int(11)  
    public $description;                     // blob(65535)  blob
    public $num_times_downloaded;            // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Document',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
