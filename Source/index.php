<?php
require_once (dirname(__FILE__).'/../Core/Common.php');

if($GLOBALS['FrameworkHelpLanguage']=='CN'){
	$ErrorMsg="<center>对不起，页面弄丢了（HTTP 404）~</center>";
}
else{
	$ErrorMsg="<center>Ah~ Page does not exist ( HTTP 404 ).</center>";
}
Wrong::Report($ErrorMsg);