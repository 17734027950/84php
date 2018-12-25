<?php
require_once (dirname(__FILE__)."/../Core/Common.php");
if($GLOBALS['DebugMode']){
	$CacheChange=FALSE;
	require_once (dirname(__FILE__)."/../Core/Class/Base/Cache.Class.php");
	$ClassCache=new Cache;
	$ClassCache->AutoCache(__FILE__);
	if($CacheChange){
		require(__FILE__);
		exit;
	}
}
$Test='Hello World!';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0,user-scalable=no,minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<title>Hello World! -From 84PHP.</title>
<link rel="stylesheet" href="Css/style.css" type="text/css" />
<script type="text/javascript" src="Js/js.js"></script>
</head>
<body>
	<div class="center">
		<div class="title">欢迎/Welcome!</div>
		<hr>
		<div class="varshow">$Test=<?php echo $Test; ?></div>
		<div class="formline">
			<form action="index.act.php" method="post">
				<input name="testinput" placeholder="请输入/Please iuput" />
				<input type="submit" class="submit" value="POST ->">
			</form>
		</div>
		<div class="link">帮助/Help -> <span onClick="Jump('http://www.84php.com')">http://www.84php.com</span></div>
	</div>
</body>
</html>