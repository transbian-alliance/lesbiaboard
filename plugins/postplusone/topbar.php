<?php

global $loguser;

if($post["id"] != "???")
{
	$plusOne = "";
	
	$plusOne .= "<span class=\"plusone\">";
	$plusOne .= formatPlusOnes($post["postplusones"]);

	if($post["u_id"] != $loguserid)
	{
		$url = actionLink("plusone", $post["id"], "key=".$loguser["token"]);
		$url = htmlspecialchars($url);
		$plusOne .= " <a href=\"\" onclick=\"$(this.parentElement).load('$url'); return false;\">+1</a>";
	}

	$plusOne .= "</span>";

	$links .= "<li>".$plusOne."</li>";
}
