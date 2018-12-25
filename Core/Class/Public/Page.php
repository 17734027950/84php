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

//对分页结果进行操作，可以注入Js,也可以是Json，也可以通过$GLOBALS[]赋值给预先定义的变量
echo "<script language=\"javascript\" type=\"text/javascript\">\r\n
	var NowPage=$NowPage;\r\n
	var TotalPage=$TotalPage;\r\n
	var TotalNumber=$TotalNumber;\r\n
	var StartNumber=".($Start+1).";\r\n
	var EndNumber=$End;\r\n
	</script>";