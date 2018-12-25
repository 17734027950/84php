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

  V1.2.0
*/

require (dirname(__FILE__)."/../Public/Pay.php");
require_once (dirname(__FILE__)."/../Base/Send.Class.php");

class Pay{
	
	//支付宝支付接口
	public function Alipay($OrderId,$Subject,$Total,$QRMode=FALSE,$QRWidth=NULL){
		$PostArray=array(
				'service'=>'create_direct_pay_by_user',
				'partner'=>$GLOBALS['Pid'],
				'_input_charset'=>'utf-8',
				'notify_url'=>$GLOBALS['AliNotifyUrl'],
				'return_url'=>$GLOBALS['ReturnUrl'],
				'out_trade_no'=>$OrderId,
				'subject'=>$Subject,	
				'payment_type'=>'1',
				'total_fee'=>$Total,
				'seller_id'=>$GLOBALS['Pid'],
				'it_b_pay'=>'1h',
				);
		if($QRMode){
			if(!empty($QRWidth)){
				$QRArray=array('qr_pay_mode'=>'4','qrcode_width'=>$QRWidth);
				$PostArray=array_merge($PostArray,$QRArray);
			}
			else{
				$QRArray=array('qr_pay_mode'=>'3');
				$PostArray=array_merge($PostArray,$QRArray);
			}
		}
		ksort($PostArray);
		$SortString=NULL;
		foreach ($PostArray as $Key => $Val) {
			$SortString.=$Key.'='.$Val.'&';
		}
		$SortString=substr($SortString, 0, -1);
		$Md5=md5($SortString.$GLOBALS['AliKey']);
		$SortString.='&sign='.$Md5.'&sign_type=MD5';
		return 'https://mapi.alipay.com/gateway.do?'.$SortString;
	}
	//微信支付接口
	public function Wxpay($OrderId,$Subject,$Total,$Mode='NATIVE',$Ip=NULL,$OpenID=NULL){
		if(empty($Ip)){
			$Ip=$_SERVER['REMOTE_ADDR'];
 		}
		$String=NULL;
		$Word='0123456789qwertyuiopasdfghjklzxcvbnm';
		for($n=1;$n<=31;$n++){
			$Random=mt_rand(0,34);
			$String.=$Word[$Random];
		}
		$ExpireTime=date('YmdHis',time()+3600);
		$Total=$Total*100;
		$PostArray=array(
				'appid'=>$GLOBALS['Appid'],
				'mch_id'=>$GLOBALS['MchId'],
				'nonce_str'=>$String,
				'body'=>$Subject,
				'out_trade_no'=>$OrderId,
				'total_fee'=>$Total,
				'spbill_create_ip'=>$Ip,
				'time_expire'=>$ExpireTime,
				'notify_url'=>$GLOBALS['WxNotifyUrl'],
				'trade_type'=>$Mode,
				);
		if($Mode=='JSAPI'){
			$PostArray['openid']=$OpenID;
		}
		if($Mode=='MWEB'){
			$PostArray['scene_info']=json_encode($GLOBALS['WxSceneInfo']);
		}
		ksort($PostArray);
		$SortString=NULL;
		foreach ($PostArray as $Key => $Val) {
			$SortString.=$Key.'='.$Val.'&';
		}
		$Md5=md5($SortString.'key='.$GLOBALS['WxKey']);
		
		$Data='<?xml version=\'1.0\'?>'."\r\n".
		'<xml>'."\r\n".
		'<appid>'.$GLOBALS['Appid'].'</appid>'."\r\n".
		'<mch_id>'.$GLOBALS['MchId'].'</mch_id>'."\r\n".
		'<nonce_str>'.$String.'</nonce_str>'."\r\n".
		'<body>'.$Subject.'</body>'."\r\n".
		'<out_trade_no>'.$OrderId.'</out_trade_no>'."\r\n".
		'<total_fee>'.$Total.'</total_fee>'."\r\n".
		'<spbill_create_ip>'.$Ip.'</spbill_create_ip>'."\r\n".
		'<time_expire>'.$ExpireTime.'</time_expire>'."\r\n".
		'<notify_url>'.$GLOBALS['WxNotifyUrl'].'</notify_url>'."\r\n".
		'<trade_type>'.$Mode.'</trade_type>'."\r\n";
		if($Mode=='JSAPI'){
			$Data.='<openid>'.$OpenID.'</openid>'."\r\n";
		}
		if($Mode=='MWEB'){
			$Data.='<scene_info>'.json_encode($GLOBALS['WxSceneInfo']).'</scene_info>'."\r\n";
		}
		$Data.='<sign>'.$Md5.'</sign>'."\r\n".
		'</xml>
		';
		$Send=new Send;
		$Send=$Send->Post('https://api.mch.weixin.qq.com/pay/unifiedorder',$Data,'Content-Type: text/xml; charset=UTF-8',1);
		
		xml_parse_into_struct(xml_parser_create(),$Send,$ReturnArray);
		$Return=FALSE;
		if(empty($ReturnArray)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Pay->Wxpay():远程网关无响应，请检查网络是否通畅，网络延迟是否正常。';
			}
			else{
				$ModuleError='84PHP Error#Pay->Wxpay():The remote gateway has no response. Please check whether the network is smooth, and the network delay is normal.';
			}
			Wrong::Report($ModuleError);
		}
		$ReturnResult=TRUE;
		foreach($ReturnArray as $Val){
			if($Val['tag']=='RETURN_CODE'&&$Val['value']!='SUCCESS'){
				$ReturnResult=FALSE;
			}
			if(!$ReturnResult&&$Val['tag']=='RETURN_MSG'){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Pay->Wxpay():微信支付平台返回的错误信息：'.$Val['value'];
				}
				else{
					$ModuleError='84PHP Error#Pay->Wxpay():Error information returned by WeChat payment platform:'.$Val['value'];
				}
				Wrong::Report($ModuleError);
			}
			if($Val['tag']=='PREPAY_ID'&&$Mode=='JSAPI'){
				$Return=$Val['value'];
			}
			if($Val['tag']=='CODE_URL'&&$Mode=='NATIVE'){
				$Return=$Val['value'];
			}
			if($Val['tag']=='MWEB_URL'&&$Mode=='MWEB'){
				$Return=$Val['value'];
			}
		}
		return $Return;
	}
	
	//支付宝支付验签
	public function AlipayVerify(){
		$PostArray=$_POST;
		if(empty($PostArray)){
			return FALSE;
		}
		if($PostArray['trade_status']!='TRADE_SUCCESS'){
			return FALSE;
		}
		ksort($PostArray);
		$WillCheck=NULL;
		foreach($PostArray as $Key => $Val){
			if($Key!='sign'&&$Key!='sign_type'&&!empty($Val)){
				$WillCheck.=$Key.'=';
				$WillCheck.=$Val.'&';
			}
		}
		$WillCheck=substr($WillCheck, 0, -1);
		$Sign=md5($WillCheck.$GLOBALS['AliKey']);
		if($Sign!=$PostArray['sign']){
			return FALSE;
		}
		$Send=new Send;
		$NotifyResult=$Send->Get('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$GLOBALS['Pid'].'&notify_id='.$PostArray['notify_id']);
		if(strtoupper($NotifyResult)=='TRUE'){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	//微信支付验签
	public function WxpayVerify($Xml){
		if(empty($Xml)){
			return FALSE;
		}
		$XmlArray=json_decode(json_encode(simplexml_load_string($Xml,'SimpleXMLElement',LIBXML_NOCDATA)),TRUE);
		if(empty($XmlArray)){
			return FALSE;
		}
		ksort($XmlArray);
		$WillCheck=NULL;
		foreach($XmlArray as $Key => $Val){
			if($Key!='sign'&&!empty($Val)&&!is_array($Val)){
				$WillCheck.=$Key.'=';
				$WillCheck.=$Val.'&';
			}
		}
		$Sign=strtoupper(md5($WillCheck.'key='.$GLOBALS['WxKey']));
		if($Sign==$XmlArray['sign']){
			return $XmlArray;
		}
		else{
			return FALSE;
		}
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Pay:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Alipay()'."\r\n".'Wxpay()'."\r\n".'AlipayVerify()'."\r\n".'WxpayVerify()';
		}
		else{
			$ModuleError="84PHP Error#Pay:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Alipay()'."\r\n".'Wxpay()'."\r\n".'AlipayVerify()'."\r\n".'WxpayVerify()';
		}
		Wrong::Report($ModuleError);
	}
}