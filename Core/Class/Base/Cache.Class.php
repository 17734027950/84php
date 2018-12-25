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

require (dirname(__FILE__)."/../Public/Cache.php");

class Cache{
	private $IncludeArray;
	
	public function __construct(){
		$this->IncludeArray=array();
	}
	
	//目录文件检查
	public function Base($Dir=NULL){
		$this->CheckCall($GLOBALS['RootPath'].$GLOBALS['TPath'].$Dir);
		$this->CheckCall($GLOBALS['RootPath'].$GLOBALS['DPath'].$Dir,TRUE);
		return TRUE;
	}
	
	//目录文件检查调用
	private function CheckCall($RootDir,$DataTrans=FALSE){
		if(file_exists($RootDir)&&$DirHandle=@opendir($RootDir)){
			while($FileName=readdir($DirHandle)){
				if($FileName!="."&&$FileName!=".."){
					$AllFile=$RootDir."/".$FileName;
					$Exp=explode('.',$AllFile);
					if(is_dir($AllFile)){
						if(!$DataTrans){
							$NewFile=str_replace($GLOBALS['TPath'],'/Web',$AllFile);
						}
						else{
							$NewFile=str_replace($GLOBALS['DPath'],'/Web',$AllFile);
						}
						if(!file_exists($NewFile)){
							if(!@mkdir($NewFile)){
								if($GLOBALS['FrameworkHelpLanguage']=='CN'){
									$ModuleError='84PHP Error#Cache->CheckCall():创建目录失败"'.$NewFile.'"，可能是权限不足。';
								}
								else{
									$ModuleError='84PHP Error#Cache->CheckCall():Creating the directory "'.$NewFile.'" failure may be a lack of permissions.';
								}
								Wrong::Report($ModuleError);
							};
						}
						$this->CheckCall($AllFile,$DataTrans);
					}
					else if(strtoupper(end($Exp))=='PHP'){
						if(!$DataTrans){
							$DataFile=str_replace($GLOBALS['TPath'],$GLOBALS['DPath'],$AllFile);
							$ActionFile=str_replace('.php','.act.php',str_replace($GLOBALS['TPath'],$GLOBALS['APath'],$AllFile));
							$NewFile=str_replace($GLOBALS['TPath'],'/Web',$AllFile);
						}
						else{
							$DataFile=$AllFile;
							$NewFile=str_replace($GLOBALS['DPath'],'/Web',$AllFile);
						}
						if(!file_exists($NewFile)||(file_exists($NewFile)&&filemtime($AllFile)>filemtime($NewFile))||(file_exists($NewFile)&&file_exists($DataFile)&&(filemtime($DataFile)>filemtime($NewFile)))||(file_exists($NewFile)&&filemtime($NewFile)>time())){
							if(filemtime($AllFile)>time()){
								touch($AllFile);
							}
							if(file_exists($DataFile)&&filemtime($DataFile)>time()){
								touch($DataFile);
							}
							if(!$DataTrans){
								$this->Translate($AllFile,$NewFile,FALSE);
							}
							else{
								$this->Translate($AllFile,$NewFile,FALSE,TRUE);
							}
						}
						if(!$DataTrans&&file_exists($ActionFile)){
							$NewActionFile=str_replace('.php','.act.php',$NewFile);
							if(!file_exists($NewActionFile)||(file_exists($NewActionFile)&&(filemtime($ActionFile)>filemtime($NewActionFile)))||(file_exists($NewActionFile)&&(filemtime($NewActionFile)>time()))){
								if(filemtime($ActionFile)>time()){
									touch($ActionFile);
								}
								$this->Translate($ActionFile,$NewActionFile,TRUE);
							}
						}
					}
				}
			}
			closedir($DirHandle);
		}
		else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Cache->CheckCall():目录或文件不存在，也可能是权限不足。';
			}
			else{
				$ModuleError='84PHP Error#Cache->CheckCall():Directory or file does not exist, or may be insufficient privileges.';
			}
			Wrong::Report($ModuleError);
		}
	}	
	
	//模块语法编译调用
	private function FastClassCall($WaitReplace){
		$this->IncludeArray[]=$WaitReplace[3];
		return	$WaitReplace[0]=$WaitReplace[2].'$GLOBALS[\'Class'.$WaitReplace[3].'\']->'.$WaitReplace[4].";\r\n";
	}
	//得到核心目录相对路径
	private function GetCorePath($Path){
		$DirArray=explode('/',$Path);
		$CorePath='';
		foreach ($DirArray as $Val){
			if(!empty($Val)){
				$CorePath.='/..';
			}
		}
		return $CorePath;
	}
	
	//标签翻译
	private function Translate($From,$To,$ActionTrans=FALSE,$DataTranslate=FALSE){
		$this->IncludeArray=array();
		$Temp=NULL;
		$IncludeTemp=NULL;
		$NewClass=NULL;
		$File=NULL;
		$TempWait=NULL;
		$Template=NULL;
		if(!$DataTranslate){
			$DataSource=str_replace($GLOBALS['TPath'],$GLOBALS['DPath'],$From);
		}
		else{
			$DataSource=$From;
		}
		
		if($ActionTrans){
			$AutoCacheCode='$ClassCache->AutoCache(__FILE__,TRUE);'."\r\n";
		}
		else if($DataTranslate){
			$AutoCacheCode='$ClassCache->AutoCache(__FILE__,FALSE,TRUE);'."\r\n";
		}
		else{
			$AutoCacheCode='$ClassCache->AutoCache(__FILE__);'."\r\n";
		}

		if(file_exists($From)){
			if($ActionTrans==FALSE){
				$Fp=@fopen($From,'a+');
				if(!$Fp){
					if($GLOBALS['FrameworkHelpLanguage']=='CN'){
						$ModuleError='84PHP Error#Cache->Translate():目录或文件不存在，也可能是权限不足。';
					}
					else{
						$ModuleError='84PHP Error#Cache->Translate():Directory or file does not exist, or may be insufficient privileges.';
					}
					Wrong::Report($ModuleError);
				}
				if(!$DataTranslate&&filesize($From)>0){
					$Temp=@fread($Fp,filesize($From));
					$Template=preg_replace($GLOBALS['CacheMatch'],$GLOBALS['CacheReplace'],$Temp);
					fclose($Fp);
				}
			}
		}
		else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Cache->Translate():目录或文件打开失败，可能是权限不足或文件被占用。';
			}
			else{
				$ModuleError='84PHP Error#Cache->Translate():Failure to open directory or file. It may be insufficient privileges or files being occupied.';
			}
			Wrong::Report($ModuleError);
		}
		if(file_exists($DataSource)){
			$Fp=@fopen($DataSource,'a+');
			if(!$Fp){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Cache->Translate():目录或文件不存在，也可能是权限不足。';
				}
				else{
					$ModuleError='84PHP Error#Cache->Translate():Directory or file does not exist, or may be insufficient privileges.';
				}
				Wrong::Report($ModuleError);
			}
			if(filesize($DataSource)>0){
				$TempWait=@fread($Fp,filesize($DataSource));
				fclose($Fp);
				$TempWait=preg_replace('/(?:^|\n|\s+)\/\/.*/','',$TempWait);
				$TempWait=preg_replace("/\/\*(.|\r\n)*\*\//","\r\n",$TempWait);
				$File=preg_replace_callback('/(.*?)#(.*?)<(.*?)@(.*)>(.*)/',array($this,'FastClassCall'),$TempWait);
				$File=preg_replace('/(?:^|\n|\s+)#.*/','',$File);
				$this->IncludeArray=array_unique($this->IncludeArray);
				foreach($this->IncludeArray as $Include){
					foreach($GLOBALS['ModuleArray'] as $Key=>$Val){
						if($Include==$Key){
							$IncludeTemp.='require_once (dirname(__FILE__)."'.$this->GetCorePath(str_replace($GLOBALS['RootPath'],'',str_replace(strrchr($To,'/'),'',$To))).'/Core/Class/'.$Val.'/'.$Key.'.Class.php");'."\r\n";
							$NewClass.='$Class'.$Key.'=new '.$Key.";\r\n";
						}
					}
				}
				if(!empty($IncludeTemp)&&!empty($NewClass)){
					$File="<?php\r\n".$IncludeTemp."\r\n".$NewClass."?>\r\n".$File;
				}
			}
		}
		$HeadCode="<?php\r\n".'require_once (dirname(__FILE__)."'.$this->GetCorePath(str_replace($GLOBALS['RootPath'],'',str_replace(strrchr($To,'/'),'',$To))).'/Core/Common.php");'."\r\n".'if($GLOBALS[\'DebugMode\']){'."\r\n".'	$CacheChange=FALSE;'."\r\n"."\r\n".'	require_once (dirname(__FILE__)."'.$this->GetCorePath(str_replace($GLOBALS['RootPath'],'',str_replace(strrchr($To,'/'),'',$To))).'/Core/Class/Base/Cache.Class.php");'."\r\n".'	$ClassCache=new Cache'.";\r\n	".$AutoCacheCode.'	if($CacheChange){'."\r\n		require(__FILE__);\r\n		exit;\r\n	}\r\n}\r\n?>";
		if(file_exists($DataSource)){
			$HeadCode.=$File;
		}
		$File=$HeadCode."\r\n".$Template;
		$File=str_replace('exit;#',NULL,$File);
		$File=str_replace('session_start();','if(!isset($_SESSION)){'."\r\n	session_start();\r\n}\r\n",$File);
		$File=str_replace(';;',';',$File);
		$File=preg_replace("/(\?>(\\s*<\?php)+)/","\r\n",$File);
		$File=preg_replace("/(<\?(\\s*\r?\n)+)/","<?php\r\n",$File);
		$File=preg_replace("/(\r?\n(\\s*\r?\n)+)/","\r\n",$File);
		if(file_exists($To)){
			unlink($To);
		}
		$Fp=@fopen($To,'a+');
		if(!$Fp){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Cache->Translate():打开文件失败，可能是权限不足或文件被占用。';
			}
			else{
				$ModuleError='84PHP Error#Cache->Translate():Failed to open file, may be insufficient privileges or files being occupied.';
			}
			Wrong::Report($ModuleError);
		}
		if(!fwrite($Fp,$File)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Cache->Translate():写入文件失败,可能是权限不足，也可能是磁盘已满。';
			}
			else{
				$ModuleError='84PHP Error#Cache->Translate():Failure to write to file. It may be insufficient privileges or full disk.';
			}
			Wrong::Report($ModuleError);
		};
		fclose($Fp);
	}
	
	//自动更新模块
	public function AutoCache($FilePath,$ActType=FALSE,$DataType=FALSE){
		if($GLOBALS['DebugMode']){
			$MainFile=str_replace($GLOBALS['RootPath'].'/Web','',str_replace('.act','',str_replace('\\','/',str_replace("//",'/',$FilePath))));
			
			if(!$DataType){
				$TemplateSource=$GLOBALS['RootPath'].$GLOBALS['TPath'].$MainFile;
				$ActionSource=$GLOBALS['RootPath'].$GLOBALS['APath'].str_replace('.php','.act.php',$MainFile);
			}
			$DataSource=$GLOBALS['RootPath'].$GLOBALS['DPath'].$MainFile;
			if(!$DataType&&!$ActType&&file_exists($TemplateSource)){
				if(filemtime($TemplateSource)>filemtime($FilePath)||(file_exists($DataSource)&&(filemtime($DataSource)>filemtime($FilePath)))||filemtime($FilePath)>time()){
					if(filemtime($TemplateSource)>time()){
						touch($TemplateSource);
					}
					if(file_exists($DataSource)&&filemtime($DataSource)>time()){
						touch($DataSource);
					}
					$GLOBALS['CacheChange']=TRUE;
				}
			}
			if($ActType&&file_exists($ActionSource)){
				if(filemtime($ActionSource)>filemtime($FilePath)||filemtime($FilePath)>time()){
					if(filemtime($ActionSource)>time()){
						touch($ActionSource);
					}
					$GLOBALS['CacheChange']=TRUE;
				}
			}
			if($DataType&&file_exists($DataSource)){
				if(filemtime($DataSource)>filemtime($FilePath)||filemtime($FilePath)>time()){
					if(filemtime($DataSource)>time()){
						touch($DataSource);
					}
					$GLOBALS['CacheChange']=TRUE;
				}
			}
			$this->CheckCall($GLOBALS['RootPath'].$GLOBALS['TPath']);
			$this->CheckCall($GLOBALS['RootPath'].$GLOBALS['DPath'],TRUE);
		}
	}
}