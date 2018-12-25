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

V2.1.1
*/

require (dirname(__FILE__)."/../Public/Sql.php");

class Sql{
	private $Mysqli;
	private $NowDb;
	
	public function __construct(){
		if($GLOBALS['RW_Splitting']){
			$this->NowDb=$this->RandomSql();
		}
		$this->Connect();
	}
	
	//读写分离随机选择数据库
	private function RandomSql(){
		$AllSql=array();
		foreach($GLOBALS['DbInfo'] as $Key => $Val){
			$AllSql[]=$Key;
		}
		return $AllSql[mt_rand(1,(count($AllSql)-1))];
	}
	
	//选择数据库
	public function Choose($ChooseDb){
		$this->Mysqli->close();
		if(empty($GLOBALS['DbInfo'][$ChooseDb])){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Choose():配置文件中不存在选定的数据库。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Choose():There is no selected database in the configuration file.';
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
		$this->Mysqli=@new mysqli($GLOBALS['DbInfo'][$this->NowDb]['address'],$GLOBALS['DbInfo'][$this->NowDb]['username'],$GLOBALS['DbInfo'][$this->NowDb]['password'],$GLOBALS['DbInfo'][$this->NowDb]['dbname'],$GLOBALS['DbInfo'][$this->NowDb]['port']);
		if($this->Mysqli->connect_errno){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Connect():连接数据库失败，请检查：网络是否通畅、端口（默认为3306）、用户名、密码、数据库名称是否正确。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Connect():Failed to connect to the database. Please check if the network is smooth, port (default is 3306), user name, password, and database name are correct.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nDetail:".$this->Mysqli->connect_error;
			Wrong::Report($ModuleError);
		}
	}

	//查询一条数据
	public function Select($Table,$WhereField,$WhereValue,$Field=NULL){
		
		if(empty($Field)){
			$Field='*';
		}
		
		if($GLOBALS['RW_Splitting']&&$this->NowDb=='default'){
			$this->Mysqli->close();
			$this->NowDb=$this->RandomSql();
			$this->Connect();
		}

		$Result=$this->Mysqli->query('select '.$Field.' from `'.$Table.'` where `'.$WhereField.'`=\''.$WhereValue.'\'');
		if(!$Result){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Select():挑选数据失败，请检查：数据表、字段是否存在。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Select():Failed to select data, please check if data tables or fields exist.';
			}
			$ModuleDebugError="\r\n\r\n-------------------\r\nSQL String:".'select '.$Field.' from `'.$Table.'` where `'.$WhereField.'`=\''.$WhereValue.'\''."\r\n-------------------\r\nerrno:".$this->Mysqli->errno."\r\nDetail:".$this->Mysqli->error;
			Wrong::Report($ModuleError,$ModuleDebugError);
		}
		$Return=$Result->fetch_assoc();
		$Result->free();
		if(empty($Return)){
			$Return=array();
			return $Return;
		}
		if(!empty($Field)&&isset($Return[$Field])){
			return $Return[$Field];
		}
		return $Return;
	}
	
	//查询多条数据
	public function SelectMore($Table,$WhereField=NULL,$WhereValue=NULL,$WhereOp='=',$Order=NULL,$Desc=FALSE,$Limit=NULL,$Like=NULL,$Index=NULL){
				
		if(empty($WhereOp)){
			$WhereOp='=';
		}

		$Where='';
		if((!is_array($WhereField)&&!is_array($WhereValue))&&!empty($WhereField)){
			$Where=' where `'.$WhereField.'`'.$WhereOp.'\''.$WhereValue.'\'';
		}
		else if(is_array($WhereField)&&is_array($WhereValue)){
			$Where=' where';
			foreach($WhereField as $Key => $Val){
				if(!is_array($WhereOp)||empty($WhereOp[$Key])){
					$TempOp=array('=','AND');
				}
				else if(!is_array($WhereOp[$Key])){
					if(strpos($WhereOp[$Key],',')===FALSE){
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=array($WhereOp[$Key],'AND');
					}
					else{
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=explode(',',$WhereOp[$Key]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$Where.=' `'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\'';
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
				else{
					if(empty($WhereOp[$Key][0])){
						$TempOp=array('=','AND');
					}
					else if(strpos($WhereOp[$Key][0],',')===FALSE){
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=array($WhereOp[$Key][0],'AND');
					}
					else{
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=explode(',',$WhereOp[$Key][0]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$TempBeforeTag='';
					$TempAfterTag='';
					if(empty($WhereOp[$Key][1])){
					}
					else if(strpos($WhereOp[$Key][1],',')===FALSE){
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempBeforeTag=$WhereOp[$Key][1]; 
					}
					else{
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempTag=explode(',',$WhereOp[$Key][1]);
						$TempBeforeTag=$TempTag[0];
						$TempAfterTag=$TempTag[1];
					}

					$Where.=' '.$TempBeforeTag.'`'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\''.$TempAfterTag;
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
			}
		}
		if(!empty($Like)&&!is_array($WhereField)&&!is_array($WhereValue)&&!empty($WhereField)){
			switch ($Like){
				case 1:
					$Where=' where `'.$WhereField.'` like \''.$WhereValue.'\'';
					break;
				case 2:
					$Where=' where `'.$WhereField.'` not like \''.$WhereValue.'\'';
					break;
				default:
					break;
			}
		}
		if(!empty($Order)){
			$Order=' order by `'.$Order.'`';
			if($Desc){
				$Order.=' desc';
			}
		}
		else{
			$Order='';
		}
		if(!empty($Index)){
			$Index=' index('.$Index.')';
		}
		else{
			$Index='';
		}
		if(is_array($Limit)){
			if(!empty($Limit[1])){
				$Limit=' limit '.$Limit[0].','.$Limit[1];
			}
			else if(isset($Limit[0])){
				$Limit=' limit 0,'.$Limit[0];
			}
		}
		else{
			$Limit='';
		}
		
		if($GLOBALS['RW_Splitting']&&$this->NowDb=='default'){
			$this->Mysqli->close();
			$this->NowDb=$this->RandomSql();
			$this->Connect();
		}
		$Result=$this->Mysqli->query('select * from `'.$Table.'`'.$Where.$Order.$Limit.$Index,MYSQLI_USE_RESULT);
		if(!$Result){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->SelectMore():挑选数据失败，请检查：数据表、字段、索引是否存在。';
			}
			else{
				$ModuleError='84PHP Error#Sql->SelectMore():Failed to select data. Please check if data tables, fields, and indexes exist.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nSQL String:".'select * from `'.$Table.'`'.$Where.$Order.$Limit.$Index."\r\n-------------------\r\nerrno:".$this->Mysqli->errno."\r\nDetail:".$this->Mysqli->error;
			Wrong::Report($ModuleError);
		}
		
		$Return=$Result->fetch_all(MYSQLI_ASSOC);
		$Result->free();
		if(empty($Return)){
			$Return=array();
		}
		return $Return;
	}
		
	//记录总数
	public function Total($Table,$WhereField=NULL,$WhereValue=NULL,$WhereOp='=',$Like=NULL,$Index=NULL){
		
		if(empty($WhereOp)){
			$WhereOp='=';
		}

		$Where='';
		if((!is_array($WhereField)&&!is_array($WhereValue))&&!empty($WhereField)){
			$Where=' where `'.$WhereField.'`'.$WhereOp.'\''.$WhereValue.'\'';
		}
		else if(is_array($WhereField)&&is_array($WhereValue)){
			$Where=' where';
			foreach($WhereField as $Key => $Val){
				if(!is_array($WhereOp)||empty($WhereOp[$Key])){
					$TempOp=array('=','AND');
				}
				else if(!is_array($WhereOp[$Key])){
					if(strpos($WhereOp[$Key],',')===FALSE){
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=array($WhereOp[$Key],'AND');
					}
					else{
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=explode(',',$WhereOp[$Key]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$Where.=' `'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\'';
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
				else{
					if(empty($WhereOp[$Key][0])){
						$TempOp=array('=','AND');
					}
					else if(strpos($WhereOp[$Key][0],',')===FALSE){
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=array($WhereOp[$Key][0],'AND');
					}
					else{
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=explode(',',$WhereOp[$Key][0]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$TempBeforeTag='';
					$TempAfterTag='';
					if(empty($WhereOp[$Key][1])){
					}
					else if(strpos($WhereOp[$Key][1],',')===FALSE){
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempBeforeTag=$WhereOp[$Key][1]; 
					}
					else{
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempTag=explode(',',$WhereOp[$Key][1]);
						$TempBeforeTag=$TempTag[0];
						$TempAfterTag=$TempTag[1];
					}

					$Where.=' '.$TempBeforeTag.'`'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\''.$TempAfterTag;
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
			}
		}
		if(!empty($Like)&&!is_array($WhereField)&&!is_array($WhereValue)&&!empty($WhereField)){
			switch ($Like){
				case 1:
					$Where=' where `'.$WhereField.'` like \''.$WhereValue.'\'';
					break;
				case 2:
					$Where=' where `'.$WhereField.'` not like \''.$WhereValue.'\'';
					break;
				default:
					break;
			}
		}
		if(!empty($Index)){
			$Index=' index('.$Index.')';
		}
		else{
			$Index='';
		}
		
		if($GLOBALS['RW_Splitting']&&$this->NowDb=='default'){
			$this->Mysqli->close();
			$this->NowDb=$this->RandomSql();
			$this->Connect();
		}
		
		$Result=$this->Mysqli->query('select count(*) as Total from `'.$Table.'`'.$Where.$Index,MYSQLI_USE_RESULT);
		if(!$Result){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Total():统计数据总数失败，请检查：数据表、字段、索引是否存在。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Total():The total number of statistics failed. Please check if data tables, fields, and indexes exist.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nSQL String:".'select count(*) as Total from `'.$Table.'`'.$Where.$Index."\r\n-------------------\r\nerrno:".$this->Mysqli->errno."\r\nDetail:".$this->Mysqli->error;
			Wrong::Report($ModuleError);
		}
		
		$Return=$Result->fetch_assoc();
		$Result->free();
		return $Return['Total'];
	}
	
	//插入数据
	public function Insert($Table,$Array){
		$Field=NULL;
		$Value=NULL;
		
		foreach ($Array as $Key => $Val) {
			$Field.='`'.$Key.'`,';
			$Value.='\''.$Val.'\',';
		}
		$Field=substr($Field, 0, -1);
		$Value=substr($Value, 0, -1);
		
		if($GLOBALS['RW_Splitting']&&$this->NowDb!='default'){
			$this->Mysqli->close();
			$this->NowDb='default';
			$this->Connect();
		}
		
		$Result=$this->Mysqli->query('insert into `'.$Table.'` ( '.$Field.' ) values ( '.$Value.' )',MYSQLI_USE_RESULT);
		if(!$Result){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Insert():插入数据失败，请检查：数据表或字段是否存在、字段的值是否为空（当数据库中字段的设置为非空时）、是否在值唯一的字段中插入了相同的数据（例如设置了索引的字段）、字段的值的类型和长度是否正确。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Insert():Failed to insert data, please check whether the data table or the field exists, whether the field is empty (when the field is set in the database is not empty), whether the same data is inserted in the value unique field (for example, the field that sets the index), the type and length of the value of the field is correct.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nSQL String:".'insert into `'.$Table.'` ( '.$Field.' ) values ( '.$Value.' )'."\r\n-------------------\r\nerrno:".$this->Mysqli->errno."\r\nDetail:".$this->Mysqli->error;
			Wrong::Report($ModuleError);
		}
		$Result=$this->Mysqli->insert_id;
		return $Result;
	}
	
	//删除数据
	public function Delete($Table,$WhereField,$WhereValue,$WhereOp='=',$Limit=NULL,$Index=NULL){

		if(empty($WhereOp)){
			$WhereOp='=';
		}
		
		$Where='';
		if((!is_array($WhereField)&&!is_array($WhereValue))&&!empty($WhereField)){
			$Where=' where `'.$WhereField.'`'.$WhereOp.'\''.$WhereValue.'\'';
		}
		else if(is_array($WhereField)&&is_array($WhereValue)){
			$Where=' where';
			foreach($WhereField as $Key => $Val){
				if(!is_array($WhereOp)||empty($WhereOp[$Key])){
					$TempOp=array('=','AND');
				}
				else if(!is_array($WhereOp[$Key])){
					if(strpos($WhereOp[$Key],',')===FALSE){
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=array($WhereOp[$Key],'AND');
					}
					else{
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=explode(',',$WhereOp[$Key]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$Where.=' `'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\'';
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
				else{
					if(empty($WhereOp[$Key][0])){
						$TempOp=array('=','AND');
					}
					else if(strpos($WhereOp[$Key][0],',')===FALSE){
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=array($WhereOp[$Key][0],'AND');
					}
					else{
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=explode(',',$WhereOp[$Key][0]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$TempBeforeTag='';
					$TempAfterTag='';
					if(empty($WhereOp[$Key][1])){
					}
					else if(strpos($WhereOp[$Key][1],',')===FALSE){
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempBeforeTag=$WhereOp[$Key][1]; 
					}
					else{
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempTag=explode(',',$WhereOp[$Key][1]);
						$TempBeforeTag=$TempTag[0];
						$TempAfterTag=$TempTag[1];
					}

					$Where.=' '.$TempBeforeTag.'`'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\''.$TempAfterTag;
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
			}
		}
		if(is_array($Limit)){
			if(!empty($Limit[1])){
				$Limit=' limit '.$Limit[0].','.$Limit[1];
			}
			else if(isset($Limit[0])){
				$Limit=' limit 0,'.$Limit[0];
			}
		}
		else{
			$Limit='';
		}
		if(!empty($Index)){
			$Index=' index('.$Index.')';
		}
		else{
			$Index='';
		}
		
		if($GLOBALS['RW_Splitting']&&$this->NowDb!='default'){
			$this->Mysqli->close();
			$this->NowDb='default';
			$this->Connect();
		}
		
		$Result=$this->Mysqli->query('delete from `'.$Table.'`'.$Where.$Limit.$Index,MYSQLI_USE_RESULT);
		if(!$Result){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Delete():删除数据失败，请检查：数据表、字段、索引是否存在。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Delete():Failed to delete data. Please check if data tables, fields, and indexes exist.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nSQL String:".'delete from `'.$Table.'`'.$Where.$Limit.$Index."\r\n-------------------\r\nerrno:".$this->Mysqli->errno."\r\nDetail:".$this->Mysqli->error;
			Wrong::Report($ModuleError);
		}
		return TRUE;
	}
	
	//更新数据
	public function Update($Table,$WhereField=NULL,$WhereValue=NULL,$Array,$WhereOp='=',$Limit=NULL,$AutoOP=NULL,$Index=NULL){
		$Value=NULL;
		$AutoOPNumber=0;
		
		if(empty($WhereOp)){
			$WhereOp='=';
		}
		
		$Where='';
		if((!is_array($WhereField)&&!is_array($WhereValue))&&!empty($WhereField)){
			$Where=' where `'.$WhereField.'`'.$WhereOp.'\''.$WhereValue.'\'';
		}
		else if(is_array($WhereField)&&is_array($WhereValue)){
			$Where=' where';
			foreach($WhereField as $Key => $Val){
				if(!is_array($WhereOp)||empty($WhereOp[$Key])){
					$TempOp=array('=','AND');
				}
				else if(!is_array($WhereOp[$Key])){
					if(strpos($WhereOp[$Key],',')===FALSE){
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=array($WhereOp[$Key],'AND');
					}
					else{
						$WhereOp[$Key]=str_replace(' ','',$WhereOp[$Key]);
						$TempOp=explode(',',$WhereOp[$Key]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$Where.=' `'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\'';
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
				else{
					if(empty($WhereOp[$Key][0])){
						$TempOp=array('=','AND');
					}
					else if(strpos($WhereOp[$Key][0],',')===FALSE){
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=array($WhereOp[$Key][0],'AND');
					}
					else{
						$WhereOp[$Key][0]=str_replace(' ','',$WhereOp[$Key][0]);
						$TempOp=explode(',',$WhereOp[$Key][0]);
						if(empty($TempOp[1])){
							$TempOp[1]='AND';
						}
					}
					$TempBeforeTag='';
					$TempAfterTag='';
					if(empty($WhereOp[$Key][1])){
					}
					else if(strpos($WhereOp[$Key][1],',')===FALSE){
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempBeforeTag=$WhereOp[$Key][1]; 
					}
					else{
						$WhereOp[$Key][1]=str_replace(' ','',$WhereOp[$Key][1]);
						$TempTag=explode(',',$WhereOp[$Key][1]);
						$TempBeforeTag=$TempTag[0];
						$TempAfterTag=$TempTag[1];
					}

					$Where.=' '.$TempBeforeTag.'`'.$Val.'` '.$TempOp[0].' \''.$WhereValue[$Key].'\''.$TempAfterTag;
					if($Key<(count($WhereField)-1)){
						$Where.=' '.$TempOp[1];
					}
				}
			}
		}
		if(is_array($Limit)){
			if(!empty($Limit[1])){
				$Limit=' limit '.$Limit[0].','.$Limit[1];
			}
			else if(isset($Limit[0])){
				$Limit=' limit 0,'.$Limit[0];
			}
		}
		else{
			$Limit='';
		}
		foreach ($Array as $Key => $Val){
			
			if(!empty($AutoOP[$AutoOPNumber])){
				$Value.='`'.$Key.'`='.$Key.' '.$AutoOP[$AutoOPNumber];
			}
			else{
				$Value.='`'.$Key.'`=\''.$Val.'\'';
			}
			$Value.=',';
			$AutoOPNumber++;
		}
		if(!empty($Index)){
			$Index=' index('.$Index.')';
		}
		else{
			$Index='';
		}
		$Value=substr($Value, 0, -1);
		
		if($GLOBALS['RW_Splitting']&&$this->NowDb!='default'){
			$this->Mysqli->close();
			$this->NowDb='default';
			$this->Connect();
		}
		
		$Result=$this->Mysqli->query('update `'.$Table.'` set '.$Value.$Where.$Limit.$Index,MYSQLI_USE_RESULT);
		if(!$Result){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Update():更新数据失败，请检查：数据表或字段是否存在、字段的值是否为空（当数据库中字段的设置为非空时）、字段的值的类型和长度是否正确。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Update():Update data failed, please check: whether the data table or field exists, whether the value of the field is empty (when the setting of the field in the database is not empty), the type and length of the value of the field is correct.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nSQL String:".'update `'.$Table.'` set '.$Value.$Where.$Limit.$Index."\r\n-------------------\r\nerrno:".$this->Mysqli->errno."\r\nDetail:".$this->Mysqli->error;
			Wrong::Report($ModuleError);
		}
		return TRUE;
	}
	
	//查询自定义语句
	public function Other($SqlString,$Fetch=FALSE){
		
		if($GLOBALS['RW_Splitting']&&$this->NowDb=='default'){
			$this->Mysqli->close();
			$this->NowDb=$this->RandomSql();
			$this->Connect();
		}
		$Result=$this->Mysqli->query($SqlString,MYSQLI_USE_RESULT);
		if(!$Result){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Sql->Other():执行指定的SQL语句失败，请检查SQL语句是否正确。';
			}
			else{
				$ModuleError='84PHP Error#Sql->Other():Failed to execute the specified SQL statement. Please check if the SQL statement is correct.';
			}
			$ModuleError.="\r\n\r\n-------------------\r\nSQL String:".$SqlString."\r\n-------------------\r\nerrno:".$this->Mysqli->errno."\r\nDetail:".$this->Mysqli->error;
			Wrong::Report($ModuleError);
		}
		
		if($Fetch){
			$Return=$Result->fetch_all(MYSQLI_ASSOC);
			$Result->free();
			if(empty($Return)){
				$Return=array();
			}
		}
		else{
			$Return=$Result;
		}
		return $Return;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Sql:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Choose()'."\r\n".'Select()'."\r\n".'SelectMore()'."\r\n".'Total()'."\r\n".'Insert()'."\r\n".'Delete()'."\r\n".'Update()'."\r\n".'Other()';
		}
		else{
			$ModuleError="84PHP Error#Sql:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Choose()'."\r\n".'Select()'."\r\n".'SelectMore()'."\r\n".'Total()'."\r\n".'Insert()'."\r\n".'Delete()'."\r\n".'Update()'."\r\n".'Other()';;
		}
		Wrong::Report($ModuleError);
	}
	
	//关闭数据库连接
	public function __destruct(){
		if(!$this->Mysqli->connect_errno){
			$this->Mysqli->close();
		}
	}
}