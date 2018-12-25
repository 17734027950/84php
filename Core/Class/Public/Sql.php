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

//读写分离
$RW_Splitting=FALSE;

//数据库信息
$DbInfo=array(
	'default'=>array(
		'address'=>'localhost',
		'username'=>'root',
		'password'=>'0000',
		'dbname'=>'demo',
		'port'=>3306
	),
	
	//可在下方继续增加配置组
	'bbsdb'=>array(
		'address'=>'',
		'username'=>'',
		'password'=>'',
		'dbname'=>'',
		'port'=>3306
	)
);