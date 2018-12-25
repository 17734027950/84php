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

require_once (dirname(__FILE__)."/../Base/Sql.Class.php");

class Page{
	
	//分页
	public function Base($Page,$Number,$Table,$WhereField=NULL,$WhereValue=NULL,$WhereOp='=',$Order=NULL,$Desc=FALSE,$Start=1,$Like=FALSE,$Index=NULL){
		
		$NowPage=intval($Page);
		$Number=intval($Number);
		$Start=intval($Start-1);
		$Select=new Sql;
		$TotalNumber=$Select->Total($Table,$WhereField,$WhereValue,$WhereOp,$Like,$Index);
		$TotalPage=intval(ceil($TotalNumber/$Number));
		if($TotalPage<$NowPage){
			$NowPage=$TotalPage;
			if($TotalPage==0){
				$NowPage=1;
			}
		}
		if($Number>0){
			if($NowPage>=2&&$Number!=0){
				$Page=$NowPage-1;
				$Start=$Start+$Page*$Number;
				$End=$Start+$Number;
			}
			else{
				$End=$Start+$Number;
			}
			$Limit=array($Start,$Number);
		}
		else{
			$End=-1;
			$Limit=array($Start,999999);
		}
		
		$Result=$Select->SelectMore($Table,$WhereField,$WhereValue,$WhereOp,$Order,$Desc,$Limit,$Like,$Index);
		if($Number!=0){
			$TotalPage=intval(ceil($TotalNumber/$Number));
		}
		else{
			$TotalPage=1;
		}
		if($End>$TotalNumber){
			$End=$TotalNumber;
		};
		require_once (dirname(__FILE__)."/../Public/Page.php");
		return $Result;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Page:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Base()';
		}
		else{
			$ModuleError="84PHP Error#Page:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Base()';
		}
		Wrong::Report($ModuleError);
	}
}