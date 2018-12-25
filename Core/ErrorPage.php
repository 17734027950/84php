<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0,user-scalable=no,minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<title>出错了！Error!</title>
<style type="text/css">
	@media screen and (max-width: 961px){
		body{
			background-size: auto 100% !important;
		}
		.center hr{
			height: 0.2rem;
			width: 80% !important;
		}
		.center .errorinfo{
			width: 80% !important;
		}
	}
	@keyframes opacity{
		0%{
			opacity:0;
		}
		100%{
			opacity:1;
		}
	}
	@-moz-keyframes opacity{
		0%{
			opacity:0;
		}
		100%{
			opacity:1;
		}
	}
	@-webkit-keyframes opacity{
		0%{
			opacity:0;
		}
		100%{
			opacity:1;
		}
	}
	html{
		width:100% !important;
		height:100% !important;
		background-color:#FFF;
		color:#FFF !important;
		font-size:6.25%;
		font-family:"Microsoft YaHei",微软雅黑,"Microsoft JhengHei";
	}
	body{
		margin:0px !important;
		width:100% !important;
		height:100% !important;
		position:absolute !important;
	}
	a{
		text-decoration:none !important;
	}
	a:link{
		color:inherit !important;
	}
	a:visited{
		color:inherit !important;
	}
	a img{ 
		border:none !important;
	}
	img{
		display: block !important;
	}
	div{
		box-sizing: border-box !important;
	}
	.clear{
		clear: both !important;
	}
	.displaynone{
		display:none !important;
	}
	.fixed{
		width: 100%;
		height: 100%;
		top: 0;
		left: 0;
		background: linear-gradient(to bottom, #210f7d 0%, #4e87e6 70%, #00a4ff 100%);
		position: fixed;
		z-index: 9999999;
	}
	.center{
		width: 100%;
		position: absolute;
		left: 0;
		top: 20%;
		text-align: center;
		color:#FFF !important;
	}
	.center .title{
		font-size: 90rem !important;
		line-height: 1 !important;
		padding-bottom: 3rem !important;
	}
	.center hr{
		height: 0.2rem;
		width: 40%;
		background-color: #FFF !important;
		border: none;
	}
	.center .errorinfo{
		width: 40%;
		border: none;
		margin: auto;
		font-size: 18rem;
	}
	.center .errorinfo pre{
		width: 100%;
		border: none;
		margin: 1rem auto;
		line-height: 1.5;
		text-align: left;
		font-family:"Microsoft YaHei",微软雅黑,"Microsoft JhengHei";
	}
	.center .link{
		width: 100%;
		font-size: 16rem;
		margin-top: 5rem;
	}
	.center .link span{
		border-bottom: solid #FFF 1px;
		cursor: pointer;
	}
</style>
</head>
<body>
	<div class="fixed">
		<div class="center">
			<div class="title">┌(*_*)┘</div>
			<hr>
			<div class="errorinfo">
				<pre style="word-wrap: break-word; white-space: pre-wrap; white-space: -moz-pre-wrap">{$ErrorInfo}</pre><br />
				<p>将在 <span id="seconds1"></span> 秒后返回首页<br>Return to home page in <span id="seconds2"></span> seconds.</p>
			</div>
			<div class="link">帮助/Help -> <span onClick="window.location.href='http://www.84php.com'">http://www.84php.com</span></div>
		</div>
	</div>
</body>
<script language="javascript">
	var Time=5;
	
	InnerSeconds(Time);
	
	var SIID=setInterval("AutoJump()",1000);
	
	function AutoJump(){
		if(Time<=1){
			clearInterval(SIID);
			window.location.href="/{$JumpURL}";
		}
		else{
			Time=Time-1;
			InnerSeconds(Time);
		}
	}
	
	function InnerSeconds(Seconds){
		document.getElementById('seconds1').innerHTML=Seconds;
		document.getElementById('seconds2').innerHTML=Seconds;
	}
</script>
</html>