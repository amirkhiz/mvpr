<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2008, Demian Turner                                         |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | o Redistributions of source code must retain the above copyright          |
// |   notice, this list of conditions and the following disclaimer.           |
// | o Redistributions in binary form must reproduce the above copyright       |
// |   notice, this list of conditions and the following disclaimer in the     |
// |   documentation and/or other materials provided with the distribution.    |
// | o The names of the authors may not be used to endorse or promote          |
// |   products derived from this software without specific prior written      |
// |   permission.                                                             |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Seagull 0.6                                                               |
// +---------------------------------------------------------------------------+
// | sliderMgr.php                                                             |
// +---------------------------------------------------------------------------+
// | Author: Ali Agha <alizowghi@gmail.com>                                    |
// |         & Siavash AmirKhiz <amirkhiz@gmail.com>                           |
// +---------------------------------------------------------------------------+

require_once 'DB/DataObject.php';
require_once SGL_MOD_DIR . '/slider/classes/SliderMgr.php';
include_once SGL_MOD_DIR  . '/slider/classes/Slider.php';

/**
 * Allow admins to manage sliders.
 *
 * @package slider
 * @author  Demian Turner <demian@phpkitchen.com>
 * @version $Revision: 1.26 $
 * @since   PHP 4.1
 */
class AdminsliderMgr extends sliderMgr
{

    var $cSlider;
    
    function AdminsliderMgr()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle = 'slider Manager';
        $this->template  = 'sliderList.html';

        $this->_aActionsMapping =  array(
            'add'           => array('add'),
            'insert'        => array('insert', 'redirectToDefault'),
            'edit'          => array('edit'),
            'update'        => array('update', 'redirectToDefault'),
            'reorder'       => array('reorder'),
            'reorderUpdate' => array('reorderUpdate', 'redirectToDefault'),
            'delete'        => array('delete', 'redirectToDefault'),
            'list'          => array('list'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        parent::validate($req, $input);
        $input->sliderId    = $req->get('frmSliderId');
        $input->items     	= $req->get('_items');
        $input->slider      = (object)$req->get('slider');
        $input->gimage1 	= $req->get('gimage1');

        // Misc.
        $input->submitted   = $req->get('submitted');
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->aDelete     = $req->get('frmDelete');
        $input->isAdd       = $req->get('isadd');
		$input->sliderLang	= $req->get('frmSliderLang');
    
        if ($input->submitted && in_array($input->action, array('insert'))) {
            if (empty($input->slider->title)) {
                $aErrors['title'] = 'Please fill in a title';
            }
            if(empty($input->gimage1['name'])){
            	$aErrors['gimage1'] = 'Please select a picture';
            }
        }    
        if ($input->submitted && in_array($input->action, array('update'))) {
            if (empty($input->slider->title)) {
                $aErrors['title'] = 'Please fill in a title';
            }
        } 
        $this->cSlider = Slider::singleton();
            
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->error    = $aErrors;
            $input->template = 'sliderEdit.html';
            $this->validated = false;

            // save currect title
            if ($input->action == 'insert') {
                $input->pageTitle .= ' :: Add';
                $input->isAdd     = true;
            } elseif ($input->action == 'update') {
                $input->pageTitle .= ' :: Edit';
            }
        }
        
    	//check if translation is on db or on multi language
    	if ($this->conf['translation']['container'] == 'db' || $this->conf['translation']['multiLanguageContents']) {
            $this->trans = & SGL_Translation::singleton('admin');
            if (is_null($input->sliderLang)) {
                $input->sliderLang = SGL_Translation::getFallbackLangID();
            }
        }
    }

    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template  = 'sliderEdit.html';
        $output->action    = 'insert';
        $output->pageTitle = $this->pageTitle . ' :: Add';
        $output->isAdd     = true;
        $output->sliderLang = SGL_Translation::getFallbackLangID();
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        SGL_DB::setConnection();
        //  get new order number
        $slider = DB_DataObject::factory($this->conf['table']['slider']);
        $slider->selectAdd();
        $slider->selectAdd('MAX(order_id) AS new_order');
        $slider->groupBy('order_id');
        $maxItemOrder = $slider->find(true);
        unset($slider);

        //echo $input->gimage1['name'];
        if(isset($input->gimage1['name']) && $input->gimage1['name'] != ""){
        	$input->gimage1['name'] = $this->cSlider->generateUniqueFileName($input->gimage1['name']);
        	$this->cSlider->uploadImage($input->gimage1['name'], $input->gimage1['tmp_name']);
		}


        //  insert record
        $slider = DB_DataObject::factory($this->conf['table']['slider']);
        $slider->setFrom($input->slider);
        $slider->slider_id = $this->dbh->nextId('slider');
        $slider->last_updated = $slider->date_created = SGL_Date::getTime(true);
        $slider->order_id = $maxItemOrder + 1;
		
		if(isset($input->gimage1['name'])){
        	$slider->image1	 = $input->gimage1['name'];
        }
        
        $success = $slider->insert();
        
    	//Add translation in database
    	if ($this->conf['translation']['container'] == 'db' || $this->conf['translation']['multiLanguageContents']) {
    		$lang = SGL_Translation::getFallbackLangID();
        	$this->trans->add($slider->slider_id, 'sliderTitle', array($lang => $input->slider->title));
    	}
        
        if ($success) {
            SGL::raiseMsg('slider saved successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('There was a problem inserting the record',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template  = 'sliderEdit.html';
        $output->action    = 'update';
        $output->pageTitle = $this->pageTitle . ' :: Edit';
        $slider = DB_DataObject::factory($this->conf['table']['slider']);

        //  get slider data
        $slider->get($input->sliderId);
        $slider->title_original = $slider->title;
        
        $output->slider = $slider;
        
    	$output->multiLanguageContents = false;
        //  translation support if enabled
        if ($this->conf['translation']['container'] == 'db' || $this->conf['translation']['multiLanguageContents']) {
            $installedLanguages = $this->trans->getLangs();

            $output->availableLangs = $installedLanguages;
            $output->sliderLang = $sliderLang = (!empty($input->sliderLang))
                ? $input->sliderLang
                : SGL_Translation::getLangID();
                
            $output->fullSliderLang      = $installedLanguages[$sliderLang];
			$output->multiLanguageContents = true;
			if ($output->sliderLang != SGL_Translation::getFallbackLangID()){
				$sliderTitle = $this->trans->get($output->slider->slider_id,
	                    'sliderTitle', $output->sliderLang);
				$output->slider->title = $sliderTitle;
			}
        }
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
    	//  update translations
        if ($this->conf['translation']['container'] == 'db') {
        	if ($input->slider->slider_id) {
        		$ok = $this->trans->add($input->slider->slider_id, 'sliderTitle', array($input->sliderLang => $input->slider->title));
        		if ($input->sliderLang != SGL_Translation::getFallbackLangID()){
        			$input->slider->title = $input->slider->title_original;
        		}
            }
        }

        $slider = DB_DataObject::factory($this->conf['table']['slider']);
        $slider->get($input->slider->slider_id);
        $slider->setFrom($input->slider);
        $slider->last_updated = SGL_Date::getTime(true);
        
        if(!empty($input->gimage1['name']) ){
            $input->gimage1['name'] = $this->cSlider->generateUniqueFileName($input->gimage1['name']);
        	$this->cSlider->uploadImage($input->gimage1['name'], $input->gimage1['tmp_name']);
        }
        
        if($input->gimage1['name'] !=''){
        	$slider->image1	 = $input->gimage1['name'];
        }
        
        $success = $slider->update();
        if ($success) {
            SGL::raiseMsg('slider updated successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('There was a problem updating the record',
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $sliderId) {
                $slider = DB_DataObject::factory($this->conf['table']['slider']);
                $slider->get($sliderId);
                unlink(SGL_WEB_ROOT.$slider->image1);
                $slider->delete();
                unset($slider);
                
            	//  Remove block translation
                if ($this->conf['translation']['container'] == 'db' || $this->conf['translation']['multiLanguageContents']) {
                    $this->trans->remove($sliderId, 'sliderTitle');
                	$this->trans->remove($sliderId, 'sliderAnswer');
                }
                
            }
            SGL::raiseMsg('slider deleted successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }

    function _cmd_reorder(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->pageTitle = $this->pageTitle . ' :: Reorder';
        $output->template  = 'sliderReorder.html';
        $output->action    = 'reorderUpdate';
        $sliderList = DB_DataObject::factory($this->conf['table']['slider']);
        $sliderList->orderBy('order_id');
        $result = $sliderList->find();
        if ($result > 0) {
            $asliders = array();
            while ($sliderList->fetch()) {
                $asliders[$sliderList->slider_id] = SGL_String::summarise($sliderList->title, 40);
            }
            $output->asliders = $asliders;
        }
    }

    function _cmd_reorderUpdate(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        if (!empty($input->items)) {
            $aNewOrder = explode(',', $input->items);
            //  reorder elements
            $pos = 1;
            foreach ($aNewOrder as $sliderId) {
                $slider = DB_DataObject::factory($this->conf['table']['slider']);
                $slider->get($sliderId);
                $slider->order_id = $pos;
                $success = $slider->update();
                unset($slider);
                $pos++;
            }
            SGL::raiseMsg('sliders reordered successfully', true, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('Incorrect parameter passed to ' . __CLASS__ . '::' .
                __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        $output->template = 'sliderList.html';
        $query = "SELECT * FROM {$this->conf['table']['slider']} ORDER BY order_id";

        $limit = $_SESSION['aPrefs']['resPerPage'];
        $pagerOptions = array(
            'mode'     => 'Sliding',
            'delta'    => 3,
            'perPage'  => $limit,
            'spacesBeforeSeparator' => 0,
            'spacesAfterSeparator'  => 0,
            'curPageSpanPre'        => '<span class="currentPage">',
            'curPageSpanPost'       => '</span>',
        );
        $aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);

        //  determine if pagination is required
        $output->aPagedData = $aPagedData;
        if (is_array($aPagedData['data']) && count($aPagedData['data'])) {
            $output->pager = ($aPagedData['totalItems'] <= $limit) ? false : true;
        }

        //echo "<pre>"; print_r($output->aPagedData); echo "</pre>"; exit;

        $output->addOnLoadEvent("switchRowColorOnHover()");
    }
}

?>
