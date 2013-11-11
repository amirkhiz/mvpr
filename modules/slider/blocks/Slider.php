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
// | Slider.php                                                                |
// +---------------------------------------------------------------------------+
// | Author: Author: Ali Agha <alizowghi@gmail.com>                            |
// |         & Siavash AmirKhiz <amirkhiz@gmail.com>                           |
// +---------------------------------------------------------------------------+
include_once 'DB/DataObject.php';
/**
 * Creates static html blocks.
 *
 * @package block
 * @author  Author: Ali Agha <alizowghi@gmail.com> & Siavash AmirKhiz <amirkhiz@gmail.com>   
 * @version 1.0
 */
class Slider_Block_Slider
{
 	var $template     = 'slider1.html';
    var $templatePath = 'slider';
    
    function init(&$output, $blockId, &$aParams)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
            
        return $this->getBlockContent($aParams);
    }

    function getBlockContent(&$aParams)
    {
        $blockOutput    = new SGL_Output();
        return $this->process($aParams, $blockOutput);
    }
    
	function process($aParams, $output)
    {
 		 SGL::logMessage(null, PEAR_LOG_DEBUG);
		static $conf;
		static $webRoot;
		$c = &SGL_Config::singleton();
            $conf  = $c->getAll();
        if(@$conf['table']['slider'] !=''){
        $sliderList = DB_DataObject::factory(@$conf['table']['slider']);

        $sliderList->orderBy('order_id');
        $result = $sliderList->find();
        $asliders  = array();
        if ($result > 0) {
            while ($sliderList->fetch()) {
            	
            	//  detect if trans2 support required
	        	if (SGL_Config::get('translation.container') == 'db' || SGL_Config::get('translation.multiLanguageContents')) {
		        	$this->trans = &SGL_Translation::singleton();
		            $language = SGL_Translation::getLangID();
		            $sliderTitle = $this->trans->get($sliderList->slider_id, 'sliderTitle', $language);
		            $sliderAnswer = $this->trans->get($sliderList->slider_id, 'sliderAnswer', $language);
		            if (!(empty($sliderTitle)))
		            	$sliderList->title = $sliderTitle;
	        	}
            	
                $asliders[] = clone($sliderList);
            }
        }
        
    
        // use moduleName for template path setting
        $output->moduleName     = $this->templatePath;
        $output->masterTemplate = $this->template;
		$output->results = $asliders;
		$output->conf=$conf;
		
		$output->sliderDir = SGL_PATH . "/modules/slider";
        
        $view = new SGL_HtmlSimpleView($output);
        return $view->render();
        }
    }
    
    
}
?>
