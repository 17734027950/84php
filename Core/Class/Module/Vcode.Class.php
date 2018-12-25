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

  V1.0.1
*/

require (dirname(__FILE__).'/../../Common.php');
require (dirname(__FILE__)."/../Public/Vcode.php");

class Vcode{
	//颜色转换
	private function HexRGB($HexColor){
		$Hex = hexdec($HexColor);
		return array("red"=>0xFF&($Hex>>0x10),"green"=>0xFF&($Hex>>0x8),"blue"=>0xFF&$Hex);
	}
	//验证码
	public function Base($Width=120,$Height=50,$Text=NULL,$Dot=27,$Line=15,$TextHexColor="#333333"){
		$Font=$GLOBALS['RootPath'].$GLOBALS['FontFile'];
		if(!file_exists($Font)){
			if($GLOBALS['FrameworkHelpLanguage']=='CN'){
				$ModuleError='84PHP Error#Vcode->Base():字体文件不存在，也有可能是权限不足或文件被占用。';
			}
			else{
				$ModuleError='84PHP Error#Vcode->Base():Font file does not exist, or there may be insufficient privileges or files being occupied.';
			}
			Wrong::Report($ModuleError);
		}
		$possible_letters='0123456789bcdfghjkmnpqrstvwxyz';
		$NoiseHexColor=$TextHexColor;
		$Vcode=NULL;
		if(!empty($Text)){
			$Vcode=$Text;
		}
		else{
			$i=0;
			while ($i<5) {
				$Vcode.=substr($possible_letters,mt_rand(0,strlen($possible_letters)-1),1);
				$i++;
			}
		}
		if(!isset($_SESSION)){
			session_start();
		}
		$_SESSION['Vcode']=$Vcode;
		$FontSize=$Height*0.5;
		$NewImg=imagecreate($Width, $Height);
		$BgColor=imagecolorallocate($NewImg,250,250,250);
		$TextRGBColor=$this->HexRGB($TextHexColor);
		$NoiseRGBColor=$this->HexRGB($NoiseHexColor);
		$TextColor=imagecolorallocate($NewImg,$TextRGBColor['red'],$TextRGBColor['green'],$TextRGBColor['blue']);
		$NoiseColor=imagecolorallocate($NewImg, $NoiseRGBColor['red'],$NoiseRGBColor['green'],$NoiseRGBColor['blue']);
		for($i=0;$i<$Dot;$i++){
			imagefilledellipse($NewImg,mt_rand(0,$Width),
			mt_rand(0,$Height),2,3,$NoiseColor);
		}
		for($i=0;$i<$Line;$i++){
			imageline($NewImg,mt_rand(0,$Width),mt_rand(0,$Height),mt_rand(0,$Width),mt_rand(0,$Height),$NoiseColor);
		}
		$AllText=imagettfbbox($FontSize,0,$Font,$Vcode);
		$X=($Width-$AllText[4])/2;
		$Y=($Height-$AllText[5])/2;
		imagettftext($NewImg,$FontSize,0,$X,$Y,$TextColor,$Font,$Vcode);
		@ob_clean();
		header('Content-Type: image/jpeg');
		header('Cache-Control: no-cache,must-revalidate');   
		header('Pragma: no-cache');   
		header("Expires: -1"); 
		header('Last-Modified: '.gmdate('D, d M Y 01:01:01',time()).' GMT');
		imagejpeg($NewImg);
		imagedestroy($NewImg);
		return TRUE;
	}
	
	//调用方法不存在
	public function __call($Method,$Parameters){
		if($GLOBALS['FrameworkHelpLanguage']=='CN'){
			$ModuleError="84PHP Error#Vcode:调用的方法“".$Method."”不存在。本模块的方法列表：\r\n\r\n".'Base()';
		}
		else{
			$ModuleError="84PHP Error#Vcode:The method \"".$Method."\" that does not exist is called.Method list:\r\n\r\n".'Base()';
		}
		Wrong::Report($ModuleError);
	}	
}
