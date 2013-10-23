<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2010, Demian Turner                                         |
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
// | Seagull 1.1                                                               |
// +---------------------------------------------------------------------------+
// | Output.php                                                                |
// +---------------------------------------------------------------------------+
// | Author: Sina Saderi <sina.saderi@gmail.com>                               |
// +---------------------------------------------------------------------------+

require_once SGL_CORE_DIR . '/Delegator.php';

class ContentOutput
{
    function makeTag($tag)
    {
    	$aOptions = unserialize($tag);
    	
    	if($aOptions['selects'])
    	{
    		$output->items = $this->makeSelects($aOptions['selects']);
    	}
    	
    	if($aOptions['multipleselects'])
    	{
    		$output->items = $this->makeMultipleselects($aOptions['multipleselects']);
    	}
    	
    	if($aOptions['checkes'])
    	{
    		$output->items = $this->makeRadios($aOptions['checkes']);
    	}
    	
    	if($aOptions['radios'])
    	{
    		$output->items = $this->makeRadios($aOptions['radios']);
    	}
    	
    	if(!$aOptions["width"])
    	{
    		$aOptions["width"] = 8;
    	}
    	
    	$output->options = $aOptions;
    	$tagType = $this->_renderTemplate($output, 'admin_type/options/tag_'.$aOptions['type'].'.html');
    	
    	
    	$tag = '<li>
					<div class="form-group tag-list tagrows">
					  <label class="col-2 control-label">'.$aOptions["label"].' </label>
					  <div class="col-'.$aOptions["width"].'" >'.$tagType.'</div>
					  <div class="actions" style="float: right; margin-right: 0px; display:none;">
						<button type="button" class="btn btn-default tagOptions" rel="popover" data-trigger="click" data-content="s" data-html="true" data-original-title="d"><span class="glyphicon glyphicon-edit"></span></button>
						<button type="button" class="btn btn-default tagDelete"><span class="glyphicon glyphicon-remove"></span></button>
					  </div>
					</div>
				</li>';
    	return $tag;
    }
    
    function makeRadios($str)
    {
    	$aItems = explode("~~||~~",$str);
    	$items = "";
    	foreach($aItems as $iValue){
    		$vValue = explode("|",$iValue);
    		$checked = "";
    		if($vValue[0]){
    			$checked = "checked";
    		}
    		$items .= '<label class="radio-inline"><input type="radio" $checked title="radio"> '. $vValue[1] .' </label>';
    	}
    	return $items;
    }
    
	function makeCheckes($str)
	{
    	$aItems = explode("~~||~~",$str);
    	$items = "";
    	foreach($aItems as $iValue){
    		$vValue = explode("|",$iValue);
    		$checked = "";
    		if($vValue[0]){
    			$checked = "checked";
    		}
    		$items .= '<label class="checkbox-inline"><input type="checkbox" $checked title="checkbox"> '. $vValue[1] .' </label>';
    	}
    	return $items;
    }
    
    
    
	function makeMultipleselects($str)
	{
    	$aItems = explode("~~||~~",$str);
    	$items = "";
    	foreach($aItems as $iValue){
    		$vValue = explode("|",$iValue);
    		$selected = "";
    		if($vValue[0]){
    			$selected = "selected";
    		}
    		$items .= '<option value="' . $vValue[1] . '" ' . $selected . '>'. $vValue[1] .'</option>';
    	}
    	return $items;
    }
    
	function makeSelects($str)
	{
    	$aItems = explode("~~||~~",$str);
    	$items = "";
    	foreach($aItems as $iValue){
    		$vValue = explode("|",$iValue);
    		$selected = "";
    		if($vValue[0]){
    			$selected = "selected";
    		}
    		$items .= '<option value="' . $vValue[1] . '" ' . $selected . '>'. $vValue[1] .'</option>';
    	}
    	return $items;
    }
    
    function setDefaultValue($userVal, $defaultValue)
    {
    	if($userVal) 
    		return $userVal;
    	else
    		return $defaultValue;
    }
    
	protected function _renderTemplate($output, $aParams)
    {
        if (!is_array($aParams)) {
            $aParams = array('masterTemplate' => $aParams);
        }
        $o = clone $output;
        $o->moduleName = "content";
        foreach ($aParams as $k => $v) {
            $o->$k = $v;
        }
        $view = new SGL_HtmlSimpleView($o);
        return $view->render();
    }
}
?>
