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

//还原html标记
$HtmlTag=array(
	'/＜(br|p|span|i|b|u|strong|h\d|hr|table|thead|tbody|tfoot|colgroup|col|ul|ol|li|em|sup|sub|tr|td|dt|dd|dl)(.*?)＞/'=>'<$1$2>',
	'/＜\/(br|p|span|i|b|u|strong|h\d|hr|table|thead|tbody|tfoot|colgroup|col|ul|ol|li|em|sup|sub|tr|td|dt|dd|dl)＞/'=>'</$1>'
);
//还原html多媒体标记
$HtmlMediaTag='/＜(img|video|audio|source)(.*?)＞/';
$HtmlMediaEndTag='/＜\/(video|audio)＞/';