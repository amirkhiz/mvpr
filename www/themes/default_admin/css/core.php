/******************************************************************************/
/*                                  LAYOUT CSS                                */
/******************************************************************************/

/*
==========================General=============================*/


/*
=======================Buttons like===========================*/


/*
======================Global layaout==========================*/
#outer-wrapper {
    margin: 0 auto;
    text-align: left;
<?php if (isBrowserFamily('MSIE7', '<')) { ?>
    width: expression((documentElement.clientWidth || document.body.clientWidth) > 740 ? "auto" : "740px");
<?php } ?>
}

body{
	padding-top: 40px;
}

.pagination-sm > li > a, .pagination-sm > li > span
{
	padding: 3px 7px 4px 7px;
}

.small-control{
	width: 250px;
}
.form-template .label-danger{
	margin-left: 15px;
}

.col-lg-2{
	width: 100px;
}

#form-group .label{
	margin-left: 15px;
}

#form-group .control-label{
	text-align: left;
}

#form-group .has-error .control-label{
	margin-top: 17px;
}

.reorder-btns{
	margin-top: 10px;
}

.table-bordered {
	margin-top: 17px;
}
.table-bordered > thead > tr > th {
	background: url("<?php echo $baseUrl ?>/images/backgrounds/table-th.gif") repeat-x;
	color: #707070;
}

.topbtns{
	padding-bottom: 17px;
}
.navbar-fixed-top {
	background: #ffffff; /* Old browsers */
	/* IE9 SVG, needs conditional override of 'filter' to 'none' */
	background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjQ3JSIgc3RvcC1jb2xvcj0iI2Y2ZjZmNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNlZGVkZWQiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
	background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #ededed 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(47%,#f6f6f6), color-stop(100%,#ededed)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 47%,#ededed 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed',GradientType=0 ); /* IE6-8 */
	-webkit-box-shadow: 0px 2px 9px rgba(50, 50, 50, 0.15);
	-moz-box-shadow:    0px 2px 9px rgba(50, 50, 50, 0.15);
	box-shadow:         0px 2px 9px rgba(50, 50, 50, 0.15);
	border-bottom: 1px solid #cccccc;
}
#header {
    position: relative;
}
#inner-wrapper {
    clear: both;
    padding: 0px;
}

#left-column {
    float: left;
    width: 200px;
}

.pagination{
	margin: 1px;
}

#container{
	background: #eee url("<?php echo $baseUrl ?>/images/back.png") repeat;
}

#middle-column {
    position: relative;
	margin-left: 230px;
	margin-right: 0px;
	width: auto;
	background: #eee url("<?php echo $baseUrl ?>/images/main-back.png") repeat;
	z-index: 50;
	min-height: 400px;
}
#main {
    float: left;
    width: 100%;
    font-size: 1em;
    border: 1px solid <?php echo $borderDark ?>;
    -moz-border-radius: 0.4em;
    margin-right: -1px; /* hides annoying horizontal scrolling in ie6 */
<?php if (isBrowserFamily('MSIE7')) { ?>
    margin-right: -2px; /* hides annoying horizontal scrolling in ie7 */
<?php } ?>
}
#content {
    clear: both;
    padding: 5px 8px 0;
    -moz-border-radius: 0 0 0.4em 0.4em;
    border-top: 1px solid <?php echo $borderLight ?>;
    padding-bottom: 40px; /* TO REMOVE */
}
#footer {
    text-align: center;
    border-top: 1px solid <?php echo $borderLight ?>;
    background: <?php echo $tertiary ?>;
}

/*
=========================Header===============================*/
#header {
	border-top: 1px solid #ccc;
	padding: 15px 5px 15px 5px;
	background: #fafafa url("<?php echo $baseUrl ?>/images/cream.png") repeat;
	border-bottom: 1px solid #ddd;
}

#header #left h1{
	font-weight: normal;
	margin: 0px;
}

h1, h2, h3, h4, h5, h6 {
	font-weight: normal;
	padding: 2px 0px;
	margin: 2px 0px;
	color: #777;
}

#header #left h1 a{
	color: #777;
	text-decoration: none;
}

#header #left h1 a:hover{
	color: #777;
	text-decoration: none;
}

#header .logo .meta {
	color: #888;
	line-height: 15px;
	padding: 0px;
	margin: 0px;
	padding-left: 10px;
}

/*
====================Left column block=========================*/


/* Sidebar */

.sidebar{
	width: 230px;
	float: left;
	display: block;
	background:#f2f2f2;
	color: #777;
	position: relative;
}

.sidebar .sidebar-dropdown{
	display: block;
	padding: 1px;
}

.sidebar .sidebar-dropdown a{
	color: #ddd;  
	box-shadow: inset 0px 0px 1px #000;
	background-color: #343434;
  background: -webkit-gradient(linear, left top, left bottom, from(#343434), to(#292929));
  background: -webkit-linear-gradient(top, #343434, #292929);
  background: -moz-linear-gradient(top, #343434, #292929);
  background: -ms-linear-gradient(top, #343434, #292929);
  background: -o-linear-gradient(top, #343434, #292929);
  background: linear-gradient(top, #343434, #292929);	
  padding:6px;
  text-transform: uppercase;
  text-align: center;
  font-size: 11px;
  display: block;
  border-top: 2px solid #666;
  border-bottom: 1px solid #333;
  text-decoration: none;
}

.sidebar ul{
	padding: 0px;
	margin: 0px;
}

.sidebar ul li{
	list-style-type: none;
}

.sidebar #nav { 
  display: block; 
  width:100%; 
  margin: 0 auto; 
  position: absolute;
  z-index: 60;
}

.sidebar #nav li i{
	margin-right: 5px;
	background: #eee;
	color:#888;
	width: 28px;
	height: 28px;
	line-height: 28px;
	text-align: center;
	border-radius: 40px;
	border: 1px solid #ccc;
}

.sidebar  #nav li span i{
	margin: 0px;
	color: #999;
	background: transparent;
	border: 0px;
}

.sidebar #nav li a { 
  display: block; 
  padding: 10px 20px;
  font-size: 13px;
  color: #777;
  text-decoration: none;
  border-bottom: 1px solid #ccc;
  border-top: 1px solid #fff;
  background-color: #f8f8f8;
  background: -webkit-gradient(linear, left top, left bottom, from(#f9f9f9), to(#f2f2f2));
  background: -webkit-linear-gradient(top, #f9f9f9, #f2f2f2);
  background: -moz-linear-gradient(top, #f9f9f9, #f2f2f2);
  background: -ms-linear-gradient(top, #f9f9f9, #f2f2f2);
  background: -o-linear-gradient(top, #f9f9f9, #f2f2f2);
  background: linear-gradient(top, #f9f9f9, #f2f2f2);	
  box-shadow: inset 0px 1px 1px #fff;
}



.sidebar #nav li a:hover, .sidebar #nav li a.open, .sidebar #nav li a.subdrop { 
  color: #e9e9e9;
  border-bottom: 1px solid #167cac;
  border-top: 1px solid #2094ca;
  background-color: #0993d3;
  background: -webkit-gradient(linear, left top, left bottom, from(#1aaef3), to(#0993d3));
  background: -webkit-linear-gradient(top, #1aaef3, #0993d3);
  background: -moz-linear-gradient(top, #1aaef3, #0993d3);
  background: -ms-linear-gradient(top, #1aaef3, #0993d3);
  background: -o-linear-gradient(top, #1aaef3, #0993d3);
  background: linear-gradient(top, #1aaef3, #0993d3);
  box-shadow: none;
  color: #fff;
  font-weight: bold;
}

.sidebar #nav li a:hover i, .sidebar #nav ul li a.open i, .sidebar #nav ul li a.subdrop i{
	color: #fff;
  background-color: #167cac !important;
  border: 1px solid #167cac;
}

.sidebar #nav li a:hover span i, .sidebar #nav ul li a.open span i, .sidebar #nav ul li a.subdrop span i{
  color: #fff;
  background: transparent !important;
  border: 0px;
}

.sidebar #nav li ul { display: none; background: #efefef url("<?php echo $baseUrl ?>/img/cream.png") repeat; }

.sidebar #nav li ul li a { 
  display: block; 
  background: none;
  padding: 10px 0px;
  padding-left: 40px;
  text-decoration: none;
  color: #777;
  font-size: 12px;
}

.sidebar #nav li ul li a:hover {
  background: #eee;
  border-bottom: 1px solid #ddd;
  border-top: 1px solid #ddd;
  color: #777;
  font-weight: normal;
}

.sidebar #nav li.open a {
  color: #e9e9e9;
  border-bottom: 1px solid #167cac;
  border-top: 1px solid #2094ca;
  background-color: #0993d3;
  background: -webkit-gradient(linear, left top, left bottom, from(#1aaef3), to(#0993d3));
  background: -webkit-linear-gradient(top, #1aaef3, #0993d3);
  background: -moz-linear-gradient(top, #1aaef3, #0993d3);
  background: -ms-linear-gradient(top, #1aaef3, #0993d3);
  background: -o-linear-gradient(top, #1aaef3, #0993d3);
  background: linear-gradient(top, #1aaef3, #0993d3);
  box-shadow: none;
  color: #fff;
  font-weight: normal;
}

.block .header {
    margin: 0;
    height: 30px;
    line-height: 30px;
    background: <?php echo $tertiaryLight ?>;
    font-weight: bold;
    font-size: 1.1em;
    text-align: center;
    color: buttonshadow;
    border-bottom: 1px solid <?php echo $borderDark ?>;
    -moz-border-radius: 0.4em 0.4em 0 0;
}
.block .content {
    border-bottom: 1px solid <?php echo $borderLight ?>;
}

/*
========================Main block============================*/
#manager-infos {
	background-color: #f8f8f8;
	background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f2f2f2));
	background: -webkit-linear-gradient(top, #f8f8f8, #f2f2f2);
	background: -moz-linear-gradient(top, #f8f8f8, #f2f2f2);
	background: -ms-linear-gradient(top, #f8f8f8, #f2f2f2);
	background: -o-linear-gradient(top, #f8f8f8, #f2f2f2);
	background: linear-gradient(top, #f8f8f8, #f2f2f2);
	padding: 8px 20px;
	border-bottom: 1px solid #cccccc;
	box-shadow: inset 0px 1px 1px #fff;
}
#manager-infos h1 {
    font-weight: normal;
	font-size: 20px;
}
a#module-conf {
    float: right;
    margin-right: 10px;
    padding-left: 25px;
    height: 30px;
    line-height: 30px;
    background: <?php echo $tertiary ?>;
    background: url('<?php echo $baseUrl ?>/images/22/action_config.gif') 0 50% no-repeat;
    color: <?php echo $primary ?>;
}
#breadcrumbs {
    float: left;
    width: 100%;
    background: <?php echo $tertiary ?>;
    border-top: 1px solid <?php echo $borderLight ?>;
    border-bottom: 1px solid <?php echo $borderDark ?>;
}
#breadcrumbs p {
    text-indent: 2em;
}
#breadcrumbs a  {
    padding-right: 20px;
    color: <?php echo $primary ?>;
}
#manager-actions {
    position: relative;
    float: left;
    width: 100%;
    height: 32px;
    padding: 1px 0;
    background: <?php echo $tertiary ?>;
    border-top: 1px solid <?php echo $borderLight ?>;
    border-bottom: 1px solid <?php echo $borderDark ?>;
}
html>body #manager-actions {
    padding: 1px 0 0;
}
#manager-actions span {
    float: left;
    text-indent: 2em;
    padding-right: 10px;
    height: 30px;
    line-height: 30px;
    font-weight: bold;
    color: <?php echo $tertiaryDarkest ?>;
}
#manager-actions a {
    float: left;
    display: block;
    margin-right: 0.5em;
    padding: 0 4px 0 28px;
    height: 28px;
    line-height: 28px;
    border: 1px solid <?php echo $tertiary ?>;
    color: <?php echo $tertiaryDarkest ?>;
    text-decoration: none;
    /* -- See below for each action backgroud image
    -----------------------------------------------*/
}
#manager-actions a:hover {
    background-color: <?php echo $tertiaryLightest ?>;
    border-style: solid;
    border-width: 1px;
    border-color: <?php echo $tertiaryDarkest ?>;
}
#manager-actions a:active {
    background-color: <?php echo $tertiaryLightest ?>;
    border: none;
}
#manager-actions a:focus {
    background-color: <?php echo $tertiaryLightest ?>;
    border: none;
}

#manager-actions select {
    float: left;
    margin-top: 5px;
    margin-right: 5px;
}
#content-header {
    margin: 0px;
    font-weight: normal;
    color: <?php echo $primary ?>;
    text-align: center;
}
/*
======================No forms layout=========================*/
div.fieldsetlike { /*
--------------------- as some pages don't use forms/fieldsets
- e.g. module/overview, we have to put data in a fieldset like
- div to have same render ------------------------------------*/
    padding: 0;
}
div.fieldsetlike h3 {
    margin-bottom: 0.5em;
    color: <?php echo $tertiaryDark ?>;
}
/*
===================Forms default layout=======================*/
form {
    margin: 0;
    padding: 0;
}
.searchth{
	width: 150px;
	color: #555555;
	margin: auto;
}
.searchth a{
	color: #555555;
}

.tooltip-inner {
	background-color: #d9534f;
	font-weigth: normal;
	width: 100px;
}
.tooltip.right .tooltip-arrow{
	border-right-color:#d9534f; 
}
fieldset {
    margin: 0 0 1em;
    padding: 10px;
    border: 1px solid <?php echo $borderDark ?>;
}
fieldset.options h3 {
    visibility: hidden;
}
fieldset.options h3.show {
    visibility: visible;
}
select, input, textarea {
    z-index: 1;
}
html>body p select, html>body p input, html>body p textarea {
    border: 1px solid <?php echo $primary ?>;
}
html>body p input[type="text"] {
    text-indent: 2px;
}
p input:focus, p textarea:focus {
    background: <?php echo $primaryLightest ?>;
}
fieldset.noBorder {
    padding: 0;
    border: none;
}
fieldset.inside { /*
    -------------- also for pages without form (e.g. module/overview) */
    background: <?php echo $tertiaryLightest ?>;
}
fieldset.options {
    clear: left;
    background: <?php echo $tertiaryLightest ?>;
    border-top: none;
}

/* --
Definition lists (<dl>) will progressively replace "p label" to display fields labels and values
-----*/
dl.onSide dt {
    float: left;
    width: 140px;
    text-align: right;
}
dl.onSide dt label {
    padding-right: 15px;
}
dl.onSide dd{
    margin-left: 160px;
    margin-bottom: 0.5em;
}
dl.onTop dd {
    margin: 0;
}
dl.buttonsBottom {
    clear: both;
    float: left;
}

/*
==================Tables default layout=======================*/

tr img, tr input {
    vertical-align: middle;
}
tr th, tr td {
    text-align: center;
    border: none;
}
tr th {
    color: #afafaf;
    font-weight: bold;
}
thead tr {
    background: <?php echo $primary; ?>;
    color: <?php echo $tertiaryLightest ?>;
}
thead tr.infos, tfoot tr.infos {
    background: <?php echo $tertiaryBg ?>;
    color: <?php echo $primary; ?>;
}
thead td {
    padding: 0 12px;
}
thead th, thead th a {
    color: <?php echo $tertiaryLightest ?>;
}
thead tr a:hover {
    color: <?php echo $primaryDark ?>;
}
tr.backLight {
    background: <?php echo $tableRowLight ?>;
    border-bottom: 1px solid <?php echo $primaryLight ?>;
}
tr.backDark {
    background: <?php echo $tableRowDark ?>;
    border-bottom: 1px solid <?php echo $primaryLight ?>;
}
tr.backHighlight {
    background: <?php echo $primaryLightest ?>;
}

/*
==========================Tip boxes===========================*/
span.tipOwner, label.tipOwner, input.tipOwner {
    position: relative;
    cursor: help;
}
label.tipOwner {
    background: url('<?php echo $baseUrl ?>/images/tooltip.gif') 98% 50% no-repeat;
}
span.tipOwner span.tipText, label.tipOwner span.tipText, input.tipOwner span.tipText {
    display: none;
    position: absolute;
    top: 1.8em;
    left: 100%;
    border: 1px solid <?php echo $borderDark ?>;
    background-color: <?php echo $primaryBackground ?>;
    color: <?php echo $primaryText ?>;
    text-align: center;
    line-height: normal;
    width: 20em;
    padding: 2px 5px;
    -moz-opacity: 1;
    z-index: 100;
    <?php if (isBrowserFamily('MSIE')) { ?>
    filter: alpha(opacity=100);
    filter: progid: DXImageTransform.Microsoft.Alpha(opacity=100);
    <?php } ?>
}
span.tipOwner:hover span.tipText, label.tipOwner:hover span.tipText, input.tipOwner:hover span.tipText {
    display: block;
}
<?php if (isBrowserFamily('MSIE7', '<')) { ?>
/* IE javascript workaround */
span.tipOwner, label.tipOwner, input.tipOwner {
    behavior: url(<?php echo $baseUrl ?>/css/tooltipHover.htc);
}
<?php } ?>
/* Holly Hack here so that tooltips don't act screwy:
 * http://www.positioniseverything.net/explorer/threepxtest.html */
/* Hide next from Mac IE plus non-IE \*/
* html #content {
    height: 1%;
}
/* End hide from IE5/mac plus non-IE */

/*-- Special TipText boxes ----------------------------------*/
span#becareful {
    top: -35px;
    left: -3.5em;
    width: 6em;
    padding: 5px;
    background: #fff;
    border: 1px solid #ff3300;
    color: #ff3300;
    z-index: 150;
}

/*
===================Manager-actions images=====================*/
/*-- Each action link (<a> tag) has a standard "action" class name
  -- plus a specific <action-type> class name e.g. "add", "edit",...
  -- to define which image to use. This allows to change assigned
  -- images in a single location : here. ---------------------*/
a.action {
    background-position: 3px 50%;
    background-repeat: no-repeat;
}
a.add {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_add.gif');
}
a.edit {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_edit.gif');
}
a.delete {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_delete.gif');
}
a.save {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_save.gif');
}
a.validate {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_validate.gif');
}
a.cancel {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_cancel.gif');
}
a.undo {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_undo.gif');
}
a.download {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_download.gif');
}
a.upload {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_upload.gif');
}
a.reorder {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_reorder.gif');
}
a.search {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_search.gif');
}
a.addcat {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_addcat.gif');
}
a.addrootcat {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_addrootcat.gif');
}
a.adduser {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_adduser.gif');
}
a.scannew {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_scannew.gif');
}
a.delorphaned {
    background-image: url('<?php echo $baseUrl ?>/images/22/action_delorphaned.gif');
}

/*
======================CategoryNav Block=======================*/
div#categorySelect {
    float: left;
    width: 20%;
}
a.catSelect {
    margin-left: 1em;
    padding: 2px 0 2px 20px;
    background: url('<?php echo $baseUrl ?>/images/16/folder.gif') 0 80% no-repeat;
}
a.catSelect:hover {
    background: url('<?php echo $baseUrl ?>/images/16/folder_open.gif') 0 80% no-repeat;
    text-decoration: none;
    cursor: pointer;
}
div#categoryNav {
    display: none;
    position: absolute;
    width: 200px;
    background: <?php echo $tertiaryLightest ?>;
    border-style: solid;
    border-color: <?php echo $primary ?>;
    border-width: 1px 2px 2px 1px;
    z-index: 100;
    <?php if (isBrowserFamily('MSIE')) { ?>
    filter: alpha(opacity=90);
    <?php } else { ?>
    -moz-opacity: 0.9;
    <?php } ?>
}

div.close {
    border-bottom: 1px solid <?php echo $primary ?>;
    text-align: right;
}
div.close span {
    padding-right: 20px;
    background: url('<?php echo $baseUrl ?>/images/close.gif') 92% 50% no-repeat;
    color: <?php echo $primary ?>;
    cursor: pointer;
}

/*
========================Options Links=========================*/
#optionsLinks {
    float:left;
    width:100%;
    background: url('<?php echo $baseUrl ?>/images/backgrounds/bg_tabs.gif') repeat-x left bottom;
}
#optionsLinks ul {
    padding:10px 7px 0;
}
#optionsLinks li {
    float:left;
    width: auto;
    background: url('<?php echo $baseUrl ?>/images/backgrounds/tab_right.gif') no-repeat right top;
}
#optionsLinks li.current {
    background-image: url('<?php echo $baseUrl ?>/images/backgrounds/tab_right_on.gif');
}
#optionsLinks a {
    display: block;
    background: url('<?php echo $baseUrl ?>/images/backgrounds/tab_left.gif') no-repeat left top;
    padding: 5px 10px 4px;
}
#optionsLinks li.current a {
    background-image: url('<?php echo $baseUrl ?>/images/backgrounds/tab_left_on.gif');
    padding-bottom: 5px;
}

/*
===========================Messages==============================*/

/*-- Seagull Errors ---------------------------------------------*/
.message {
    text-align: center;
    z-index: 1;
}
.message div {
    width: 60%;
    margin: 0 auto 15px;
    padding: 5px 25px;
    background-color: <?php echo $tertiaryLightest ?>;
    background-position: 0 50%;
    background-repeat: no-repeat;
    border-width: 2px;
    border-style: solid;
    -moz-border-radius: 0.4em;
}
.infoMessage {
	padding: 10px 35px 10px 15px;
	margin-bottom: 20px;
	color: #c09853;
	background-color: #fcf8e3;
	border: 1px solid #fbeed5;
	border-radius: 4px;
    color: #468847;
	background-color: #dff0d8;
	border-color: #d6e9c6;
	text-align: center;

}
.errorMessage {
	padding: 10px 35px 10px 15px;
	margin-bottom: 20px;
	border: 1px solid #fbeed5;
	border-radius: 4px;
    color: #b94a48;
	background-color: #f2dede;
	border-color: #eed3d7;
	text-align: center;
}
.warningMessage {
    color: #3a87ad;
	background-color: #d9edf7;
	border-color: #bce8f1;
	border: 1px solid #fbeed5;
	border-radius: 4px;
	padding: 10px 35px 10px 15px;
	text-align: center;
}
.error, .required {
    color: <?php echo $errorMessage ?>;
}
.warning {
    color: <?php echo $warningMessage ?>;
}

/*-- PHP Errors by ErrorHandler.php -----------------------------*/


/*-- PEAR Errors ------------------------------------------------*/
.errorContainer {
    text-align: left;
}
.errorContainer div{
    width: auto;
    margin: 0;
    padding: 5px 0;
    border: none;
}
.errorContainer .errorHeader {
    padding-left: 30px;
    background-image: url('<?php echo $baseUrl ?>/images/22/dialog_error.gif');
    text-transform: uppercase ;
    font-weight: bold;
    letter-spacing: 0.3em;
    color: <?php echo $errorMessage ?>;
}
.errorContainer .errorContent {

}

/*-- Errors in submitted forms ----------------------------------*/
p.errorBlock label {
    background: url('<?php echo $baseUrl ?>/images/16/dialog_error.gif') 98% 50% no-repeat;
    font-weight: bold;
}
p.errorBlock span.required {
    display: none;
}
p.errorBlock span.error {
    display: block;
    line-height: normal;
}
p.errorBlock input, p.errorBlock select {
    display: block;
    border: 1px solid <?php echo $errorMessage ?>;
}
<?php
    if (!empty($isFormSubmitted)) { ?>
.required {
    display: none;
}
.helpRequire {
    display: none;
}
    <?php }
?>

/*
========================Miscellaneous=========================*/
.floatLeft {
    float: left;
}
.floatRight {
    float: right;
}
.clear {
    clear: both;
}
.spacer {
    clear: both;
    visibility: hidden;
    line-height: 1px;
}
.left {
    text-align: left;
}
.right {
    text-align: right;
}
.center {
    text-align: center;
}
.altFont {
    font-family: <?php echo $fontFamilyAlt ?>;
}
.hide {
    display: none;
}
.narrow {
    width: 45%;
}
.wide {
    width: 60%;
}
.full {
    width: 100%;
}
.noBg {
    background: none;
}

a.clearSearch {
    background: url('<?php echo $baseUrl ?>/images/16/action_no.gif') no-repeat 5px 50%;
    margin-left: 10px;
    padding: 3px 5px 3px 25px;
    color: <?php echo $tertiaryDarkest ?>;
}
a.clearSearch:hover {
    text-decoration: none;
    color: <?php echo $tertiaryDarkest ?>;
}
.disabled, a.disabled, a.disabled:visited {
    color: grey;
}


#debug {
    color: #333333;
    position: absolute;
    z-index: 999;
    top: 0px;
    right: 0px;
    border: 1px black solid;
    margin: 10px;
    padding: 5px 20px;
    width: 120px;
    height: 300px;
    background-color: grey;
    opacity:0.9;
    text-align: left;
    overflow: auto;
}
#debug a, #debug a:visited {
    color: #CCCCCC;
}


/* Debug panel */
div#debugPanel {
    position: absolute;
    z-index: 9999;
    top: 0;
    right: 0;
    overflow: auto;
    border: 1px black solid;
    margin: 10px;
    min-width: 150px;
    height: 300px;
    padding: 5px 10px;
    background-color: #808080;
    opacity: 0.9;
    color: #333;
    text-align: left;
}
    div#debugPanel h3 {
        margin-bottom: 0.5em;
        color: #fff;
    }
    div#debugPanel a {
        color: #ccc;
        text-decoration: none;
    }
    div#debugPanel dl {
        margin-bottom: 0;
    }
        div#debugPanel dl dt {
            margin-bottom: 0.2em;
            font-weight: normal
        }
        div#debugPanel dl dd {
            margin-left: 0;
            margin-bottom: 0.5em;
        }
/* END debug panel */

