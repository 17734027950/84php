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

Class Send{
	//Post提交
	public function Post($Url,$Data,$Headers=NULL,$BuildQuery=TRUE){
		$Response=NULL;
		if (is_array($Data)&&$BuildQuery){
			$Data=http_build_query($Data);
		}
		$Params=array('http'=>array(
					'method'=>'POST',
					'content'=>$Data
		));
		if(!empty($Headers)){
			$Params['http']['header']=$Headers;
		}
		$Context=stream_context_create($Params);
		$Fopen=@fopen($Url,'rb',FALSE,$Context);
		if(!$Fopen){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Send->Post():无法打开给定的URL。请检查URL是否错误，或网络是否通畅。';
			}
			else{
				$ModuleError='84PHP Error#Send->Post():Unable to open a given URL. Please check if URL is wrong, or whether the network is smooth.';
			}
			Wrong::Report($ModuleError);
		}
		$Response=@stream_get_contents($Fopen);
		return $Response;
	}
	
	//Get提交
	public function Get($Url,$Data=NULL,$Headers=NULL){
		$Response=NULL;
		if(!empty($Data)){
			$Data='?'.http_build_query($Data);
		}
		$Params=array('http'=>array());
		if(!empty($Headers)) {
			$Params['http']['header']=$Headers;
		}
		$Context=stream_context_create($Params);
		$Fopen=@fopen($Url.$Data,'rb',FALSE,$Context);
		if(!$Fopen){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Send->Get():无法打开给定的URL。请检查URL是否错误，或网络是否通畅。';
			}
			else{
				$ModuleError='84PHP Error#Send->Get():Unable to open a given URL. Please check if URL is wrong, or whether the network is smooth.';
			}
			Wrong::Report($ModuleError);
		}
		$Response=@stream_get_contents($Fopen);
		return $Response;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Send:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Post()'."\r\n".'Get()';
		}
		else{
			$ModuleError="84PHP Error#Send:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Post()'."\r\n".'Get()';
		}
		Wrong::Report($ModuleError);
	}
}