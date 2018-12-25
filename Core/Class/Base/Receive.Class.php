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

require (dirname(__FILE__)."/../Public/Receive.php");

class Receive{
	//来源检测
	public function FromCheck($TokenCheck=FALSE,$UnsetToken=TRUE){
		if (!isset($_SERVER['HTTP_REFERER'])){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError="84PHP Error#Receive->FromCheck():上一页面（通常是表单页）的域名为空。通常情况是由于机器爬虫访问（非浏览器访问）造成，如果此页面为API，请将方法中的FromCheck参数设置为FALSE。详情见说明文档。";
			}
			else{
				$ModuleError="84PHP Error#Receive->FromCheck():The domain name of the last page (this page usually is form page) is empty. Usually, it is caused by machine crawler access (non browser access). If this page is API, please set the FromCheck parameter to FALSE in the function. For details, see the documentation.";
			}
			Wrong::Report($ModuleError);
		}
		if ($GLOBALS['BeforeDomainCheck']&&parse_url($_SERVER['HTTP_REFERER'])['host']!=$_SERVER['SERVER_NAME']){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError="84PHP Error#Receive->FromCheck():上一页面（通常是表单页）的域名与此页面的域名不一致。如果您的服务器为Apache，可能需要相应的设置后才能使用，您也可以在配置文件中关闭此功能。详情见说明文档。";
			}
			else{
				$ModuleError="84PHP Error#Receive->FromCheck():The domain name of the previous page (this page usually is form page) is inconsistent with the domain name of this page. If your server is Apache, you may need a corresponding setting before you can use it, you can also close this function in the configuration file. For details, see the documentation.";
			}
			Wrong::Report($ModuleError);
		}
		if($TokenCheck){
			if(isset($_POST['Token'],$_GET['Token'])){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError="84PHP Error#Receive->FromCheck():Token缺失，请通过GET/POST方法传递一个Token字段（大小写敏感）。";
				}
				else{
					$ModuleError="84PHP Error#Receive->FromCheck():Token is missing. Please pass a Token field (case sensitive) through the GET/POST method.";
				}
				Wrong::Report($ModuleError);
			}
			if(!isset($_SESSION['Token'])){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Receive->FromCheck():SESSION中没有Token。可能您并未生成Token，或者是您已经将Token清除。';
				}
				else{
					$ModuleError='84PHP Error#Receive->FromCheck():There is no Token in SESSION. You may not have generated Token, or you have cleared Token.';
				}
				Wrong::Report($ModuleError);
			}
			if((isset($_POST['Token'])&&$_POST['Token']!=$_SESSION['Token']['token'])||(isset($_GET['Token'])&&$_GET['Token']!=$_SESSION['Token']['token'])||$_SESSION['Token']['time']+$GLOBALS['TokenExpTime']<time()){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Receive->FromCheck():Token不正确或Token已过期。';
				}
				else{
					$ModuleError='84PHP Error#Receive->FromCheck():Token is incorrect or Token has expired.';
				}
				Wrong::Report($ModuleError);
			}
			if($UnsetToken){
				unset($_SESSION['Token']);
			}
		}
	}

	//安全检测模块
	public function SafeCheck($WillCheck) {
		$Return=$WillCheck;
		foreach ($GLOBALS['DangerChar'] as $Key=>$Val) {
			$Return=str_replace($Key,$Val,$Return);
		}
		if($GLOBALS['KillEmoji']){
			$Return=preg_replace_callback('/./u',function($TempArray){
				if(strlen($TempArray[0])>=4){
					return NULL;
				}
				return $TempArray[0];
			},$Return);
		}
		return $Return;
	}
	
	//Post接收
	public function Post($TokenCheck=FALSE,$FieldCheck=NULL,$FromCheck=TRUE,$SafeCheck=TRUE){
		if(!empty($FieldCheck)&&is_array($FieldCheck)){
			foreach ($FieldCheck as $Val){
				$TempOp=explode(',',$Val);
				if((!isset($_POST[$TempOp[0]]))||(isset($TempOp[1])&&strtoupper($TempOp[1])=='TRUE'&&empty($_POST[$TempOp[0]]))){
					if($GLOBALS['FrameworkHelpLanguage']=='CN'){
						$ModuleError='84PHP Error#Receive->Post():字段“'.$TempOp[0].'”缺失或值为空。如果字段不允许空值，请参照说明文档设置正确的参数。';
					}
					else{
						$ModuleError='84PHP Error#Receive->Post():The field "'.$TempOp[0].'" is missing or the value is empty. If the field not allows null values, refer to the specification document to set the correct parameters.';
					}
					Wrong::Report($ModuleError);
				}
			}
		}
		if($FromCheck==TRUE){
			$this->FromCheck($TokenCheck);
		}
		
		$Return=array();
		foreach ($_POST as $Key=>$Val) {
			if($SafeCheck==TRUE){
				$Val=$this->SafeCheck($Val);
			}
			$PostArray=array($Key=>$Val);
			$Return=array_merge($Return,$PostArray);
		}
		return $Return;
	}
	
	//Get接收
	public function Get($TokenCheck=FALSE,$FieldCheck=NULL,$FromCheck=TRUE,$SafeCheck=TRUE){
		if(!empty($FieldCheck)&&is_array($FieldCheck)){
			foreach ($FieldCheck as $Val){
				$TempOp=explode(',',$Val);
				if((!isset($_GET[$TempOp[0]]))||(isset($TempOp[1])&&strtoupper($TempOp[1])=='TRUE'&&empty($_GET[$TempOp[0]]))){
					if($GLOBALS['FrameworkHelpLanguage']=='CN'){
						$ModuleError='84PHP Error#Receive->Get():字段“'.$TempOp[0].'”缺失或值为空。如果字段不允许空值，请参照说明文档设置正确的参数。';
					}
					else{
						$ModuleError='84PHP Error#Receive->Get():The field "'.$TempOp[0].'" is missing or the value is empty. If the field not allows null values, refer to the specification document to set the correct parameters.';
					}
					Wrong::Report($ModuleError);
				}
			}
		}
		if($FromCheck==TRUE){
			$this->FromCheck($TokenCheck);
		}
		$Return=array();
		foreach ($_GET as $Key=>$Val) {
			if($SafeCheck==TRUE){
				$Val=$this->SafeCheck($Val);
			}
			$GetArray=array($Key=>$Val);
			$Return=array_merge($Return,$GetArray);
		}
		return $Return;
	}
	
	//Cookie过滤接收
	public function Cookie($FieldCheck=NULL){
		$Return=array();
		if(!empty($FieldCheck)&&is_array($FieldCheck)){
			foreach ($FieldCheck as $Val){
				$TempOp=explode(',',$Val);
				if((!isset($_COOKIE[$TempOp[0]]))||(isset($TempOp[1])&&strtoupper($TempOp[1])=='TRUE'&&empty($_COOKIE[$TempOp[0]]))){
					if($GLOBALS['FrameworkHelpLanguage']=='CN'){
						$ModuleError='84PHP Error#Receive->Cookie():字段“'.$TempOp[0].'”缺失或值为空。如果字段不允许空值，请参照说明文档设置正确的参数。';
					}
					else{
						$ModuleError='84PHP Error#Receive->Cookie():The field "'.$TempOp[0].'" is missing or the value is empty. If the field not allows null values, refer to the specification document to set the correct parameters.';
					}
					Wrong::Report($ModuleError);
				}
			}
		}
		foreach ($_COOKIE as $Key=>$Val) {
			$Val=$this->SafeCheck($Val);
			$CookieArray=array($Key=>$Val);
			$Return=array_merge($Return,$CookieArray);
		}
		return $Return;
	}

	//Json过滤
	public function Json($JsonString,$FieldCheck=NULL){
		$TempArray=@json_decode($JsonString,TRUE);
		if(empty($TempArray)){
			return NULL;
		}
		$Return=array();
		if(!empty($FieldCheck)&&is_array($FieldCheck)){
			foreach ($FieldCheck as $Val){
				$TempOp=explode(',',$Val);
				if((!isset($TempArray[$TempOp[0]]))||(isset($TempOp[1])&&strtoupper($TempOp[1])=='TRUE'&&empty($TempArray[$TempOp[0]]))){
					if($GLOBALS['FrameworkHelpLanguage']=='CN'){
						$ModuleError='84PHP Error#Receive->Json():字段“'.$TempOp[0].'”缺失或值为空。如果字段不允许空值，请参照说明文档设置正确的参数。';
					}
					else{
						$ModuleError='84PHP Error#Receive->Json():The field "'.$TempOp[0].'" is missing or the value is empty. If the field not allows null values, refer to the specification document to set the correct parameters.';
					}
					Wrong::Report($ModuleError);
				}
			}
		}
		foreach ($TempArray as $Key=>$Val) {
			if(!is_array($Val)){
				$Val=$this->SafeCheck($Val);
			}
			$JsonArray=array($Key=>$Val);
			$Return=array_merge($Return,$JsonArray);
		}
		return $Return;
	}

	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Receive:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'FromCheck()'."\r\n".'SafeCheck()'."\r\n".'Post()'."\r\n".'Get()'."\r\n".'Cookie()';
		}
		else{
			$ModuleError="84PHP Error#Receive:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'FromCheck()'."\r\n".'SafeCheck()'."\r\n".'Post()'."\r\n".'Get()'."\r\n".'Cookie()';
		}
		Wrong::Report($ModuleError);
	}
}