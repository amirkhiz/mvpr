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

class ProductOutput
{
	var $addition = array();
    function loadTag($tag, $addition = array())
    {
    	$this->addition = $addition;
    	$aOptions = unserialize($tag);
    	
    	if($aOptions['selects'])
    	{
    		$output->items = $this->makeSelects($aOptions['selects'], $aOptions['content_type_mapping_id']);
    	}
    	
    	if($aOptions['multipleselects'])
    	{
    		$output->items = $this->makeMultipleselects($aOptions['multipleselects'], $aOptions['content_type_mapping_id']);
    	}
    	
    	if($aOptions['checkes'])
    	{
    		$output->items = $this->makeCheckes($aOptions['checkes'], $aOptions['content_type_mapping_id']);
    	}
    	
    	if($aOptions['radios'])
    	{
    		$output->items = $this->makeRadios($aOptions['radios'], $aOptions['content_type_mapping_id']);
    	}
    	
    	if($aOptions['type'] == "textbox"){
    		if(count($addition)){
    			$aOptions['deval'] = $this->finTxtValue($aOptions['content_type_mapping_id']);
    		}
    	}
    	
    	if(!$aOptions["width"])
    	{
    		$aOptions["width"] = 8;
    	}
    	
    	$output->options = $aOptions;
    	
    	$tagType = $this->_renderTemplate($output, 'type/options/tag_'.$aOptions['type'].'.html');
    	
    	if($aOptions['type'] == "textbox" and $aOptions['postval'] != ""){
    		$aOptions["width"] -= 1;
    		$post = '<label class="col-lg-2 control-label" style="text-align: left;padding-left: 0px;">'.$aOptions['postval'].' </label>';
    	}
    	if($aOptions['type'] == "textbox" and $aOptions['preval'] != ""){
    		$aOptions["width"] -= 1;
    		$pre = '<label class="col-lg-1 control-label" style="padding-right: 0px;">'.$aOptions['preval'].' </label>';
    	}
    	
    	$tag = '
				<div class="form-group">
				  <label class="col-lg-3 control-label">'.$aOptions["label"].' </label>
				  '.$pre.'
				  <div class="col-lg-'.$aOptions["width"].'" >'.$tagType.'</div>
				  '.$post.'
				</div>
				';
    	return $tag;
    }
    
    function makeRadios($str, $mId)
    {
    	$aItems = $this->syncIfEdit($str, $mId);
    	$items = "";
    	foreach($aItems as $iValue){
    		$vValue = explode("|",$iValue);
    		$checked = "";
    		if($vValue[0]){
    			$checked = "checked";
    		}
    		$items .= '<label class="radio-inline"><input type="radio" name="product[prop]['.$mId.'][]" title="radio" '.$checked.' value="'. $vValue[1] .'"> '. $vValue[1] .' </label>';
    	}
    	return $items;
    }
    
	function makeCheckes($str, $mId)
	{
		$aItems = $this->syncIfEdit($str, $mId);
    	//$aItems = explode("~~||~~",$str);
    	$items = "";
    	foreach($aItems as $iValue){
    		$vValue = explode("|",$iValue);
    		$checked = "";
    		if($vValue[0]){
    			$checked = "checked";
    		}
    		$items .= '<label class="checkbox-inline"><input type="checkbox" name="product[prop]['.$mId.'][]"  title="checkbox" '.$checked.' value="'. $vValue[1] .'"> '. $vValue[1] .' </label>';
    	}
    	return $items;
    }
    
    
    
	function makeMultipleselects($str, $mId)
	{
    	$aItems = $this->syncIfEdit($str, $mId);
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
    
	function makeSelects($str, $mId)
	{
    	$aItems = $this->syncIfEdit($str, $mId);
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
    
    function finTxtValue($mId){
    	foreach($this->addition as $key => $value)
    	{
    		if($value->content_type_mapping_id == $mId){
    			return $value->value;
    		}
    	}
    }
    
    function syncIfEdit($str, $mId)
    {
    	$aItems = explode("~~||~~", $str);
    	if(count($this->addition)){
			foreach($aItems as $iKey => $iValue)
			{
				$x = 0;
				$aItemsValue = substr($iValue, 2);
		    	foreach($this->addition as $key => $value)
		    	{
		    		if($mId == $value->content_type_mapping_id){
						$f = $aItemsValue . "---" ;
						if($value->value == $aItemsValue)
						{
							$x = 1;
						}
		    		}
		    	}
		    	if($x){
		    		$aItems[$iKey] = "1|" . $aItemsValue;
		    	}else{
		    		$aItems[$iKey] = "0|" . $aItemsValue;
		    	}
			}
    	}
    	return $aItems;
    }
    
    function removeDeVal($aItems){
    	foreach($aItems as $key => $value)
    	{
    		$aItems[$key] = substr($value, 2);
    	}
    	return $aItems;
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
        $o->moduleName = "product";
        foreach ($aParams as $k => $v) {
            $o->$k = $v;
        }
        $view = new SGL_HtmlSimpleView($o);
        return $view->render();
    }
}
?>
