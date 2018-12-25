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

require (dirname(__FILE__).'/../../Common.php');
require (dirname(__FILE__)."/../Public/Tool.php");

class Tool{

	//随机字符
	public function Random($Mode='AaN',$StringLength=32){
		$String=NULL;
		$NWord='0123456789';
		$AUpperWord='QWERTYUIOPASDFGHJKLZXCVBNM';
		$ALowerWord='qwertyuiopasdfghjklzxcvbnm';
		$Word=NULL;
		if(strstr($Mode,'A')){
			$Word.=$AUpperWord;
		}
		if(strstr($Mode,'a')){
			$Word.=$ALowerWord;
		}
		if(strstr($Mode,'N')){
			$Word.=$NWord;
		}
		if(empty($Mode)){
			$Word=$NWord.$ALowerWord.$AUpperWord;
		}
		if(!empty($Word)){
			for($n=0;$n<$StringLength;$n++){
				$Random=mt_rand(0,strlen($Word)-1);
				$String.=$Word[$Random];
			}
		}
		return $String;
	}
	
	//设置Token
	public function Token(){
		if(!isset($_SESSION)){
			session_start();
		}
		$Token=$this->Random();

		$_SESSION['Token']=array(
								'token'=>$Token,
								'time'=>time()
							);
		return $Token;
	}
	
	//允许标签携带链接参数
	private function LinkInTag($WaitReplace){
		$Return=$WaitReplace[0];
		$Return=str_replace(array('＜','＞','＆','＃'),array('<','>','&','#'),$WaitReplace[0]);
		return $Return;
	}
	
	//还原HTML标记
	public function Html($Str,$Tag_media=TRUE,$Tag_a=TRUE,$Tag_div=FALSE){
		if(!$Tag_media){
			$GLOBALS['HtmlTag']['/<img(.*?)>/']='＜img$1＞';
		}
		if($Tag_div){
			$GLOBALS['HtmlTag']['/＜div(.*?)＞/']='<div$1>';
			$GLOBALS['HtmlTag']['/＜\/div＞/']='</div>';
		}
		foreach ($GLOBALS['HtmlTag'] as $Key=>$Val) {
			$Str=preg_replace($Key,$Val,$Str);
		}
		if($Tag_media){
			$Str=preg_replace_callback($GLOBALS['HtmlMediaTag'],array($this,'LinkInTag'),$Str);
			$Str=preg_replace_callback($GLOBALS['HtmlMediaEndTag'],array($this,'LinkInTag'),$Str);
		}
		if($Tag_a){
			$Str=preg_replace_callback('/＜a(.*?)＞/',array($this,'LinkInTag'),$Str);
			$Str=preg_replace('/＜\/a＞/','</a>',$Str);
		}
		$StrArray=array(
			'（'=>'(',
			'）'=>')',
			'﹡'=>'*',
			'＇'=>'\'',
			'？'=>'?',
			'@＠'=>'@@',
			'＋'=>'+',
			'；'=>';',
			'＝'=>'=',
			'&#'=>'＆＃'
		);
		foreach ($StrArray as $Key=>$Val) {
			$Str=str_replace($Key,$Val,$Str);
		}
		return $Str;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Tool:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Random()'."\r\n".'Token()'."\r\n".'HtmlTag()';
		}
		else{
			$ModuleError="84PHP Error#Tool:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Random()'."\r\n".'Token()'."\r\n".'HtmlTag()';
		}
		Wrong::Report($ModuleError);
	}
}