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

//支付宝支付公共参数
$Pid='';
$AliKey='';
$AliNotifyUrl='';
$ReturnUrl='';

//微信扫码支付公共参数
$Appid='';
$MchId='';
$WxKey='';
$WxNotifyUrl='';
$WxSceneInfo=array(
	'h5_info'=>array(
		'type'=>'',
		'wap_url'=>'',
		'wap_name'=>''
	)
);