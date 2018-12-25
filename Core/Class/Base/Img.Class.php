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

require (dirname(__FILE__)."/../Public/Img.php");

class Img{
	//颜色转换
	private function HexRGB($HexColor){
		$Hex = hexdec($HexColor);
		return array("red"=>0xFF&($Hex>>0x10),"green"=>0xFF&($Hex>>0x8),"blue"=>0xFF&$Hex);
	}
	
	//伸缩和水印
	public function Base($From,$To,$Width=NULL,$Height=NULL,$Scale=1.0,$Word=NULL,$WordColor='#F5F5F5'){
		$From=$GLOBALS['RootPath'].$From;
		$To=$GLOBALS['RootPath'].$To;
		if(!file_exists($From)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Img->Base():文件不存在，也有可能是权限不足或文件被占用。';
			}
			else{
				$ModuleError='84PHP Error#Img->Base():File does not exist, or there may be insufficient privileges or files being occupied.';
			}
			Wrong::Report($ModuleError);
		}
		if(!file_exists(dirname($To))){
			mkdir(dirname($To));
		}
		$WordColorArray=array("red"=>80,"green"=>80,"blue"=>80);
		$ImgSize=getimagesize($From);
		$ImgCreate=NULL;
		switch($ImgSize['mime']){
			case 'image/jpeg':
				$ImgCreate=imagecreatefromjpeg($From);
				break;
			case 'image/gif':
				$ImgCreate=imagecreatefromgif($From);
				break;
			case 'image/png':
				$ImgCreate=imagecreatefrompng($From);
				break;
			case 'image/wbmp':
				$ImgCreate=imagecreatefromwbmp($From);
				break;
			default:
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Img->Base():文件的MIME类型不正确。可能其并不是图片文件。';
				}
				else{
					$ModuleError='84PHP Error#Img->Base():The MIME type of the file is not correct. Maybe it\'s not a picture file.';
				}
				Wrong::Report($ModuleError);
				break;
		}
		if(empty($Width)&&empty($Height)){
			$NewWidth=round($ImgSize[0]*$Scale);
			$NewHeight=round($ImgSize[1]*$Scale);
		}
		else{
			if(empty($Width)){
			$NewWidth=round($ImgSize[0] * ($Height/$ImgSize[1]));
			}
			else{
				$NewWidth=$Width;
			}
			if(empty($Height)){
				$NewHeight=round($ImgSize[1] * ($Width/$ImgSize[0]));
			}
			else{
				$NewHeight=$Height;
			}
		}
		$NewImg=imagecreatetruecolor($NewWidth,$NewHeight);
		if(!$NewImg){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Img->Base():创建图片失败，请检查是否开启了GD库。';
			}
			else{
				$ModuleError='84PHP Error#Img->Base():Failed to create a picture. Please check whether the GD library has been opened.';
			}
			Wrong::Report($ModuleError);
		}
		imagecopyresampled($NewImg,$ImgCreate,0,0,0,0,$NewWidth,$NewHeight,$ImgSize[0],$ImgSize[1]);
		if(!empty($Word)){
			$FontSize=$NewHeight*0.12;
			if($WordColor!=NULL){
				$WordColorArray=$this->HexRGB($WordColor);
			}
			$textcolor1=imagecolorallocate($NewImg,$WordColorArray['red'],$WordColorArray['green'],$WordColorArray['blue']);
			$str=preg_replace('/[\x80-\xff]{1,3}/',' ',$Word,-1); 
			$Num=strlen($str);
			if(!imagettftext($NewImg,$FontSize,0,$NewWidth-$Num*$FontSize,$NewHeight-$FontSize/3,$textcolor1,$GLOBALS['RootPath'].$GLOBALS['FontFile'],$Word)){
				if($GLOBALS['FrameworkHelpLanguage']=='CN'){
					$ModuleError='84PHP Error#Img->Base():添加文字失败，请检查字体文件是否损坏。';
				}
				else{
					$ModuleError='84PHP Error#Img->Base():Failed to add text, please check whether the font file is damaged.';
				}
				Wrong::Report($ModuleError);
			}
		}
		switch($ImgSize['mime']){
			case 'image/jpeg':
				$OutPut=imagejpeg($NewImg,$To);
				break;
			case 'image/gif':
				$OutPut=imagegif($NewImg,$To);
				break;
			case 'image/png':
				$OutPut=imagepng($NewImg,$To);
				break;
			case 'image/wbmp':
				$OutPut=imagewbmp($NewImg,$To);
				break;
			default:
				return FALSE;
				break;
		}
		if($OutPut){
			return TRUE;
		}else{
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Img->Base():生成图片失败。';
			}
			else{
				$ModuleError='84PHP Error#Img->Base():Generating picture failure.';
			}
			Wrong::Report($ModuleError);
		}
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Img:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Base()';
		}
		else{
			$ModuleError="84PHP Error#Img:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Base()';
		}
		Wrong::Report($ModuleError);
	}
}