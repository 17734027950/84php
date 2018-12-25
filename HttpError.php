<?php
require (dirname(__FILE__).'/Core/Common.php');

if($GLOBALS['FrameworkHelpLanguage']=='CN'){
	$ErrorMsg="<center>服务器出现了HTTP错误（http 4xx/5xx）。</center>";
}
else{
	$ErrorMsg="<center>The server has HTTP error(HTTP 4xx/5xx).</center>";
}
Wrong::Report($ErrorMsg);