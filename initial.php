<?php
	require (dirname(__FILE__)."/Core/Common.php");
	require (dirname(__FILE__)."/Core/Class/Base/Cache.Class.php");
	set_time_limit(0);
	$ClassCache=new Cache;
	$Result=$ClassCache->Base();
	header('Location: /');