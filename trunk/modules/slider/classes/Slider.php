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
// $Id: Slider.php,v 1.0 2009/12/09 14:12:00 sina Exp $

include_once 'DB/DataObject.php';
include_once SGL_CORE_DIR . '/Image.php';

/**
 * slider module's functions.
 *
 * @package Slider
 * @author  Sina Saderi <s.saderi@parsoon.net>
 * @version $Revision: 1.0 $
 */

class Slider
{
	var $conf;
	var $dbh;
	var $_tableStructure = array(
		    'slider_id'   	=> 'slider_id',
			'title'        	=> 'title',
			'image1'   		=> 'image1',
			'date_created' 	=> 'date_created',
			'last_updated'  => 'last_updated',
			'order_id'   	=> 'order_id',
	);
	
	/**
     * Constructor
     *
     * @access  public
     * @return  void
     */
	function Slider()
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);

		//Load slider module config file
		$c = &SGL_Config::singleton();
		$this->conf = $c->ensureModuleConfigLoaded('slider');
		$this->dbh  = &SGL_DB::singleton();
		
	}
	
	/**
    * Returns a singleton of slider instance.
    *
    * @access  public
    * @static
    * @return  object
    */
    function &singleton($forceNew = false)
    {
        static $instance;

        // If the instance is not available, create one
        if (!is_object($instance) || $forceNew) {
            $instance = new Slider();
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
																   "resize" =>"width:100,height:100" , 
																   "uploadDir" => "/www/images/slider"
																 )
												 ),
							 "uploadDir" => "/www/images/slider"
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
																   "resize" =>"width:100,height:100" , 
																   "uploadDir" => "/www/images/slider"
																 )
												 ),
							 "uploadDir" => "/www/images/slider"
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
																   "resize" =>"width:100,height:100" , 
																   "uploadDir" => "/www/images/slider"
																 )
												 ),
							 "uploadDir" => "/www/images/slider"
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
	function create(&$values){
		SGL::logMessage(null, PEAR_LOG_DEBUG);
		$values->image['name'] = $this->generateUniqueFileName($values->image['name']);
		
		//Get max item order
        $slider = DB_DataObject::factory($this->conf['table']['slider']);
        $slider->selectAdd();
        $slider->selectAdd('MAX(order_id) AS new_order');
        $slider->groupBy('order_id');
        $maxItemOrder = $slider->find(true);
        unset($slider);	
        
		//  insert new record
        $slider = DB_DataObject::factory($this->conf['table']['slider']);
        $slider->setFrom($values);
        $slider->slider_id 		= $this->dbh->nextId('slider');
        $slider->date_created   = SGL_Date::getTime(true);
		$slider->last_updated 	= SGL_Date::getTime(true);
		$slider->image1      	= $values->image['name'];
        $slider->order_id      	= $maxItemOrder + 1;
        
        $success = $slider->insert();
        
		//Add translation in database
    	if ($this->conf['translation']['container'] == 'db' || $this->conf['translation']['multiLanguageContents']) {
    		//print_r($slider);die;
    		$this->trans = & SGL_Translation::singleton('admin');
    		$lang = SGL_Translation::getFallbackLangID();
        	$this->trans->add($slider->slider_id, 'sliderTitle', array($lang => $slider->title));
    	}
        
        if ($success) {	
			$this->uploadImage($values->image['name'], $values->image['tmp_name']);
			return $success;
		} else {
			//  error
            SGL::raiseError('Incorrect parent node id or module id passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
			return false;
		}
	}
	
	/**
	* Update image information
	* 
	* @access public
	* @param int $slider_id
	* @param array $values values of image
	* @return boolean
	*/
	 
	 function update($slider_id, &$values){
	 	SGL::logMessage(null, PEAR_LOG_DEBUG);
		// generate a unique image name
		if(isset($values->image['name'])){
			$values->image['name'] = $this->generateUniqueFileName($values->image['name']);
		}
		
		$slider = DB_DataObject::factory($this->conf['table']['slider']);
        $slider->get($values->slider_id);
        $slider->setFrom($values);
		$slider->last_updated = SGL_Date::getTime(true);
		
		if(isset($values->image['name'])){
			$slider->image_name      = $values->image['name'];
		}
		
        $success = $slider->update();
        
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
            foreach ($aDeletes as $key => $slider_id) {
                $slider = DB_DataObject::factory($this->conf['table']['slider']);
                $slider->get($slider_id);
				if($slider->image_name){
					$this->deleteImage($slider->image_name);
				}
                $slider->delete();
                unset($slider);
           	}
            return true;
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .__FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
	}
	
	/**
    * get a field of this image .
    *
    * @access public
    * @param  int  $slider_id index of an image
    * @param  int  $field_name  Determine field name to fetch it`s value 
    * @return string
    */
	function get($slider_id, $field_name){
		SGL::logMessage(null, PEAR_LOG_DEBUG);

		if(intval($slider_id)>0 && $field_name!='') {
		    if(array_key_exists($field_name, $this->_tableStructure)) {
			    $slider = DB_DataObject::factory($this->conf['table']['slider']);
				$slider->get(intval($slider_id));
				//Add quote to field name
				$field_name = $slider->escape($field_name);

				$field_value = $slider->$field_name;
				return $field_value;
			}
		} else {
			//  error
            SGL::raiseError('slider id or diels name is not valid ', SGL_ERROR_INVALIDARGS);
			return false;
		}
	}
	
	/**
    * get all of image's fields.
    *
    * @access public
    * @param  int  $slider_id index of an image
    * @return array
    */
	function getImageInfo($slider_id)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);

		if(intval($slider_id)>0) {
			//Get slider information
			$slider  = DB_DataObject::factory($this->conf['table']['slider']);
			$slider->get(intval($slider_id));
			return $slider->toArray();
		} else {
			//  error
            SGL::raiseError('Incorrect slider id passed to ' . __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
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
			$slider = DB_DataObject::factory($this->conf['table']['slider']);
			$aSliders = array();
			foreach($filters as $key => $value) {
				
				switch($key) {
					case "select" :
						if(is_string($value)) {
							$slider->selectAdd($value);
						} elseif(is_array($value)) {
							foreach($value as $v) {
								$slider->selectAdd($v);
							}
						}
					    break;
					case "where"  :
                        if(is_string($value)) {
							$slider->whereAdd($value);
						} elseif(is_array($value)) {
							foreach($value as $v) {
								$slider->whereAdd($v);
							}
						}
					    break;
					case "limit"  :
                        if(is_int($value)) {
                        	$slider->limit($value);
                        } elseif(is_array($value)) {
                        	$slider->limit($value[0],$value[1]);
                        }
					    break;
					case "orderBy" :
                        if(is_string($value)) {
                        	$slider->orderBy($value);
                        }
						break;
					case "groupBy" :
                        if(is_string($value)) {
                        	$slider->groupBy($value);
                        }
						break;
				}
			}

			$slider->find();

			while($slider->fetch()) {
				$aSliders[] = $slider->toArray();
			}
			//exit;
            //Return categories
			return $aSliders;
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

		$sliders = array();

		//Generate category tree
		$sliders = $this->getImages($filters);
			
		
		$aSliders = array();
		foreach($sliders as $image) {
			$aSliders[$image['slider_id']] = $image['title'];
		}

		return SGL_Output::generateSelect($aSliders, $selectedCat);
		
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

			$slider = DB_DataObject::factory($this->conf['table']['slider']);
			if(is_array($filters) && count($filters)>0) {
				foreach($filters as $key=>$value) {
					$slider->$key = $value;
				}
			}
			return $slider->count();
	}
}
?>
