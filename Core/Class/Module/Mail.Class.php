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

require (dirname(__FILE__)."/../Public/Mail.php");

class Mail{
	
	//Jmail发送
	public function Jsend($To,$Title,$Content,$NewUserName=NULL,$NewPassWord=NULL,$FromAddress=NULL){
		$Jmail=new COM("Jmail.Message");
		if(!$Jmail){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Mail->Jsend():Jmail拓展没有启用，或配置不正确。';
			}
			else{
				$ModuleError='84PHP Error#Mail->Jsend():Jmail expansion is not enabled or configured incorrectly.';
			}
			Wrong::Report($ModuleError);
		}
		$Jmail->Silent=TRUE;
		$Jmail->Logging=TRUE;
		$Jmail->CharSet='utf-8';
		$Jmail->ContentType="Text/html";
		if(!empty($NewUserName)){
			$Jmail->MailServerUsername=$NewUserName;
		}
		else{
			$Jmail->MailServerUsername=$GLOBALS['UserName'];
		}
		
		if(!empty($NewPassWord)){
			$Jmail->MailServerPassword=$NewPassWord;
		}
		else{
			$Jmail->MailServerPassword=$GLOBALS['PassWord'];
		}
		$Jmail->FromName='"=?utf-8?B?'.base64_encode($GLOBALS['FromName']).'?="';
		if(!empty($NewFromAddress)){
			$Jmail->From=$NewFromAddress;
		}
		else{
			$Jmail->From=$GLOBALS['FromAddress'];
		}
		$Jmail->AddRecipient($To);
		$Jmail->Subject='=?utf-8?B?'.base64_encode($Title).'?=';
		$Jmail->Body=$Content;
		$JmailState=$Jmail->Send($GLOBALS['Server']);
		if($JmailState){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	//Socket发送
	public function Ssend($To,$Title,$Content,$Timeout=15,$NewUserName=NULL,$NewPassWord=NULL,$FromAddress=NULL){
		$NewAction=NULL;
		$FwriteState=TRUE;
		$Fsock=fsockopen($GLOBALS['Server'],$GLOBALS['Port'],$Errno,$Errstr,$Timeout);
		if(!$Fsock&&$Errno===0){
			return FALSE;
		}
		stream_set_blocking($Fsock,1);
		$LastMessage=fgets($Fsock,512);
		$NewAction='EHLO '.'=?utf-8?B?'.base64_encode($GLOBALS['FromName']).'?='."\r\n";
		if(fwrite($Fsock,$NewAction)){
			$FwriteState=FALSE;
		}
		$LastMessage==fgets($Fsock,512);
		while(TRUE){
			$LastMessage=fgets($Fsock,512);
			if((substr($LastMessage,3,1)!='-')or(empty($LastMessage))){
				break;
			}
		}
		$NewAction="AUTH LOGIN\r\n";
		if(fwrite($Fsock,$NewAction)){
			$FwriteState=FALSE;
		}
		$LastMessage=fgets($Fsock,512);
		if(!empty($NewUserName)){
			$NewAction=base64_encode($NewUserName)."\r\n";
		}
		else{
			$NewAction=base64_encode($GLOBALS['UserName'])."\r\n";
		}
		if(fwrite($Fsock,$NewAction)){
			$FwriteState=FALSE;
		}
		$LastMessage=fgets($Fsock,512);
		if(!empty($NewPassWord)){
			$NewAction=base64_encode($NewPassWord)."\r\n";
		}
		else{
			$NewAction=base64_encode($GLOBALS['PassWord'])."\r\n";
		}
		if(fwrite($Fsock,$NewAction)){
			$FwriteState=FALSE;
		}
		$LastMessage=fgets($Fsock,512);
		if(!empty($NewFromAddress)){
			$NewAction='MAIL FROM: <'.$NewFromAddress.">\r\n";
		}
		else{
			$NewAction='MAIL FROM: <'.$GLOBALS['FromAddress'].">\r\n";
		}

		if(fwrite($Fsock,$NewAction)){
			$FwriteState=FALSE;
		}
		$LastMessage=fgets($Fsock,512);
		$NewAction='RCPT TO: <'.$To."> \r\n";
		if(fwrite($Fsock,$NewAction)){
			$FwriteState=FALSE;
		}
		$LastMessage=fgets($Fsock,512);
		$NewAction="DATA\r\n";
		if(fwrite($Fsock,$NewAction)){
			$FwriteState=FALSE;
		}
		$LastMessage=fgets($Fsock,512);
		if(!empty($NewFromAddress)){
			$Head='From: =?utf-8?B?'.base64_encode($GLOBALS['FromName']).'?= <'.$NewFromAddress.">\r\n";
		}
		else{
			$Head='From: =?utf-8?B?'.base64_encode($GLOBALS['FromName']).'?= <'.$GLOBALS['FromAddress'].">\r\n";
		}
		$Head.='To: '.$To."\r\n";
		$Head.='Subject: =?utf-8?B?'.base64_encode($Title)."?=\r\n";
		$Head.="Content-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding:8bit\r\n";
		$Content=$Head."\r\n".$Content;
		$Content.="\r\n.\r\n";
		if(fwrite($Fsock,$Content)){
			$FwriteState=FALSE;
		}
		$NewAction="QUIT\r\n";
		fclose($Fsock);
		return $FwriteState;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Mail:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Jsend()'."\r\n".'Ssend()';
		}
		else{
			$ModuleError="84PHP Error#Mail:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Jsend()'."\r\n".'Ssend()';
		}
		Wrong::Report($ModuleError);
	}
}