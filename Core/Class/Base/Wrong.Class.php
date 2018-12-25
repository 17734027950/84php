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

require (dirname(__FILE__)."/../Public/Wrong.php");

class Wrong{
	public static function Report($ErrorDetail,$ShowErrorInfo=FALSE,$OnlyMessage=FALSE){
		ob_clean();
		$FEContents=NULL;
		$FEFile=@fopen($GLOBALS['RootPath']."/Core/ErrorPage.php","r");
		if(!$FEFile){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Error->Report():ErrorPage.php不存在，也有可能是权限不足或文件被占用。';
			}
			else{
				$ModuleError='84PHP Error#Error->Report():ErrorPage.php does not exist, or there may be insufficient privileges or files being occupied.';
			}
		}
		else if(!$OnlyMessage&&filesize($GLOBALS['RootPath']."/Core/ErrorPage.php")>0){
			$FEContents=@fread($FEFile,filesize($GLOBALS['RootPath']."/Core/ErrorPage.php"));
		}
		else{
			$FEContents='{$ErrorInfo}';
		}
		@fclose($FEFile);
		if($GLOBALS['DebugMode']){
			$FEContents=str_replace('{$ErrorInfo}',$ErrorDetail,$FEContents);
		}
		else if($ShowErrorInfo){
			$FEContents=str_replace('{$ErrorInfo}',preg_replace("/84PHP(.*)\(\):/",'',$ErrorDetail),$FEContents);
		}
		else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$FEContents=str_replace('{$ErrorInfo}',"很抱歉，处理您的请求时出错了！\r\n如果您是开发者，请打开“调试模式”查看详细的出错信息。",$FEContents);
			}
			else{
				$FEContents=str_replace('{$ErrorInfo}',"We are sorry to have made a mistake when dealing with your request!\r\nIf you are a developer, please open debug mode to view detailed error messages.",$FEContents);
			}
		}
		if($GLOBALS['DebugMode']){
			$JumpURL='initial.php';
		}
		else{
			$JumpURL='';
		}
		$FEContents=str_replace('{$JumpURL}',$JumpURL,$FEContents);
		
		die($FEContents);
	}
}