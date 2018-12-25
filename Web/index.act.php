<?php
require_once (dirname(__FILE__)."/../Core/Common.php");
if($GLOBALS['DebugMode']){
	$CacheChange=FALSE;
	require_once (dirname(__FILE__)."/../Core/Class/Base/Cache.Class.php");
	$ClassCache=new Cache;
	$ClassCache->AutoCache(__FILE__,TRUE);
	if($CacheChange){
		require(__FILE__);
		exit;
	}
}
require_once (dirname(__FILE__)."/../Core/Class/Base/Receive.Class.php");
$ClassReceive=new Receive;
$Post=$GLOBALS['ClassReceive']->Post(FALSE,array('testinput'));
if(empty($Post['testinput'])){
	die('<script>alert("Empty.");window.location.href="/index.php"</script>');
}
else{
	die('<script>alert("'.$Post['testinput'].'");window.location.href="/index.php"</script>');
}
?>
