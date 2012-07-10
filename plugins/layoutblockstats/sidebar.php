<?php

if ($post['uid'] == $loguserid)
{
	if (!$GLOBALS["myblockcount"])
		$GLOBALS["myblockcount"] = 1+FetchResult("SELECT COUNT(*) FROM blockedlayouts WHERE blockee={$loguserid}");
	
	$sideBarStuff .= "<br>".($GLOBALS["myblockcount"]-1).($GLOBALS["myblockcount"]==2 ? ' user has':' users have')." blocked your layout<br>";
}

?>
