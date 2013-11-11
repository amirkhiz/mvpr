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
// | Author: Siavash AmirKhiz <amirkhiz@gmail.com>                             |
// +---------------------------------------------------------------------------+
include_once 'DB/DataObject.php';
/**
 * Creates static html blocks.
 *
 * @package block
 * @author  Author: Siavash AmirKhiz <amirkhiz@gmail.com>   
 * @version 1.0
 */
class Product_Block_Slider extends SGL_Manager
{
	
	var $webRoot = SGL_BASE_URL;
	
	var $template     = 'slider.html';
    var $templatePath = 'product';
	
	function init(&$output, $block_id, &$aParams)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);
	
		return $this->getBlockContent($output, $aParams);
	}
	
	function getBlockContent(&$output, &$aParams)
	{
		SGL::logMessage(null, PEAR_LOG_DEBUG);
	
		$blockOutput = new SGL_Output();
		$blockOutput->webRoot = $this->webRoot;
		
		// get All Products
		$blockOutput->productsItem = $this->retrieveAll();
	
		return $this->process($blockOutput);
	}
	
	/**
	 * Retrieves all Products in gallery table.
	 *
	 * @access  public
	 * @return  array $aItems array of tabs objects
	 */
	function retrieveAll()
	{
		$query = "
				SELECT p.*, pi.title AS image
        		FROM {$this->conf['table']['product']} AS p
        		JOIN {$this->conf['table']['product_image']} AS pi
        		ON pi.product_id = p.product_id
       			GROUP BY p.product_id
       			ORDER BY p.product_id DESC
			";
		$aItems = $this->dbh->getAll($query);
		
		//echo '<pre>';print_r($aItems);echo '</pre>';die;
	
		if (!DB::isError($aItems)) {
			return $aItems;
		} else {
			SGL::raiseError('perhaps no Tab exist', SGL_ERROR_NODATA);
		}
	}
	
	function process(&$output)
	{
		// use moduleName for template path setting
		$output->moduleName     = $this->templatePath;
		$output->masterTemplate = $this->template;
		
		$view = new SGL_HtmlSimpleView($output);
		return $view->render();
	}
	
}
?>
