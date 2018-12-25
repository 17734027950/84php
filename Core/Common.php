<?php
/*****************************************************/
/*****************************************************/
/*                                                   */
/*               84PHP-http://84php.com              */
/*                                                   */
/*****************************************************/
/*****************************************************/

/*
  本框架为免费开源、遵循Apache开源协议的框架，但不得删除此文件的版权信息，违者必究。
  This framework is free and open source, following the framework of Apache open source protocol, but the copyright information of this file is not allowed to be deleted,violators will be prosecuted to the maximum extent possible.

  ©2018 84Tech. All rights reserved.

  V1.1.1
*/

//调试模式
$DebugMode=TRUE;

//SESSION自动开启
$SessionAutoStart=FALSE;

//帮助信息语言('CN'或'EN')
$FrameworkHelpLanguage='CN';

//以下禁止修改
$RootPath=substr(str_replace('\\','/',str_replace("//",'/',dirname(__FILE__))),0,-5);

require_once (dirname(__FILE__)."/Class/Base/Wrong.Class.php"); 
require_once (dirname(__FILE__)."/Class/Base/Session.Class.php"); 

if(!$DebugMode){
	error_reporting(0);
}
else{
	header('Cache-Control: no-cache,must-revalidate');   
	header('Pragma: no-cache');   
	header("Expires: -1"); 
	header('Last-Modified: '.gmdate('D, d M Y 00:00:00',time()).' GMT');
}

//X-Powered-By隐藏
$XPoweredBy='ASP.NET';
header('X-Powered-By: '.$XPoweredBy);   

// 错误处理
set_error_handler(function ($error_no, $error_msg, $error_file, $error_line) {
	if(error_reporting()==0){
		return TRUE;
	}
	if($GLOBALS['FrameworkHelpLanguage']=='CN'){
		$PHPSystemError="出现了PHP系统级别的错误（而不是框架导致的错误），详细的错误信息是：\r\n\r\n";
	}
	else{
		$PHPSystemError="A PHP system level error (rather than a frame error) occurred. The detailed error message is:\r\n\r\n";
	}
	
	switch ($error_no) {
        case E_WARNING:
            $PHPSystemError.='PHP Warning: ';
            break;
        case E_NOTICE:
            $PHPSystemError.='PHP Notice: ';
            break;
        case E_DEPRECATED:
            $PHPSystemError.='PHP Deprecated: ';
            break;
        case E_USER_ERROR:
            $PHPSystemError.='User Error: ';
            break;
        case E_USER_WARNING:
            $PHPSystemError.='User Warning: ';
            break;
        case E_USER_NOTICE:
            $PHPSystemError.='User Notice: ';
            break;
        case E_USER_DEPRECATED:
            $PHPSystemError.='User Deprecated: ';
            break;
        case E_STRICT:
            $PHPSystemError.='PHP Strict: ';
            break;
        default:
            $PHPSystemError.='Unkonw Type Error: ';
            break;
    }
 
    $PHPSystemError.=$error_msg.' in '.$error_file.' on '.$error_line;
	Wrong::Report($PHPSystemError);
	return TRUE;
}, E_ALL | E_STRICT);

//缓冲区控制开启
ob_start();