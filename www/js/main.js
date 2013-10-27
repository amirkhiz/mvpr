$(document).ready(function() {
	
	$("#form-group").validationEngine();
	$(".chosen").chosen();
	
	minPrice = parseInt(jQuery("#minPrice").val());
	maxPrice = parseInt(jQuery("#maxPrice").val());
	
	$("#Price").slider({ 
		from: minPrice, 
		to: maxPrice, 
		step: 1, 
		dimension: '&nbsp;', 
		skin: 'round',
		callback: function( value )
		{
			searchProduct();
		}
	});
	
	$('#searchCur').change(function()
	{
		searchProduct();
	});
	
	$('.multiple').multiselect({
		selectedList: 2,
		header: false
	});
	
	
	$('.multiple').change(function()
	{
		searchProduct();
	});
	
	function searchProduct()
	{
		var prices		= $('#Price').val();
		var currency	= $('#searchCur').val();
		var aSelected 	= new Array();
		var aBrand		= new Array();
		var filter		= '';
		
		$('.multiple').each(function(){
			if ($(this).attr('fieldId') == '0'){
				aThisSelect = $(this).val();
				aBrand.push.apply(aBrand, aThisSelect);
			}
			else{
				aThisSelect = $(this).val();
				aSelected.push.apply(aSelected, aThisSelect);
			}
			if ($(this).val()){
				filter += '<b>' + $(this).attr('selTitle') + '</b>: ';
				filter += '<i>' + $(this).children("option:selected").text() + '</i>,  ';
			}
		});
		
		console.log(filter);
		$("#filters").html(filter);
		
		var catId = $('#catId').val();

		if (aSelected.length != 0)
			var ajaxurl  = makeUrl({module: "product", action: "search", frmCAddition:aSelected, frmPrices:prices, frmCur:currency, frmBrands:aBrand});
		else
			var ajaxurl  = makeUrl({module: "product", action: "search", frmCategoryID:catId, frmPrices:prices, frmCur:currency, frmBrands:aBrand});
		
		$.ajax({
			url: ajaxurl,
			cache: false,
            type: 'POST',
			success: function(data){
				//console.log(data);
				$("#searchResult").html(data);
			},
			error: function(){
				console.log('Error');
			}
		});
	}
	

	var myFields=new Array("label","place","deval","help","height","width","rows","valid","mask","pattern", "checkes");
	var callfunc = true;
	
	function orderTags(){
		var order = 1;
		$("ul#added-tags").find(".h-order").each(function(){
			$(this).val(order);
			order++;
		});
	}
	
	$('body').on('click', '.sinap', function () {
		thisDiv = $(this).parent().parent();
		values = '<input type="checkbox" style="display: inline;"> ';
		values += '<input type="text" class="form-control input-sm" style="width: 180px; display: inline;" placeholder="New label" value=""> ';
		values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-plus sinap"></span></a> ';
		var newCheck = '<div class="option-values">' + values + '</div>'
		thisDiv.parent().append(newCheck);
		$(this).parent().remove();
		thisDiv.append('<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-minus sinam"></span></a>');
	});
	$('body').on('click', '.sinam', function () {
		$(this).parent().parent().remove();
	});
	
	$('body').on('click', '.rsinap', function () {
		thisDiv = $(this).parent().parent();
		values = '<input type="radio" name="radioption" style="display: inline;"> ';
		values += '<input type="text" class="form-control input-sm" style="width: 180px; display: inline;" placeholder="New label" value=""> ';
		values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-plus rsinap"></span></a> ';
		var newRadio = '<div class="option-values">' + values + '</div>'
		thisDiv.parent().append(newRadio);
		$(this).parent().remove();
		thisDiv.append('<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-minus rsinam"></span></a>');
	});
	$('body').on('click', '.rsinam', function () {
		$(this).parent().parent().remove();
	});
	
	$('body').on('mouseover', '.tagrows', function () {
		$(this).removeClass('tag-list').addClass('tag-listHover');
		$(this).find(".actions").css("display","block");
	});
	
	$('body').on('mouseout', '.tagrows', function () {
		if (!($(this).find(".tagOptions").next('div.popover:visible').length)){
			$(this).removeClass('tag-listHover').addClass('tag-list');
			$(this).find(".actions").css("display","none");
		}
	});
	$('body').on('click', '.tagDelete', function () {
		$(this).parent().parent().parent().remove();
	});
	
	$('body').on('click', '.f_button_dataProductPrice', function () {
	     $(this).parent().children(":first").trigger("click");
	 });
	
	$('body').on('change', '.file_dataProductPrice', function () {
		 $(this).parent().find(".f_text_dataProductPrice").val($(this).val());
	 });
	
	$('body').on('click', '.popover .closeOption', function () {
		$('.tagOptions').popover('hide');
    	$('.sortable').sortable('enable');
	});
	
	$('body').on('click', '.popover .saveOption', function () {
		saveTagOptions(this);
		
    	$('.tagOptions').popover('hide');
    	$('.sortable').sortable('enable');
    	
    	orderTags();
	});
	function tagOptionPopover(){
		$('.tagOptions').on('shown.bs.popover', function () {
			if(callfunc){
				loadTagOptions(this);
				callfunc = false;
			}
		})
		
		$('.tagOptions').on('hidden.bs.popover', function () {
			callfunc = true;
			$('.sortable').sortable('enable');
		});
	}
	
	function tagOptionClick(){
		$('.tagOptions').click(function(){
			parentDiv = $(this).parent().parent();
			elementType = parentDiv.find("input, textarea, select").attr("title");
			optionHtml = $("."+elementType+"Option");
			popTitle = optionHtml.find(".title-popover").val();
			$(this).attr("data-content",optionHtml.html());
			$(this).attr("data-original-title",popTitle);
			$('.sortable').sortable('disable');
		}).popover({
		    placement : 'left',
		    trigger: 'click',
		});
	}
	tagOptionClick();
	$('body').on('click', '#newtag li a', function () {
		tagType = $(this).attr("alt");
		var $input = $("."+tagType+"Tag").html();
		var newDiv = $(".newDiv").html();
		newDiv = newDiv.replace("$inputing",$input);
		
		$("#added-tags").append(newDiv);	
		
		orderTags();
		
		$('.datepicker').datepicker({
			language: 'tr',
			autoclose: true,
	    });
		$('.sortable').sortable({
			update: function( event, ui ) {
				orderTags();
			}
		});
		tagOptionPopover();
		tagOptionClick();
	});
	tagOptionPopover();
	function loadTagOptions($this){
		$($this).parents('li').find("input[type=hidden]").each(function(){
			myfields = $(this).attr("class").replace("h-","");
			var fieldOption = $($this).parents('li').find(".h-"+myfields).val();
			switch(myfields){
				case 'width':
					parentDiv.find(".cu-"+myfields).html(fieldOption);
					$( ".wslide" ).slider( "value", $($this).parents('li').find(".h-width").val() );
				break;
				
				case 'multipleselects':
					var aOptions = new Array;
					aOptions = fieldOption.split("~~||~~");
					for(var j = 0; j < aOptions.length; j++){
						aOps = aOptions[j].split("|");
						if(aOps[0] == 1){
							values = '<input type="checkbox" checked style="display: inline;" value="'+aOps[1]+'"> ';
						}else{
							values = '<input type="checkbox" style="display: inline;" value="'+aOps[1]+'"> ';
						}
						values += '<input type="text" class="form-control input-sm" style="width: 180px; display: inline;" placeholder="{translate(#New label#)}" value="'+aOps[1]+'"> ';
						if(j + 1 != aOptions.length){
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-minus sinam"></span></a> ';
						}else{
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-plus sinap"></span></a> ';
						}
						parentDiv.find(".cu-"+myfields).append('<div class="option-values">' + values + '</div>');
					}
				break;
				
				case 'selects':
					var aOptions = new Array;
					aOptions = fieldOption.split("~~||~~");
					for(var j = 0; j < aOptions.length; j++){
						aOps = aOptions[j].split("|");
						if(aOps[0] == 1){
							values = '<input type="radio" checked name="radioption" style="display: inline;" value="'+aOps[1]+'"> ';
						}else{
							values = '<input type="radio" name="radioption" style="display: inline;" value="'+aOps[1]+'"> ';
						}
						values += '<input type="text" class="form-control input-sm" style="width: 180px; display: inline;" placeholder="{translate(#New label#)}" value="'+aOps[1]+'"> ';
						if(j + 1 != aOptions.length){
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-minus rsinam"></span></a> ';
						}else{
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-plus rsinap"></span></a> ';
						}
						parentDiv.find(".cu-"+myfields).append('<div class="option-values">' + values + '</div>');
					}
				break;
				
				case 'radios':
					var aOptions = new Array;
					aOptions = fieldOption.split("~~||~~");
					for(var j = 0; j < aOptions.length; j++){
						aOps = aOptions[j].split("|");
						
						if(aOps[0] == 1){
							values = '<input type="radio" checked name="radioption" style="display: inline;" value="'+aOps[1]+'"> ';
						}else{
							values = '<input type="radio" name="radioption" style="display: inline;" value="'+aOps[1]+'"> ';
						}
						values += '<input type="text" class="form-control input-sm" style="width: 180px; display: inline;" placeholder="{translate(#New label#)}" value="'+aOps[1]+'"> ';
						if(j + 1 != aOptions.length){
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-minus rsinam"></span></a> ';
						}else{
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-plus rsinap"></span></a> ';
						}
						
						parentDiv.find(".cu-"+myfields).append('<div class="option-values">' + values + '</div>');
						
					}
					
				break;
				
				case 'checkes':
					var aOptions = new Array;
					aOptions = fieldOption.split("~~||~~");
					for(var j = 0; j < aOptions.length; j++){
						aOps = aOptions[j].split("|");
						if(aOps[0] == 1){
							values = '<input type="checkbox" checked style="display: inline;" value="'+aOps[1]+'"> ';
						}else{
							values = '<input type="checkbox" style="display: inline;" value="'+aOps[1]+'"> ';
						}
						values += '<input type="text" class="form-control input-sm" style="width: 180px; display: inline;" placeholder="{translate(#New label#)}" value="'+aOps[1]+'"> ';
						if(j + 1 != aOptions.length){
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-minus sinam"></span></a> ';
						}else{
							values += '<a style="margin-bottom:10px; cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-plus sinap"></span></a> ';
						}
						parentDiv.find(".cu-"+myfields).append('<div class="option-values">' + values + '</div>');
					}
				break;
				
				case 'rows':
					parentDiv.find(".cu-"+myfields).html(fieldOption);
					$( ".rslide" ).slider( "value", $($this).parents('li').find(".h-rows").val() );
				break;
				
				case 'size':
					parentDiv.find(".cu-"+myfields).html(fieldOption);
					$( ".ssize" ).slider( "value", $($this).parents('li').find(".h-size").val() );
				break;
				
				default:
					parentDiv.find(".cu-"+myfields).val(fieldOption);
			} //end for switch
		});
	}
	
	
	function saveTagOptions($this){
		$($this).parents('li').find("input[type=hidden]").each(function(){
			myfields = $(this).attr("class").replace("h-","");
			var optionField = $($this).closest('div[class="options"]').find(".cu-"+myfields).val();
			
			switch(myfields){
				case "multipleselects":
					var optionField = "";
					$($this).parents('li').find('.added-tag').html("");
					var i = 0;
					var fieldOptions = $($this).closest('div[class="options"]').find(".cu-"+myfields);
					$(fieldOptions).find(".option-values").each(function(){
						i++;
						label =  $(this).find('input[type="text"]').val();
						if($(this).find('input[type="checkbox"]').is(':checked')){
							optionField += "1|" + label + "~~||~~";
							option = '<option value="' + label + '" selected> ' + label + ' </option>';
						}else{
							optionField += "0|" + label + "~~||~~";
							option = '<option value="' + label + '"> ' + label + ' </option>';
						}
						$($this).parents('li').find('.added-tag').append(option);
					});
					optionField = substr(optionField,0,-6);
				break;
			
				case "selects":
					var optionField = "";
					$($this).parents('li').find('.added-tag').html("");
					var i = 0;
					var fieldOptions = $($this).closest('div[class="options"]').find(".cu-"+myfields);
					$(fieldOptions).find(".option-values").each(function(){
						i++;
						label =  $(this).find('input[type="text"]').val();
						if($(this).find('input[type="radio"]').is(':checked')){
							optionField += "1|" + label + "~~||~~";
							option = '<option value="' + label + '" selected> ' + label + ' </option>';
						}else{
							optionField += "0|" + label + "~~||~~";
							option = '<option value="' + label + '"> ' + label + ' </option>';
						}
						$($this).parents('li').find('.added-tag').append(option);
					});
					optionField = substr(optionField,0,-6);
				break;
			
				case "radios":
					var optionField = "";
					$($this).parents('li').find('.added-tags').html("");
					var i = 0;
					var fieldDivs = $($this).closest('div[class="options"]').find(".cu-"+myfields);
					$(fieldDivs).find(".option-values").each(function(){
						i++;
						label =  $(this).find('input[type="text"]').val();
						if($(this).find('input[type="radio"]').is(':checked')){
							optionField += "1|" + label + "~~||~~";
							radio = '<label class="radio-inline"><input type="radio" name="radioption" value="' + label + '" title="radio" checked> ' + label + ' </label>';
						}else{
							optionField += "0|" + label + "~~||~~";
							radio = '<label class="radio-inline"><input type="radio" name="radioption" value="' + label + '" title="radio"> ' + label + ' </label>';
						}
						$($this).parents('li').find('.added-tags').append(radio);
					});
					optionField = substr(optionField,0,-6);
				break;
			
				case "checkes":
					var optionField = "";
					$($this).parents('li').find('.added-tags').html("");
					var i = 0;
					var fieldDivs = $($this).closest('div[class="options"]').find(".cu-"+myfields);
					$(fieldDivs).find(".option-values").each(function(){
						i++;
						label =  $(this).find('input[type="text"]').val();
						if($(this).find('input[type="checkbox"]').is(':checked')){
							optionField += "1|" + label + "~~||~~";
							checkbox = '<label class="checkbox-inline"><input type="checkbox" name="check_'+i+'" value="' + label + '" title="checkbox" checked> ' + label + ' </label>';
						}else{
							optionField += "0|" + label + "~~||~~";
							checkbox = '<label class="checkbox-inline"><input type="checkbox" name="check_'+i+'" value="' + label + '" title="checkbox"> ' + label + ' </label>';
						}
						$($this).parents('li').find('.added-tags').append(checkbox);
					});
					optionField = substr(optionField,0,-6);
				break;
				
				case "label":
					$($this).parents('li').find(".control-label").html(optionField);
				break;
				case "place":
					$($this).parents('li').find('.added-tag').attr("placeholder",optionField);
				break;
				case "deval":
					$($this).parents('li').find('.added-tag').val(optionField);
				break;
				case "help":
					$($this).parents('li').find('span[class="help-block"]').html(optionField);
				break;
				case "height":
					$($this).parents('li').find('.added-tag').removeClass("input-sm");
			    	$($this).parents('li').find('.added-tag').removeClass("input-lg");
			    	$($this).parents('li').find('.added-tag').addClass(optionField);
			    break;
				case "width":
					var optionField = $($this).closest('div[class="options"]').find(".cu-"+myfields).html();
					$($this).parents('li').find('.added-tag').parent().attr("class","col-"+optionField);
				break;
				case "rows":
					var optionField = $($this).closest('div[class="options"]').find(".cu-"+myfields).html();
					$($this).parents('li').find('.added-tag').attr("rows",optionField);
				break;
				case "size":
					var optionField = $($this).closest('div[class="options"]').find(".cu-"+myfields).html();
					$($this).parents('li').find('.added-tag').attr("size",optionField);
				break;
				case "mask":
					help = $($this).parents('li').find('span[class="help-block"]').html();
					if(optionField == ""){
			    		if(typeof(unmask) == "function"){
			    			$($this).parents('li').find('input[class^="added-tag"]').unmask();
			    		}
			    		$($this).parents('li').find('span[class="help-block"]').html(help);
			    	}else{
			    		$($this).parents('li').find('input[class^="added-tag"]').mask(optionField);
			    		$($this).parents('li').find('span[class="help-block"]').html(help + " e.g." + optionField);
			    	}
				break;
				case "blabel":
					$($this).parents('li').find('input[type="button"]').val(optionField);
				break;
			}
			$($this).parents('li').find(".h-"+myfields).val(optionField);
		});
	}
	
	$( ".selectable" ).on( "selectableunselected", function( event, ui ) {
        $(this).find("tr").each(function () {
            $(this).find("input[type=checkbox]").prop('checked', false);
        });
    
        $(this).find(".ui-selected").each(function () {
            $(this).find("input[type=checkbox]").prop('checked', true);
        });
    } );
    
    $(".selectable").on( "selectableselected", function( event, ui ) {
        $(this).find("tr").each(function () {
            $(this).find("input[type=checkbox]").prop('checked', false);
        });
    
        $(this).find(".ui-selected").each(function () {
            $(this).find("input[type=checkbox]").prop('checked', true);
        });
    });
    
    $('.selectable').selectable({
      filter:'tbody tr',
      cancel: 'a',
    });
	
	$("#searchBox").keyup(function(){
		searchVal = $(this).val();
		$url = makeUrl({module: "sms", manager: "sms", action: "searchGroupitemList", searchVal: searchVal});
		$.ajax({
	        url: $url,
	        type: 'POST',
			cache: false,
			success: function(data){
				aData = new Array;
			    aData = data.split("<!-- ~~||~~ -->");
			    $("#page-data").html(aData[1]);
	        }
		});
	});
	
    $('#mySwitch').on('switch-change', function (e, data) {
        var $el = $(data.el)
            , value = data.value;
        console.log(e, $el, value);
    });
    
    $('#dp1').datepicker();
    
    $('.make-switch').bootstrapSwitch('setOffClass', 'danger');
	
	$('.datepicker').mask('31.12.2020');
	$('.timepicker').mask('23:59');
	
	$('#timepicker1').timepicker({
        minuteStep: 1,
        showMeridian: false,
    });
	
	$('.datepicker').datepicker({
		language: 'tr',
		autoclose: true,
    });
	
	$('#nav-main ul > li > ul').hide();
	
	$('#nav-main > ul > li > a').click(function() {
		if(false == $(this).next().is(':visible')) {
	        $('#nav-main ul li ul').slideUp(300);
	    }
	    $(this).next().slideToggle(300);
	});
});

function orderModule(down)
{
    sl = document.fm.item.selectedIndex;
    if (sl != -1) {
     oText = document.fm.item.options[sl].text;
     oValue = document.fm.item.options[sl].value;
     if (sl > 0 && down == 0) {
      document.fm.item.options[sl].text = document.fm.item.options[sl-1].text;
      document.fm.item.options[sl].value = document.fm.item.options[sl-1].value;
      document.fm.item.options[sl-1].text = oText;
      document.fm.item.options[sl-1].value = oValue;
      document.fm.item.selectedIndex--;
     } else if (sl < document.fm.item.length-1 && down == 1) {
      document.fm.item.options[sl].text = document.fm.item.options[sl+1].text;
      document.fm.item.options[sl].value = document.fm.item.options[sl+1].value;
      document.fm.item.options[sl+1].text = oText;
      document.fm.item.options[sl+1].value = oValue;
      document.fm.item.selectedIndex++;
     }
    } else {
     alert("you must select an item to move");
    }
    return false;
}

function strpos (haystack, needle, offset) {
	  var i = (haystack + '').indexOf(needle, (offset || 0));
	  return i === -1 ? false : i;
}

function in_array(what, where) {
    var a=false;
    for (var i=0; i<where.length; i++) {
        if(what == where[i]) {
            a=true;
            break;
        }
    }
    return a;
}

function substr (str, start, len) {
	  var i = 0,
	    allBMP = true,
	    es = 0,
	    el = 0,
	    se = 0,
	    ret = '';
	  str += '';
	  var end = str.length;

	  this.php_js = this.php_js || {};
	  this.php_js.ini = this.php_js.ini || {};
	  switch ((this.php_js.ini['unicode.semantics'] && this.php_js.ini['unicode.semantics'].local_value.toLowerCase())) {
	  case 'on':
	    for (i = 0; i < str.length; i++) {
	      if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
	        allBMP = false;
	        break;
	      }
	    }

	    if (!allBMP) {
	      if (start < 0) {
	        for (i = end - 1, es = (start += end); i >= es; i--) {
	          if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
	            start--;
	            es--;
	          }
	        }
	      } else {
	        var surrogatePairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
	        while ((surrogatePairs.exec(str)) != null) {
	          var li = surrogatePairs.lastIndex;
	          if (li - 2 < start) {
	            start++;
	          } else {
	            break;
	          }
	        }
	      }

	      if (start >= end || start < 0) {
	        return false;
	      }
	      if (len < 0) {
	        for (i = end - 1, el = (end += len); i >= el; i--) {
	          if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
	            end--;
	            el--;
	          }
	        }
	        if (start > end) {
	          return false;
	        }
	        return str.slice(start, end);
	      } else {
	        se = start + len;
	        for (i = start; i < se; i++) {
	          ret += str.charAt(i);
	          if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
	            se++; 
	          }
	        }
	        return ret;
	      }
	      break;
	    }
	  case 'off':
	  default:
	    if (start < 0) {
	      start += end;
	    }
	    end = typeof len === 'undefined' ? end : (len < 0 ? len + end : len + start);
	    return start >= str.length || start < 0 || start > end ? !1 : str.slice(start, end);
	  }
	  return undefined; 
	}
