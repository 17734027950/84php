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

  V1.0.0
*/

//直接替换的字符
$DangerChar=array(
	'On'=>'Оn',//U+041E,&#1054;
	'on'=>'оn',//U+043E,&#1086;
	'|'=>'∣',//U+2223,&#8739;
	';'=>'；',//U+FF1B,&#65307;
	'&'=>'＆',//U+FF06,&#65286;
	'+'=>'＋',//U+FF0B,&#65291;
	'`'=>'ˋ',//U+02CB,&#715;
	'\\'=>'＼',//U+FF3C,&#65340;
	','=>'，',//U+FF0C,#65292;
	'('=>'（',//U+FF08,&#65288;
	')'=>'）',//U+FF09,&#65289;
	'#'=>'＃',//U+FF03,&#65283;
	'*'=>'﹡',//U+FE61,&#65121;
	'%'=>'％',//U+FF05,&#65285;
	'?'=>'？',//U+FF1F,&#65311;
	'<'=>'＜',//U+FF1C,&#10216;
	'>'=>'＞',//U+FF1E,&#65310;
	'@@'=>'@＠',//U+FF20,&#65312;
	'\''=>'＇',//U+FF07,&#65287;
	'='=>'＝',//U+FF1D,&#65309;
);

//来源域名检测
$BeforeDomainCheck=TRUE;

//Token超时时间（s）
$TokenExpTime=600;

//Emoji过滤
$KillEmoji=TRUE;