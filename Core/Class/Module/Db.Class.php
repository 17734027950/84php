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

require (dirname(__FILE__)."/../Public/Db.php");

class Db{
	private $Mysqli;
	private $NowDb;
		
	public function __construct(){
		$this->Connect();
	}

	//选择数据库
	public function Choose($ChooseDb){
		$this->Mysqli->close();
		if(empty($GLOBALS['DbInfo'][$ChooseDb])){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Db->Choose():配置文件中不存在选定的数据库。';
			}
			else{
				$ModuleError='84PHP Error#Db->Choose():There is no selected database in the configuration file.';
			}
			Wrong::Report($ModuleError);
		}
		$this->NowDb=$ChooseDb;
		$this->Connect();
	}
	
	//连接数据库
	private function Connect(){
		if(empty($this->NowDb)){
			$this->NowDb='default';
		}
		$this->Mysqli=new mysqli($GLOBALS['DbInfo'][$this->NowDb]['address'],$GLOBALS['DbInfo'][$this->NowDb]['username'],$GLOBALS['DbInfo'][$this->NowDb]['password'],$GLOBALS['DbInfo'][$this->NowDb]['dbname'],$GLOBALS['DbInfo'][$this->NowDb]['port']);
		if($this->Mysqli->connect_errno){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Db->Connect():连接数据库失败，请检查：网络是否通畅、端口（默认为3306）、用户名、密码、数据库名称是否正确。';
			}
			else{
				$ModuleError='84PHP Error#Db->Connect():Failed to connect to the database. Please check if the network is smooth, port (default is 3306), user name, password, and database name are correct.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nDetail:".$this->Mysqli->connect_error;
			Wrong::Report($ModuleError);
		}
	}
		
	//备份
	public function BackUp($Path){
		$Path=$GLOBALS['RootPath'].$Path;
		if(!file_exists($Path)){
			mkdir($Path);
		}
		$this->Mysqli->query('set names \'utf8\'');
		$SQLContext='set charset utf8;'."\r\n";
		$AllTables=$this->Mysqli->query('show tables');
		while ($Result=$AllTables->fetch_array()){
			$Table=$Result[0];
			$TableField=$this->Mysqli->query("show create table `$Table`");
			$Sql=$TableField->fetch_array();
			$SQLContext.=$Sql['Create Table'].';'."\r\n";
			$TableField->free();
			$TableData=$this->Mysqli->query("select * from `$Table`");
			
			while ($Data=$TableData->fetch_assoc()){
				$Key=array_keys($Data);
				$Key=array_map('addslashes',$Key);
				$Key=join('`,`',$Key);
				$Key='`'.$Key.'`';
				$Val=array_values($Data);
				$Val=array_map('addslashes',$Val);
				$Val=join('\',\'',$Val);
				$Val='\''.$Val.'\'';
				$SQLContext.='insert into `'.$Table.'`('.$Key.') values('.$Val.');'."\r\n";
			}
			$TableData->free();
		}
		
		$Fp=@fopen($Path,'w');
		if(!$Fp){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Db->BackUp():打开文件失败，可能是权限不足或文件被占用。';
			}
			else{
				$ModuleError='84PHP Error#Db->BackUp():Failed to open file, may be insufficient privileges or files being occupied.';
			}
			Wrong::Report($ModuleError);
		}
		
		if(!fwrite($Fp,$SQLContext)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Db->BackUp():写入文件失败，可能是磁盘空间不足。';
			}
			else{
				$ModuleError='84PHP Error#Db->BackUp():Failure to write to file may be insufficient disk space.';
			}
			Wrong::Report($ModuleError);
		};
		fclose($Fp);
		$AllTables->free();
		
		return TRUE;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Db:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'BackUp()';
		}
		else{
			$ModuleError="84PHP Error#Db:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'BackUp()';
		}
		Wrong::Report($ModuleError);
	}
		
	//关闭数据库连接
	public function __destruct(){
		$this->Mysqli->close();
	}
}