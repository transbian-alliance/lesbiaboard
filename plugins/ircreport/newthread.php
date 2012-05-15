<?php

$c1 = $selfsettings["color1"];
$c2 = $selfsettings["color2"];

if ($forum['minpower'] <= 0)
	ircReport("\003".$c2."New thread by\003$c1 "
		.$postingAsUser["name"]
		."\003$c2: \003$c1"
		.$thread["title"]
		."\003$c2 (".$forum["title"].")"
		." -- "
		.getServerURL()."?tid=".$tid
		);
