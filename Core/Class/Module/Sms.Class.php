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

require (dirname(__FILE__)."/../Public/Sms.php");
require_once (dirname(__FILE__)."/../Base/Send.Class.php");

class Sms{
	
	//阿里云云通信接口特殊编码
	private function AliyunEncode($WaitEncode)
    {
        $TempEncode = urlencode($WaitEncode);
        $TempEncode=str_replace('+','%20',$TempEncode);
		$TempEncode=str_replace('*','%2A',$TempEncode);
		$TempEncode=str_replace('%7E','~',$TempEncode);
        return $TempEncode;
    }
	
	//阿里云云通信接口
	public function Aliyun($NumberArray,$TemplateCode,$Param=NULL){
		$PhoneNumber=NULL;
		$TempTimestamp=gmdate('Y-m-d\TH:i:s\Z');
		foreach ($NumberArray as $Key => $Val) {
			$PhoneNumber=$PhoneNumber.$Val.',';
		}
		$RecNum=substr($PhoneNumber,0,-1);
		$GetArray=array(
				'AccessKeyId'=>$GLOBALS['AliyunAccessKeyID'],
				'Timestamp'=>$TempTimestamp,
				'SignatureMethod'=>'HMAC-SHA1',
				'SignatureVersion'=>'1.0',
				'SignatureNonce'=>uniqid(mt_rand(0,0xffff),TRUE),
				'Format'=>'JSON',

				'Action'=>'SendSms',
				'Version'=>'2017-05-25',
				'RegionId'=>'cn-hangzhou',
				'PhoneNumbers'=>$PhoneNumber,
				'SignName'=>$GLOBALS['AliyunSignName'],
				'TemplateCode'=>$TemplateCode
				);
		if(!empty($Param)){
			$JsonArray=array('TemplateParam'=>json_encode($Param));
			$GetArray=array_merge($GetArray,$JsonArray);
		}
		ksort($GetArray);

		$SortString=NULL;
		foreach ($GetArray as $Key => $Val) {
			$SortString.=$this->AliyunEncode($Key).'='.$this->AliyunEncode($Val).'&';
		}
		$SortString=substr($SortString,0,-1);
        $Signed=base64_encode(hash_hmac('sha1','GET&%2F&'.$this->AliyunEncode($SortString),$GLOBALS['AliyunAccessKeySecret']."&",TRUE));
		$SignArray=array(
				'Signature'=>$Signed
		);
		$GetArray=array_merge($GetArray,$SignArray);

		
		$Send=new Send;
		$Send=$Send->Get('http://dysmsapi.aliyuncs.com/',$GetArray,'x-sdk-client: php/2.0.0');
		$Send=json_decode($Send);
		$Send=$Send->Code;
		if($Send=='OK'){
			$Send=TRUE;
		}
		else{
			$Send=FALSE;
		}
		return $Send;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Sms:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Aliyun()';
		}
		else{
			$ModuleError="84PHP Error#Sms:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Aliyun()';
		}
		Wrong::Report($ModuleError);
	}
}