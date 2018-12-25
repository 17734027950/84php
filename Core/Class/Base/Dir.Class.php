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

class Dir{
	//目录文件属性
	public function State($PathArray){
		clearstatcache();
		$Return=array();
		foreach ($PathArray as $Key => $Val){
			$TempArray=array();

			if(file_exists($GLOBALS['RootPath'].$Val)){
				if (is_readable($GLOBALS['RootPath'].$Val)){
					$TempArray['Read']='Yes';
				}
				else{
					$TempArray['Read']='No';
				}
				
				if (is_writable($GLOBALS['RootPath'].$Val)){
					$TempArray['Write']='Yes';
				}
				else{
					$TempArray['Write']='No';
				}
				
				if(is_dir($GLOBALS['RootPath'].$Val)){
					if (is_executable($GLOBALS['RootPath'].$Val)){
						$TempArray['Execute']='Yes';
					}
					else{
						$TempArray['Execute']='No';
					}
				}
			}
			else{
				$TempArray=array();
			}
			$Return[$Val]=$TempArray;
		}
		return $Return;
	}
	
	//目录大小调用
	private function SizeCall($Dir){
		$DirSize=0;
		if(file_exists($Dir)&&$DirHandle=@opendir($Dir)){
			while($FileName=readdir($DirHandle)){
				if($FileName!="."&&$FileName!=".."){
					$SubFile=$Dir."/".$FileName;
					if(is_dir($SubFile))
						$DirSize+=$this->SizeCall($SubFile);
					if(is_file($SubFile))
						$DirSize+=filesize($SubFile);
				}
			}
			closedir($DirHandle);
			return $DirSize;
		}
		else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Dir->SizeCall():目录或文件不存在，也可能是权限不足。';
			}
			else{
				$ModuleError='84PHP Error#Dir->SizeCall():Directory or file does not exist, or may be insufficient privileges.';
			}
			Wrong::Report($ModuleError);
		}
	}
	
	//目录大小
	public function Size($Dir,$Unit=NULL){
		$DirSize=$this->SizeCall($GLOBALS['RootPath'].$Dir);
		
		if($Unit=='KB'){
			$DirSize=round($DirSize/pow(1024,1),2);
			return $DirSize;
		}
		elseif($Unit=='MB'){
			$DirSize=round($DirSize/pow(1024,2),2);
			return $DirSize;
		}
		elseif($Unit=='GB'){
			$DirSize=round($DirSize/pow(1024,3),2);
			return $DirSize;
		}
		else{
			return $DirSize;
		}
	}
	
	//删除目录调用
	private function DeleteCall($Dir){
		if(file_exists($Dir)){
			if($DirHandle=@opendir($Dir)){
				while($FileName=readdir($DirHandle)){
					if($FileName!="."&&$FileName!=".."){
						$SubFile=$Dir."/".$FileName;
						if(is_dir($SubFile))
							$this->DelCall($SubFile);
						if(is_file($SubFile))
							unlink($SubFile);
					}
				}
				closedir($DirHandle);
				rmdir($Dir);
			}
			else{
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Dir->DeleteCall():目录或文件打开失败，也有可能是权限不足或文件被占用。';
				}
				else{
					$ModuleError='84PHP Error#Dir->DeleteCall():Failure to open directory or file may also result in insufficient privileges or files being occupied.';
				}
				Wrong::Report($ModuleError);
			}
		}
	}
	
	//删除目录
	public function Delete($Dir){
		$Dir=$GLOBALS['RootPath'].$Dir;
		$this->DeleteCall($Dir);
	}
	
	//复制目录调用
	private function CopyCall($From,$To){
		if(!file_exists($From)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Dir->CopyCall():目录或文件不存在，也可能是权限不足。';
			}
			else{
				$ModuleError='84PHP Error#Dir->CopyCall():Directory or file does not exist, or may be insufficient privileges.';
			}
			Wrong::Report($ModuleError);
		}
		if(is_file($To)){
			exit;
		}
		if(!file_exists($To)){
			mkdir($To);
		}
		if($DirHandle=@opendir($From)){
			while($FileName=readdir($DirHandle)){
				if($FileName!="." && $FileName!=".."){
					$FromPath=$From."/".$FileName;
					$ToPath=$To."/".$FileName;
					if(is_dir($FromPath)){
						$this->CopyCall($FromPath,$ToPath);
					}
					if(is_file($FromPath)){
						copy($FromPath,$ToPath);
					}
				}
			}
			closedir($DirHandle);
		}
		else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Dir->CopyCall():目录或文件打开失败，也有可能是权限不足或文件被占用。';
			}
			else{
				$ModuleError='84PHP Error#Dir->CopyCall():Failure to open directory or file may also result in insufficient privileges or files being occupied.';
			}
			Wrong::Report($ModuleError);
		}
	}
	
	//复制目录
	public function Copy($From,$To){
		$From=$GLOBALS['RootPath'].$From;
		$To=$GLOBALS['RootPath'].$To;
		$this->CopyCall($From, $To);
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Dir:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'State()'."\r\n".'Size()'."\r\n".'Del()'."\r\n".'Copy()';
		}
		else{
			$ModuleError="84PHP Error#Dir:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'State()'."\r\n".'Size()'."\r\n".'Del()'."\r\n".'Copy()';
		}
		Wrong::Report($ModuleError);
	}
}