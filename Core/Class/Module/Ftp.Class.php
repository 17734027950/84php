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

  V1.1.0
*/

require (dirname(__FILE__)."/../Public/Ftp.php");

class Ftp{

	//上传
	public function Up($From,$To,$ConnectInfo=NULL){
		$From=$GLOBALS['RootPath'].$From;
		if(!empty($ConnectInfo)&&is_array($ConnectInfo)){
			$Connect=ftp_connect($ConnectInfo['Server'],$ConnectInfo['Port']);
			$Login=ftp_login($Connect,$ConnectInfo['User'],$ConnectInfo['Password']);
		}
		else{
			$Connect=ftp_connect($GLOBALS['Server'],$GLOBALS['Port']);
			$Login=ftp_login($Connect,$GLOBALS['User'],$GLOBALS['Password']);
		}
		if((!$Connect)||(!$Login)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Ftp->Up():连接FTP服务器失败，请检查：网络是否通畅、端口（默认为21）、用户名、密码是否正确。';
			}
			else{
				$ModuleError='84PHP Error#Ftp->Up():Failed to connect to the FTP server. Please check if the network is smooth, port (default is 21), user name, and password are correct.';
			}
			Wrong::Report($ModuleError);
		}
		$Upload=ftp_put($Connect,$To,$From,FTP_ASCII); 
		ftp_close($Connect);
		if(!$Upload){
			return FALSE;
		}
		else{
			return TRUE;
		}
	}

	//下载
	public function Down($From,$To,$ConnectInfo){
		$To=$GLOBALS['RootPath'].$To;
		if(!empty($ConnectInfo)&&is_array($ConnectInfo)){
			$Connect=ftp_connect($ConnectInfo['Server'],$ConnectInfo['Port']);
			$Login=ftp_login($Connect,$ConnectInfo['User'],$ConnectInfo['Password']);
		}
		else{
			$Connect=ftp_connect($GLOBALS['Server'],$GLOBALS['Port']);
			$Login=ftp_login($Connect,$GLOBALS['User'],$GLOBALS['Password']);
		}
		if((!$Connect)||(!$Login)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Ftp->Down():连接FTP服务器失败，请检查：网络是否通畅、端口（默认为21）、用户名、密码是否正确。';
			}
			else{
				$ModuleError='84PHP Error#Ftp->Down():Failed to connect to the FTP server. Please check if the network is smooth, port (default is 21), user name, and password are correct.';
			}
			Wrong::Report($ModuleError);
		}
		$Download=ftp_get($Connect,$To,$From,FTP_ASCII); 
		ftp_close($Connect);
		if(!$Download){
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Ftp:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Up()'."\r\n".'Down()';
		}
		else{
			$ModuleError="84PHP Error#Ftp:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Up()'."\r\n".'Down()';
		}
		Wrong::Report($ModuleError);
	}
}