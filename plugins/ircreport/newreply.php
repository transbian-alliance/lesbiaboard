<?php

$c1 = $selfsettings["color1"];
$c2 = $selfsettings["color2"];

$thename = $loguser["name"];
if($loguser["displayname"])
	$thename = $loguser["displayname"];
	
if ($forum['minpower'] <= 0)
	ircReport("\003".$c2."New reply by\003$c1 "
		.$thename
		."\003$c2: \003$c1"
		.$thread["title"]
		."\003$c2 (".$forum["title"].")"
		." -- "
		.getServerURL()."?pid=".$pid
		);
	
