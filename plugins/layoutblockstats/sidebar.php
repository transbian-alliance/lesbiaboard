<?php

if ($post['uid'] == $loguserid)
{
	static $myblockcount = -1;
	if ($myblockcount == -1) $myblockcount = FetchResult("SELECT COUNT(*) FROM blockedlayouts WHERE blockee={$loguserid}");
	
	$sideBarStuff .= "<br>".$myblockcount.($myblockcount==1 ? ' user has':' users have')." blocked your layout<br>";
}

?>
