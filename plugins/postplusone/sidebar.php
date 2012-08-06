<?php

global $loguser;

if($post["id"] != "???")
{
	$sideBarStuff .= "<div class=\"plusone\">";
	$sideBarStuff .= "+".$post["postplusones"];

	if($post["u_id"] != $loguserid)
	{
		$url = actionLink("plusone", $post["id"], "key=".$loguser["token"]);
		$url = htmlspecialchars($url);
		$sideBarStuff .= " <a href=\"\" onclick=\"$(this.parentElement).load('$url'); return false;\">+1</a>";
	}

	$sideBarStuff .= "</div>";
}
