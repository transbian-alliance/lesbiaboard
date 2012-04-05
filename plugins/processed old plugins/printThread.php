<?php
registerPlugin("Print Thread");

function Threads_Userbar($tag)
{
	global $rssBar, $rssWidth, $fid, $tid;
	if($tag != "userBar")
		return;
	$snp = explode("/", $_SERVER['SCRIPT_NAME']);
	$s = $snp[count($snp)-1];
	if($s == "thread.php")
	{
		$rssBar .= "<a href=\"printthread.php?id=".$tid."\"><img src=\"img/print.png\" alt=\"Print\" title=\"Printable view\" /></a>";
		$rssWidth += 19;
	}
}

register("writers", "Threads_Userbar", 1);

?>