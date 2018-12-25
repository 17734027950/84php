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

class Load{

	//上传
	public function Up($Name,$Dir='/Upload',$SaveFileName=NULL,$Size=10240,$Type=NULL){
		$Path=$Dir;
		$Dir=$GLOBALS['RootPath'].'/Web'.$Dir;
		if(empty($Type)){
			$Type=array('gif','png','jpg');
		}
		$Size=intval($Size*1024);
		if(empty($_FILES[$Name]['tmp_name'])){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Load->Up():指定的表单字段中没有文件。请检查：表单中文件域的“name”属性是否正确，或表单是否设置了“enctype="multipart/form-data"”。';
			}
			else{
				$ModuleError='84PHP Error#Load->Up():There is no file in the specified form field. Please check: the "name" property of the file field in the form is correct, or whether the form has "enctype=" multipart/form-data "".';
			}
			Wrong::Report($ModuleError);
		}
		if($_FILES[$Name]['error'] > 0){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Load->Up():上传失败了，因为';
				switch ($_FILES[$Name]['error']){
					case 1:
						$ModuleError.='上传的文件超过了php.ini中“upload_max_filesize”选项限制的值。';
						break;
					case 2:
						$ModuleError.='上传文件的大小超过了 HTML 表单中“MAX_FILE_SIZE”选项指定的值。';  
						break;
					case 3:
						$ModuleError.='文件只有部分被上传。（网络不稳定，或者用户中断了传输。）'; 
						break;
					case 4:
						$ModuleError.='没有文件被上传。'; 
						break;
					default:
						$ModuleError.='未知错误。';
						break;
				}
			}
			else{
				$ModuleError='84PHP Error#Load->Up():Upload failed, because ';
				switch ($_FILES[$Name]['error']){
					case 1:
						$ModuleError.='the uploaded file exceeds the value restricted by the "upload_max_filesize" option in php.ini.';
						break;
					case 2:
						$ModuleError.='the size of the uploaded file exceeds the value specified in the MAX_FILE_SIZE option of the HTML form.';  
						break;
					case 3:
						$ModuleError.='only part of the file is uploaded. (the network is unstable, or the user has interrupted the transmission.)'; 
						break;
					case 4:
						$ModuleError.='no files are uploaded.'; 
						break;
					default:
						$ModuleError.='no files are uploaded.';
						break;
				}
			}
			Wrong::Report($ModuleError);
		}
		$Exp=explode('.',$_FILES[$Name]['name']);
		$Suffix=strtolower(end($Exp));
		
		if(!in_array($Suffix,$Type)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Load->Up():上传文件的后缀名不在被允许的列表中。';
			}
			else{
				$ModuleError='84PHP Error#Load->Up():The suffix name of the uploaded file is not in the allowed list.';
			}
			Wrong::Report($ModuleError);
		}
			
		if($_FILES[$Name]['size']>$Size){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Load->Up():上传文件的大小超过了限制。';
			}
			else{
				$ModuleError='84PHP Error#Load->Up():The size of the uploaded file exceeds the limit.';
			}
			Wrong::Report($ModuleError);
		}
		if(empty($SaveFileName)){
		$FileName=date("YmdHis").mt_rand(1000, 9999).".".$Suffix;
		}
		else{
			$FileName=$SaveFileName.".".$Suffix;
		}
		if(!file_exists($Dir)){
			mkdir($Dir);
		}
		if (is_uploaded_file($_FILES[$Name]['tmp_name'])) { 	
 	    	if(!move_uploaded_file($_FILES[$Name]['tmp_name'],$Dir.'/'.$FileName)){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Load->Up():将上传的文件移动到指定位置时发生错误。';
				}
				else{
					$ModuleError='84PHP Error#Load->Up():An error occurred when the uploaded file was moved to the specified location.';
				}
				Wrong::Report($ModuleError);
			}
 		}
		else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Load->Up():文件不是通过HTTP POST方式上传的。';
			}
			else{
				$ModuleError='84PHP Error#Load->Up():Files are not uploaded by HTTP POST.';
			}
			Wrong::Report($ModuleError);
		}
		return str_replace('/Web','',$Path.'/'.$FileName);
	}
	
	//下载
	public function Down($Url,$Path,$TimeLimit=86400){
		set_time_limit($TimeLimit);
		$Path=$GLOBALS['RootPath'].$Path;
		if(!file_exists($Path)){
			mkdir($Path);
		}
		$NewName=$Path.'/'.time().mt_rand(11111,99999).'-'.basename($Url);
		$File=@fopen($Url,'rb');
		if($File){
			$NewFile=@fopen($NewName,"wb");
			if(!$NewFile){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Load->Down():打开文件失败，可能是权限不足或文件被占用。';
				}
				else{
					$ModuleError='84PHP Error#Load->Down():Failed to open file, may be insufficient privileges or files being occupied.';
				}
				Wrong::Report($ModuleError);
			}
			$Size=NULL;
			$Headers=get_headers($Url,1);
			if((!array_key_exists("Content-Length",$Headers))){
				$Size=0;
			}
			$Size=$Headers["Content-Length"];
			if($NewFile){
				while(!feof($File)){
					if(!fwrite($NewFile,@fread($File,1024*8),1024*8)){
						if($GLOBALS['FrameworkHelpLanguage']=='CN'){
							$ModuleError='84PHP Error#Load->Down():写入文件失败，可能是磁盘空间不足。';
						}
						else{
							$ModuleError='84PHP Error#Load->Down():Failure to write to file may be insufficient disk space.';
						}
						Wrong::Report($ModuleError);
					};
				}
				fclose($NewFile);
			}
			fclose($File);
		}
		else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Load->Down():无法打开给定的URL。请检查URL是否错误，或网络是否通畅。';
			}
			else{
				$ModuleError='84PHP Error#Load->Down():Unable to open a given URL. Please check if URL is wrong, or whether the network is smooth.';
			}
			Wrong::Report($ModuleError);
		}
		return $NewName;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Load:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Up()'."\r\n".'Down()';
		}
		else{
			$ModuleError="84PHP Error#Load:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Up()'."\r\n".'Down()';
		}
		Wrong::Report($ModuleError);
	}
}