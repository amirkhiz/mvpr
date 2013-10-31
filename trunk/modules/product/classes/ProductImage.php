<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2009, Parsoon Co                                            |
// | All rights reserved.                                                      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Zoopeer 1.0                                                               |
// +---------------------------------------------------------------------------+
// | ZoopeerCategory.php                                                       |
// +---------------------------------------------------------------------------+
// | Author: Sina Saderi <s.saderi@parsoon.net>                          |
// +---------------------------------------------------------------------------+
// $Id: Gallery.php,v 1.0 2009/12/09 14:12:00 sina Exp $

include_once 'DB/DataObject.php';
include_once SGL_CORE_DIR . '/Image.php';

/**
 * gallery module's functions.
 *
 * @package Gallery
 * @author  Sina Saderi <s.saderi@parsoon.net>
 * @version $Revision: 1.0 $
 */

class ProductImage extends SGL_Manager {
	var $_tableStructure = array(
		    'product_image_id'	=> 'product_image_id',
			'product_id'		=> 'product_id',
			'title'				=> 'title',
			'url'				=> 'url',
			'description'  		=> 'description',
			'order_id'			=> 'order_id',
	);
	
	/**
     * Constructor
     *
     * @access  public
     * @return  void
     */
	function __construct(){
		SGL::logMessage(null, PEAR_LOG_DEBUG);
		parent::__construct();
	}
	
	/**
    * Returns a singleton of gallery instance.
    *
    * @access  public
    * @static
    * @return  object
    */
    function &singleton($forceNew = false){
        static $instance;

        // If the instance is not available, create one
        if (!is_object($instance) || $forceNew) {
            $instance = new Gallery();
        }
        return $instance;
    }
 
	
	 /**
     * Generate filename.
     * 
     * @access  public
     * @param   string $imageName
     * @return  string
     */
    function generateUniqueFileName($imageName)
    {
    	$dotPos = strrpos($imageName, ".");
		$firstSection = substr($imageName, 0, $dotPos);
		$lastSection = substr($imageName, $dotPos);
		$imageName = $firstSection . time() . $lastSection; 
        return $imageName;
    }
	
	/**
    * Image validation.
    *
    * @access  public
    * @param   array  $image image url
    * @return  boolean
    */
	function isValidImage($image){
		$ok = @getimagesize($image['tmp_name']);
        return $ok !== false;
	}
	
	
	/**
    * Upload image .
    *
    * @access  private
    * @param   string $uploadImageName Image file name
    * @param   string $uploadImagetmp  Image template file
    * @return  boolean
    */
	function uploadImage($uploadImageName, $uploadImagetmp)
	{
   		SGL::logMessage(null, PEAR_LOG_DEBUG);
		
		$image = & new SGL_Image($uploadImageName);
		$imageConfig = array("driver" => "GD_SGL", 
		                     "saveQuality"=>"100", 
							 "thumbDir"=>"small", 
		                     "thumbnails" =>array("small" =>array( "driver" => "GD_SGL", 
							                                       "saveQuality"=>"100",
																   "thumbDir"=>"small", 
																   "thumbnails" => "small",
																   "resize" =>"width:120,height:120" , 
																   "uploadDir" => "/www/images/products"
																 )
												 ),
							 "uploadDir" => "/www/images/products"
							);
		$ok   = $image->init($imageConfig); 
        if (PEAR::isError($ok)) {
            return false;
        }
		//Create image and it`s thumbnail in image directory . If exist them, replace them
		$ok = $image->create($uploadImagetmp);
	    if (PEAR::isError($ok)) {
            return false;
        }
	    return true;
    }
	
	
	
	/**
    * Replace image 
    *
    * @access  private
    * @param   string $uploadImageName Image file name
    * @param   string $uploadImagetmp  Image template file
    * @return  boolean
    */
	function replaceImage($uploadImageName, $uploadImagetmp){
   		SGL::logMessage(null, PEAR_LOG_DEBUG);

		$image = & new SGL_Image($uploadImageName);
		$imageConfig = array("driver" => "GD_SGL", 
		                     "saveQuality"=>"100", 
							 "thumbDir"=>"small", 
		                     "thumbnails" =>array("small" =>array( "driver" => "GD_SGL", 
							                                       "saveQuality"=>"100",
																   "thumbDir"=>"small", 
																   "thumbnails" => "small",
																   "resize" =>"width:120,height:120" , 
																   "uploadDir" => "/www/images/products"
																 )
												 ),
							 "uploadDir" => "/www/images/products"
							);
		$ok   = $image->init($imageConfig); 
        if (PEAR::isError($ok)) {
            return false;
        }
		//Create image and it`s thumbnail in image directory . If exist them, replace them
		$ok = $image->replace($uploadImagetmp);
	    if (PEAR::isError($ok)) {
            return false;
        }
	    return true;
    }
	
	/**
    * Delete image 
    *
    * @access  private
    * @param   string $imageName Image file name
    * @return  boolean
    */
	function deleteImage($imageName){
   		SGL::logMessage(null, PEAR_LOG_DEBUG);
		$image = & new SGL_Image($imageName);
		$imageConfig = array("driver" => "GD_SGL", 
		                     "saveQuality"=>"100", 
							 "thumbDir"=>"small", 
		                     "thumbnails" =>array("small" =>array( "driver" => "GD_SGL", 
							                                       "saveQuality"=>"100",
																   "thumbDir"=>"small", 
																   "thumbnails" => "small",
																   "resize" =>"width:120,height:120" , 
																   "uploadDir" => "/www/images/products"
																 )
												 ),
							 "uploadDir" => "/www/images/products"
							);
		$ok   = $image->init($imageConfig); 
        if (PEAR::isError($ok)) {
            return false;
        }
		//Create image and it`s thumbnail in image directory . If exist them, replace them
		$ok = $image->delete($imageName);
	    if (PEAR::isError($ok)) {
            return false;
        }
	    return true;
    }
	
	
	/**
    * Insert new data
    *
    * @access  public
    * @param   array  $values values of image
    * @return  boolean
    */
	function create($values, $productId){
		SGL::logMessage(null, PEAR_LOG_DEBUG);
		
		foreach ($values as $key => $value)
    	{
    		$value['name'] = $this->generateUniqueFileName($value['name']);

    		$proImage = DB_DataObject::factory($this->conf['table']['product_image']);
    		$proImage->setFrom($values);
    		$proImage->product_image_id		= $proImgId[] = $this->dbh->nextId('product_image');
    		$proImage->product_id 			= $productId;
    		$proImage->title			 	= $value['name'];
    		 
    		$success = $proImage->insert();
    		
    		if ($success) {
    			$this->uploadImage($value['name'], $value['tmp_name']);
    			$result = $proImgId;
    		} else {
    			//  error
    			SGL::raiseError('Incorrect parent node id or module id passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
    			$result = false;
    		}
    	}
    	
    	return $result;
	}
	
	/**
	* Update image information
	* 
	* @access public
	* @param int $product_image_id
	* @param array $values values of image
	* @return boolean
	*/
	 
	 function update($product_image_id, &$values){
	 	SGL::logMessage(null, PEAR_LOG_DEBUG);
		// generate a unique image name
		if(isset($values->image['name'])){
			$values->image['name'] = $this->generateUniqueFileName($values->image['name']);
		}
		
		$gallery = DB_DataObject::factory($this->conf['table']['product_image']);
        $gallery->get($values->product_image_id);
        $gallery->setFrom($values);
		
		$gallery->last_update = SGL_Date::getTime(true);
		$gallery->enabled = isset($values->enabled) ? 1 : 0 ;
		if(isset($values->image['name'])){
			$gallery->image_name      = $values->image['name'];
		}
        $success = $gallery->update();
        if ($success) {	
			if(isset($values->image['name'])){
				$this->replaceImage($values->image['name'], $values->image['tmp_name']);
			}
			return $success;
		} else {
			//  error
            SGL::raiseError('Incorrect parent node id or module id passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
			return false;
		}
	 }
	
	
	/**
	* Delete Selected images
	* 
	* @access public
	* @param array $aDeletes list of images id
	* @return boolean
	* 
	*/
	function delete(&$aDeletes){
		SGL::logMessage(null, PEAR_LOG_DEBUG);
		
		if (is_array($aDeletes)) {
			// delete selected images
            foreach ($aDeletes as $key => $product_image_id) {
                $gallery = DB_DataObject::factory($this->conf['table']['product_image']);
                $gallery->get($product_image_id);
				if($gallery->image_name){
					$this->deleteImage($gallery->title);
				}
                $gallery->delete();
                unset($gallery);
           	}
            return true;
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .__FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
	}
	
	/**
	 * change status of image to enable
	 * 
	 * @access public
	 * @param int $product_image_id index of an image
	 * @retun boolean
	 * 
	 */
	function enable($product_image_id){
		
		$gallery = DB_DataObject::factory($this->conf['table']['product_image']);
		
		if(intval($product_image_id)>0) {
			//publish this category
			$gallery = DB_DataObject::factory($this->conf['table']['product_image']);
			$gallery->get(intval($product_image_id));
			$gallery->enabled = 1;
			$success = $gallery->update();
			return $success;
		} else {
			//  error
            SGL::raiseError('Incorrect category id passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
			return false;
		}
	}
	
	/**
	 * change status of image to disable
	 * 
	 * @access public
	 * @param int $product_image_id index of an image
	 * @retun boolean
	 * 
	 */
	function disable($product_image_id){
		
		$gallery = DB_DataObject::factory($this->conf['table']['product_image']);
		
		if(intval($product_image_id)>0) {
			//publish this category
			$gallery = DB_DataObject::factory($this->conf['table']['product_image']);
			$gallery->get(intval($product_image_id));
			$gallery->enabled = 0;
			$success = $gallery->update();
			return $success;
		} else {
			//  error
            SGL::raiseError('Incorrect category id passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
			return false;
		}
	}
	
	/**
    * get a field of this image .
    *
    * @access public
    * @param  int  $product_image_id index of an image
    * @param  int  $field_name  Determine field name to fetch it`s value 
    * @return string
    */
	function get($product_image_id, $field_name){
		SGL::logMessage(null, PEAR_LOG_DEBUG);

		if(intval($product_image_id)>0 && $field_name!='') {
		    if(array_key_exists($field_name, $this->_tableStructure)) {
			    $gallery = DB_DataObject::factory($this->conf['table']['product_image']);
				$gallery->get(intval($product_image_id));
				//Add quote to field name
				$field_name = $gallery->escape($field_name);

				$field_value = $gallery->$field_name;
				return $field_value;
			}
		} else {
			//  error
            SGL::raiseError('gallery id or diels name is not valid ', SGL_ERROR_INVALIDARGS);
			return false;
		}
	}
	
	/**
    * get all of image's fields.
    *
    * @access public
    * @param  int  $product_image_id index of an image
    * @return array
    */
	function getImageInfo($product_image_id)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);

		if(intval($product_image_id)>0) {
			//Get gallery information
			$gallery  = DB_DataObject::factory($this->conf['table']['product_image']);
			$gallery->get(intval($product_image_id));
			return $gallery->toArray();
		} else {
			//  error
            SGL::raiseError('Incorrect gallery id passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
			return false;
		}
	}
	
	/**
    * get an image information by sql filtering
    *
    * @access public
    * @param  int  $filters   Sql filter to filtering results
    * @return array(two dimensional array)
    */
	function getImages($filters)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);

		if(is_array($filters) && count($filters)>0) {
			$gallery = DB_DataObject::factory($this->conf['table']['product_image']);
			$aGalleries = array();
			foreach($filters as $key => $value) {
				
				switch($key) {
					case "select" :
						if(is_string($value)) {
							$gallery->selectAdd($value);
						} elseif(is_array($value)) {
							foreach($value as $v) {
								$gallery->selectAdd($v);
							}
						}
					    break;
					case "where"  :
                        if(is_string($value)) {
							$gallery->whereAdd($value);
						} elseif(is_array($value)) {
							foreach($value as $v) {
								$gallery->whereAdd($v);
							}
						}
					    break;
					case "limit"  :
                        if(is_int($value)) {
                        	$gallery->limit($value);
                        } elseif(is_array($value)) {
                        	$gallery->limit($value[0],$value[1]);
                        }
					    break;
					case "orderBy" :
                        if(is_string($value)) {
                        	$gallery->orderBy($value);
                        }
						break;
					case "groupBy" :
                        if(is_string($value)) {
                        	$gallery->groupBy($value);
                        }
						break;
				}
			}

			$gallery->find();

			while($gallery->fetch()) {
				$aGalleries[] = $gallery->toArray();
			}
			//exit;
            //Return categories
			return $aGalleries;
		} else {
			//  error
            SGL::raiseError('Incorrect parameters passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
			return false;
		}
	}
	
	
	/**
    * Build menu based on filters
    *
    * @access public
    * @param  array  $filters     FieldName.It is optional parameter
    * @param  int    $selectedCat Selected images id.It is optional parameter
    * @return html code (Drop down list)
    */
	function getDropDown($filters = array(), $selectedCat = 0)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);

		$galleries = array();

		//Generate category tree
		$galleries = $this->getImages($filters);
			
		
		$aGalleries = array();
		foreach($galleries as $image) {
			$aGalleries[$image['product_image_id']] = $image['title'];
		}

		return SGL_Output::generateSelect($aGalleries, $selectedCat);
		
	}
	
	
	/**
    * Images available based on filters
    *
    * @access public
    * @param  int  $module_id Module id
    * @param  int  $filters   Filter items. It is a optional parameter
    * @return int
    */
	function getCount(&$filters)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);

			$gallery = DB_DataObject::factory($this->conf['table']['product_image']);
			if(is_array($filters) && count($filters)>0) {
				foreach($filters as $key=>$value) {
					$gallery->$key = $value;
				}
			}
			return $gallery->count();
	}
}
?>
