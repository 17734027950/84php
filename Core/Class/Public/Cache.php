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

  V1.0.2
*/

//模块对应分类
$ModuleArray=array(
'Cache'=>'Base',
'Dir'=>'Base',
'Wrong'=>'Base',
'Img'=>'Base',
'Load'=>'Base',
'Receive'=>'Base',
'Send'=>'Base',
'Sql'=>'Base',
'Tool'=>'Base',
'Db'=>'Module',
'Ftp'=>'Module',
'Mail'=>'Module',
'Page'=>'Module',
'Sms'=>'Module',
'Pay'=>'Module',
'Pdo'=>'Module',
'Vcode'=>'Module',
);

//模板目录
$TPath='/Source/Template';
$DPath='/Source/Data';
$APath='/Source/Action';

//模板编译规则
$CacheMatch=array(
				'001_Echo'=>'/\{\$([a-zA-Z_\x7f-\xff][a-zA-Z\[\]\'\'""0-9_\x7f-\xff]*)\}/',
				'002_LoopForeach'=>'/\{(loop|foreach) \$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/',
				'003_LoopForeach'=>'/\{\/(loop|foreach)}/',
				'004_IfElse'=>'/\{if(.*?)\}/i',
				'005_IfElse'=>'/\{(else if|elseif) (.*?)\}/i',
				'006_IfElse'=>'/\{else\}/',
				'007_IfElse'=>'/\{\/if\}/',
				'008_Notes'=>'/\{(\#|\*)(.*?)(\#|\*)\}/'
			);
$CacheReplace=array(
				'001_Echo'=>'<?php echo $\\1; ?>',
				'002_LoopForeach'=>'<?php foreach($\\2 AS $Key => $Val) { ?>',
				'003_LoopForeach'=>'<?php } ?>',
				'004_IfElse'=>'<?php if(\\1){ ?>',
				'005_IfElse'=>'<?php }else if(\\2){ ?>',
				'006_IfElse'=>'<?php }else{ ?>',
				'007_IfElse'=>'<?php } ?>',
				'008_Notes'=>''
			);
?>