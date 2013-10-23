//  Allows to show/collapse any block element given its #id
//  Usage: setup a checkbox and call this function with onclick event
//  ex: <input type="checkbox" name="foo" id="foo" onclick="collapseElement(this.checked,'id_of_block_to_collapse')" />
function collapseElement(display,elementId)
{
    var blockToCollapse = document.getElementById(elementId);
    if (display){
        blockToCollapse.style.display = 'block';
    } else {
        blockToCollapse.style.display = 'none';
    }
}

//  Allows to highlight a row when hovering it with mouse
//  Needs every row to have a "back..." class name
function switchRowColorOnHover()
{
	var table = document.getElementsByTagName("table");
    for (var i=0; i<table.length; i++) {
        var row = table[i].getElementsByTagName("tr");
        for (var j=0; j<row.length; j++) {
            row[j].onmouseover=function() {
                if (this.className.search(new RegExp("back"))>=0) {
                    this.className+=" backHighlight";
                }

            }
            row[j].onmouseout=function() {
                this.className=this.className.replace(new RegExp(" backHighlight\\b"), "");
            }
        }
    }
}

function lockButtons(whichform)
{
    ua = new String(navigator.userAgent);
    if (ua.match(/IE/g)) {
        for (i=1; i<whichform.elements.length; i++) {
            if ((whichform.elements[i].type == 'submit') || (whichform.elements[i].type == 'button'))
                whichform.elements[i].disabled = true;
        }
    }
    whichform.submit();
}

function openWindow()
{
    var newWin = null;
    var url = openWindow.arguments[0];
    nArgs = openWindow.arguments.length;
    var width = openWindow.arguments[1];
    var height = openWindow.arguments[2];

    //  if dynamic window size args are passed
    if (nArgs > 1)
        newWin =  window.open ("","newWindow","toolbar=no,width=" + width + ",height=" + height + ",directories=no,status=no,scrollbars=yes,resizable=no,menubar=no");
    else
        newWin =  window.open ("","newWindow","toolbar=no,width=" + SGL_JS_WINWIDTH + ",height=" + SGL_JS_WINHEIGHT + ",directories=no,status=no,scrollbars=yes,resizable=no,menubar=no");
    newWin.location.href = url;
}

function confirmSubmit(item, formName)
{
    var evalFormName = eval('document.' + formName)
    var flag = false
    for (var count = 0; count < evalFormName.elements.length; count++) {
        var tipo = evalFormName.elements[count].type
        if (tipo == 'checkbox' && evalFormName.elements[count].checked == true && evalFormName.elements[count].name != '')
            flag = true
    }
    if (flag == false) {
        alert('You must select an element to delete')
        return false
    }
    var agree = confirm("Are you sure you want to delete this " + item + "?");
    if (agree)
        return true;
    else
        return false;
}

function confirmSubmitAndUpdate(myAction, formId)
{
    var selectedForm = document.getElementById(formId);
    var flag = false
    for (var count = 0; count < selectedForm.elements.length; count++) {
        var myType = selectedForm.elements[count].type
        if (myType == 'checkbox' && selectedForm.elements[count].checked == true && selectedForm.elements[count].name != '')
            flag = true
    }
    if (flag == false) {
        alert('You must select at least one element to update')
        return false
    }
    newInput = document.createElement("input");
    newInput.setAttribute('name', 'action');
    newInput.setAttribute('value', myAction);
    newInput.setAttribute('type', 'hidden');
    selectedForm.appendChild(newInput);
    selectedForm.submit();
}

function confirmDelete(item, formName)
{
    var evalFormName = eval('document.' + formName)
    var flag = false
    var agree = confirm("Are you sure you want to delete this " + item + "?");
    if (agree)
        return true;
    else
        return false;
}

function confirmDeleteWithMsg(msg)
{
    var agree = confirm(msg);
    if (agree)
        return true;
    else
        return false;
}

function confirmSave(formName)
{
    var evalFormName = eval('document.' + formName)
    var flag = false
    for (var count = 0; count < evalFormName.elements.length; count++) {
        var tipo = evalFormName.elements[count].type
        if (tipo == 'checkbox' && evalFormName.elements[count].checked == true && evalFormName.elements[count].name != '')
            flag = true
    }
    if (flag == false) {
        alert('You must select an element to save')
        return false
    }
}

function confirmSend(formName)
{
    var evalFormName = eval('document.' + formName)
    var flag = false
    for (var count = 0; count < evalFormName.elements.length; count++) {
        var tipo = evalFormName.elements[count].type
        if (tipo == 'checkbox' && evalFormName.elements[count].checked == true && evalFormName.elements[count].name != '')
            flag = true
    }
    if (flag == false) {
        alert('You must select at least one recipient')
        return false
    }
}

function confirmCategoryDelete(item)
{
    var agree = confirm("Are you sure you want to delete this " + item + "?");
    if (agree)
        return true;
    else
        return false;
}

function verifySelectionMade()
{
    var moveForm = document.moveCategory.frmNewCatParentID
    var selectedCat = moveForm.value
    if (selectedCat == '') {
        alert('Please select a new parent category')
        return false;
    } else
        return true;
}

function checkInput(formName, fieldName)
{
    var f = eval('document.' + formName + '.' + fieldName)
    if (f.value == '') {
        alert('Please enter a value in the field before submitting');
        return false;
    } else
        return true;
}

function getSelectedValue(selectObj)
{
    return (selectObj.options[selectObj.selectedIndex].value);
}


function toggleDisplay(myElement)
{
	boxElement = document.getElementById(myElement);

	if (boxElement.style.display == 'none') {
		boxElement.style.display = 'block';
	} else {
    	// ... otherwise collapse box
		boxElement.style.display = 'none';
	}
}

function confirmCustom(alertText, confirmText, formName)
{
    var evalFormName = eval('document.' + formName)
    var flag = false
    for (var count = 0; count < evalFormName.elements.length; count++) {
        var tipo = evalFormName.elements[count].type
        if (tipo == 'checkbox' && evalFormName.elements[count].checked == true && evalFormName.elements[count].name != '')
            flag = true
    }
    if (flag == false) {
        alert(alertText)
        return false
    }
    var agree = confirm(confirmText);
    if (agree)
        return true;
    else
        return false;
}

//  for block manager

var oldDate;
oldDate = new Array();

function time_select_reset(prefix, changeBack) {
    //  TODO: Rewrite this whole function (time_select_reset()) when adminGui is implemented.
    function setEmpty(id) {
        if (dateSelector = document.getElementById(id)) {
            oldDate = dateSelector.value;
            dateSelectorToShow = document.getElementById("frmExpiryDateToShow");
            oldDateToShow = dateSelectorToShow.innerHTML;
            if (dateSelector.value != ''){
                //alert(dateSelector.value);
                dateSelector.value = '';
                dateSelectorToShow.innerHTML = '';
            }
        }
    }

    function setActive(id) {
        if (dateSelector = document.getElementById(id)) {
            dateSelector.value = oldDate;
            dateSelectorToShow.innerHTML = oldDateToShow;
        }

    }

    if (document.getElementById(prefix+'NoExpire').checked) {
        setEmpty('frmExpiryDate');
    } else {
        if (changeBack == true) {
            setActive('frmExpiryDate');
        }
    }
}

/**
 * Checks/unchecks all tables, modified from phpMyAdmin
 *
 * @param   string   the form name
 * @param   boolean  whether to check or to uncheck the element
 *
 * @return  boolean  always true
 */
function setCheckboxes(the_form, element_name, do_check)
{
    var elts      = (typeof(document.forms[the_form].elements[element_name]) != 'undefined')
                  ? document.forms[the_form].elements[element_name]
                  : '';
    var elts_cnt  = (typeof(elts.length) != 'undefined')
                  ? elts.length
                  : 0;
    //var applyToWholeForm =
    //alert(element_name)


    if (elts_cnt) {
        for (var i = 0; i < elts_cnt; i++) {
            elts[i].checked = do_check;
        }
    //  tick all checkboxes per form
    } else if (element_name == false) {
        var f = document.forms[the_form];
        for (var c = 0; c < f.elements.length; c++)
        if (f.elements[c].type == 'checkbox') {
          f.elements[c].checked = do_check;
        }

    } else {
        elts.checked        = do_check;
    }
    return true;
}

/**
 * Launches the above function depending on the status of a trigger checkbox
 *
 * @param   string   the form name
 * @param   string   the element name
 * @param   boolean   the status of triggered checkbox
 *
 * @return  void
 */
function applyToAllCheckboxes(formName, elementName, isChecked)
{
    if (isChecked) {
        setCheckboxes(formName, elementName, true)
    } else {
        setCheckboxes(formName, elementName, false)
    }
}

//  select/deselect options in a combobox
function toggleSelected(elem, state)
{
	var i;
	for (i = 0; i< elem.length; i++) {
		elem[i].selected = state;
	}
}

/**
 * REMINDER
 * Progressively change functions so they use document.getElementById() func
 * instead of document.<form name>
 * as forms must not have a "name" value according to xHTML STRICT DOCTYPE
 */

/**
 * Errors checking
 * Searching for <span class="error">,
 *  Modifying parent node className
 *  Setting appropriate left margin to other fields
 */
function formErrorCheck()
{
 var labels = document.getElementsByTagName("label");
 if (labels) {
     var labelWidth = labels[0].offsetWidth;
     if (document.all && !window.sidebar) {
        //  this check should recognise only IE
        labelWidth += 3;
     }
 }
 for (i=0; i<document.getElementsByTagName("form").length; i++) {
     var errorSpans = document.forms[i].getElementsByTagName("span");
     if (errorSpans) {
         for (j=0; j<errorSpans.length; j++) {
             if (errorSpans[j].className == "error") {
                 var parentObject = errorSpans[j].parentNode;
                 parentObject.className += " errorBlock";
                 for (k=0; k<parentObject.childNodes.length; k++) {
                     if (parentObject.childNodes[k].nodeName == "INPUT"
                         || parentObject.childNodes[k].nodeName == "SELECT") {
                         parentObject.childNodes[k].style.marginLeft = labelWidth +"px";
                     }
                 }
                 //  check if this node is in a tab
                 if (field = parentObject.parentNode) {
                     if (field.className == "options") {
                         var tabId = field.id;
                         var tabs = document.getElementById("optionsLinks");
                         var tabElements = tabs.getElementsByTagName("li");
                         for (l=0; l<tabElements.length; l++) {
                             if (tabElements[l].className.match(new RegExp(tabId+"\\b"))) {
                                 var errorTab = tabElements[l].childNodes;
                                 errorTab[0].className = "error";
                                 var thisForm = document.forms[0].id;
                                 showSelectedOptions(thisForm,tabId);
                             }
                         }
                     }
                 }
             }
         }
     }
 }
}
/**
 * Allows to create/modify a field value within a form before submitting it.
 * Launches the above function depending on the status of a trigger checkbox

 * @param   string   formName Obviously the form name you want to submit
 * @param   string   fieldToUpdate The element name you want to modify
 * @param   string   fieldValue
 * @param   bool      doCreate If you want to create a hidden input element instead of modifying an existing one
 *
 * @return  void Submit the form
 */
function formSubmit(formId, fieldName, fieldValue, doCreate)
{
 var selectedForm = document.getElementById(formId);
 if (typeof doCreate != "undefined" && doCreate == 1) {
     newInput = document.createElement("input");
     newInput.setAttribute('name', fieldName);
     newInput.setAttribute('value', fieldValue);
     newInput.setAttribute('type', 'hidden');
     selectedForm.appendChild(newInput);
 } else {
     if (fieldName) {
         var elm = selectedForm.elements[fieldName];
         elm.value = fieldValue;
     }
 }
 selectedForm.submit();
}

//Allows to reset a form
function formReset(formId)
{
 var selectedForm = document.getElementById(formId);
 if (!selectedForm) {
     return;
 }
 selectedForm.reset();
}

//Allows to show/hide a block of options (defined within a fieldset) in any form
function showSelectedOptions(formId, option)
{
 var selectedForm = document.getElementById(formId);
 if (!selectedForm) return true;
 var elms = selectedForm.getElementsByTagName("fieldset");
 for (i=0; i<elms.length; i++) {
     if (elms[i].className.match(new RegExp("options\\b"))) {
         if (elms[i].id == option) {
             elms[i].style.display = "block";
         } else {
             elms[i].style.display = "none";
         }
     }
 }

 var items = document.getElementById("optionsLinks").getElementsByTagName("li");
 for (i=0; i<items.length; i++) {
     if (items[i].className.match(new RegExp(" current\\b"))) {
         items[i].className = items[i].className.replace(new RegExp(" current\\b"), "");
     }
     if (items[i].className.match(new RegExp(option +"\\b"))) {
         items[i].className +=" current";
     }
 }
}

//Mandatory function when using showConfigOptions() above
//Dynamically creates links to display selected block of options
function createAvailOptionsLinks(formId, titleTag)
{
 var selectedForm = document.getElementById(formId);
 if (typeof titleTag == "undefined") var titleTag = 'h3';
 if (!selectedForm) return true;
 if (!document.getElementById("optionsLinks")) {
     alert('The Div container with id set to "optionsLinks" wasn\'t found' );
     return true;
 }
 var elms = selectedForm.getElementsByTagName("fieldset");
 var optionsLinks = '<ul>';
 for (i=0; i<elms.length; i++) {
     if (elms[i].className.match(new RegExp("options\\b"))) {
         optionsLinks += "<li class=\""+elms[i].id+"\"><a href='javascript:showSelectedOptions(\""+formId +"\",\""+elms[i].id +"\")'>"+elms[i].getElementsByTagName(titleTag)[0].innerHTML +"</a></li>";
     }
 }
 optionsLinks += "</ul>";
 document.getElementById("optionsLinks").innerHTML += optionsLinks;
}

function relocate_select(obj, value){
 if( obj ){
     for( i=0; i<obj.options.length; i++ ){
         if( obj.options[i].value==value )
             obj.options[i].selected = true;
         else
             obj.options[i].selected = false;
     }
 }

}

function orderItems(down)
{
 sl = document.frmBlockMgr.item.selectedIndex;
 if (sl != -1) {
     oText = document.frmBlockMgr.item.options[sl].text;
     oValue = document.frmBlockMgr.item.options[sl].value;
     if (sl > 0 && down == 0) {
         document.frmBlockMgr.item.options[sl].text = document.frmBlockMgr.item.options[sl-1].text;
         document.frmBlockMgr.item.options[sl].value = document.frmBlockMgr.item.options[sl-1].value;
         document.frmBlockMgr.item.options[sl-1].text = oText;
         document.frmBlockMgr.item.options[sl-1].value = oValue;
         document.frmBlockMgr.item.selectedIndex--;
     } else if (sl < document.frmBlockMgr.item.length-1 && down == 1) {
         document.frmBlockMgr.item.options[sl].text = document.frmBlockMgr.item.options[sl+1].text;
         document.frmBlockMgr.item.options[sl].value = document.frmBlockMgr.item.options[sl+1].value;
         document.frmBlockMgr.item.options[sl+1].text = oText;
         document.frmBlockMgr.item.options[sl+1].value = oValue;
         document.frmBlockMgr.item.selectedIndex++;
     }
 } else {
     alert("you must select an item to move");
 }

 return false;
}

function doSubBlock()
{
 blocksVal = "";
 for (i=0;i<document.frmBlockMgr.item.length;i++) {
     if (i!=0) { blocksVal += ","; }
     blocksVal += document.frmBlockMgr.item.options[i].value;
 }
 document.frmBlockMgr["_items"].value = blocksVal;

 return true;
}

//same fns again for faq & section managers!
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

function doSub()
{
 val = '';
 for (i=0;i<document.fm.item.length;i++) {
     if (i!=0) {
         val += ",";
     }
     val += document.fm.item.options[i].value;
 }
 document.fm[".items"].value = val;
 return true;
}

var delimiter = ":";

function MoveOption(MoveFrom, MoveTo, ToDo)
{
var SelectFrom = eval('document.main_form.'+MoveFrom);
var SelectTo = eval('document.main_form.'+MoveTo);
var SelectedIndex = SelectFrom.options.selectedIndex;
var container;
if (ToDo=='Add') {
 container=eval('document.main_form.' + ToDo + MoveTo);
}
if (ToDo=='Remove') {
 container=eval('document.main_form.' + ToDo + MoveFrom);
}
if (SelectedIndex == -1) {
 alert("Please select a permission(s) to move.");
} else {
 for (i=0; i<SelectFrom.options.length; i ++) {
   if (SelectFrom.options[i].selected) {
     var name = SelectFrom.options[i].text;
     var ID = SelectFrom.options[i].value;
     SelectFrom.options[i] = null;
     SelectTo.options[SelectTo.options.length]= new Option (name,ID);
     i=i-1;
     if (ToDo=='Add'||ToDo=='Remove') {
       container.value=container.value+name+'^' + ID + delimiter;
     }
   }
 }
}
}

function checkDuplicates(AddListContainer, RemoveListContainer)
{
 var AddList = eval('document.main_form.'+AddListContainer);
 var RemoveList = eval('document.main_form.'+RemoveListContainer);
 var TempAddList = AddList.value;
 var TempRemoveList = RemoveList.value;
 if (TempAddList>''&&TempRemoveList>'') {
     TempAddList = TempAddList.substring(0,TempAddList.length-1);
     TempRemoveList = TempRemoveList.substring(0,TempRemoveList.length-1);
     var AddArray = TempAddList.split(delimiter);
     var RemoveArray = TempRemoveList.split(delimiter);
     for (i=0; i<AddArray.length; i++) {
       for (j=0; j<RemoveArray.length; j++) {
         if (AddArray[i]==RemoveArray[j]) {
           AddArray[i]='';
           RemoveArray[j]='';
           break;
         }
       }
     }
     AddList.value='';
     for (i=0; i<AddArray.length; i++) {
       if (AddArray[i]>'') {
         AddList.value = AddList.value + AddArray[i] + delimiter;
       }
     }
     RemoveList.value='';
     for (i=0; i<RemoveArray.length; i++) {
       if (RemoveArray[i]>'') {
         RemoveList.value = RemoveList.value + RemoveArray[i] + delimiter;
       }
     }
 }
}

function lockChanges()
{
 checkDuplicates('AddfrmRolePerms','RemovefrmRolePerms');
}

// simple confirm box, incl list of any child node(s) of selected node(s)
function confirmDelete(item, formName)
{
var evalFormName = eval('document.' + formName)
var flag = false;
var childrenPresent = false;
var childNodes = new Array();
var toDelete = '';
var msg = '';
var childrenString = '';
for (var cont = 0; cont < evalFormName.elements.length; cont++) {
  var elType = evalFormName.elements[cont].type
  if (elType == 'checkbox' && evalFormName.elements[cont].checked == true && evalFormName.elements[cont].name != ''){
      flag = true;
      var elementString = evalFormName.elements[cont].name;
      var openBracket = elementString.indexOf("[") + 1;
      var closeBracket = elementString.lastIndexOf("]");
      nodeId = elementString.substring(openBracket,closeBracket);
      toDelete += nodeArray[nodeId][2] + ", ";
      if (!contains(nodeId,childNodes)){
       childNodes[childNodes.length] =  nodeId;
      }

      for(id in nodeArray)
      {
          if ( nodeArray[id][0] > nodeArray[nodeId][0] && nodeArray[id][1] < nodeArray[nodeId][1]  && nodeArray[id][4] == nodeArray[nodeId][4]){
              if (!contains(id,childNodes)){
                  childNodes[childNodes.length] = id;
                  childrenPresent = true;
              }
          }
      }
  }
}
toDelete = toDelete.substring(0,toDelete.lastIndexOf(","));
msg = "Are you sure you wish to permanently delete the " + item + "(s): " + toDelete + "?\n(If you anticipate using a " + item + " later, simply mark it \"disabled\" instead; it will no longer be displayed but can easily be reactivated later.)\n";

if (childrenPresent == true){
  for(b=0;b<childNodes.length;b++){
      var indent = '';
      for(g=1;g<nodeArray[childNodes[b]][3];g++){
          indent = indent + "\t";
      }
      childrenString = childrenString + indent + "-" + nodeArray[childNodes[b]][2] + "\n";
  }
  msg = msg + "\nCAUTION: One or more of the " + item + "s you selected contains subordinate " + item + "s. If you proceed, all of the following " + item + "s will be deleted:\n\n" + childrenString + "\nAre you sure you want to do this?";
}

if (flag == false) {
  alert('You must select an element to delete')
  return false
}
var agree = confirm(msg);
if (agree)
  return true;
else
  return false;
}

function confirmAction(msg)
{
 var isConfirmed = confirm(msg);
 if (isConfirmed != '') {
     return true;
 } else {
     return false;
 }
}

//used by confirmDelete(); sees if array already contains a value
function contains(tmpVal, tmpArray)
{
 for (j=0; j < tmpArray.length; j++) {
     if (tmpArray[j] == tmpVal) {
         return true;
     }
 }
 return false;
}
