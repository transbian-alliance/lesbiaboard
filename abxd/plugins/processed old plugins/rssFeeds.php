<?php
registerPlugin("RSS Feeds");

function Feeds_Userbar($tag)
{
	global $rssBar, $rssWidth, $fid, $tid;
	if($tag != "userBar")
		return;
	$snp = explode("/", $_SERVER['SCRIPT_NAME']);
	$s = $snp[count($snp)-1];
	if($s == "index.php")
	{
		$rssBar .= "<a href=\"rss2.php\"><img src=\"img/feed.png\" alt=\"RSS Feed\" title=\"RSS Feed\" /></a>";
		$rssWidth += 19;
	}
	else if($s == "forum.php")
	{
		$rssBar .= "<a href=\"rss2.php?forum=".$fid."\"><img src=\"img/feed.png\" alt=\"RSS Feed\" title=\"RSS Feed for this forum\" /></a>";
		$rssWidth += 19;
	}
	else if($s == "thread.php")
	{
		$rssBar .= "<a href=\"rss2.php?thread=".$tid."\"><img src=\"img/feed.png\" alt=\"RSS Feed\" title=\"RSS Feed for this thread\" /></a>";
		$rssWidth += 19;
	}
}

function Feeds_PageHeader()
{
	$snp = explode("/", $_SERVER['SCRIPT_NAME']);
	$s = $snp[count($snp)-1];
	if($s == "index.php")
	{
		write("
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed\" href=\"rss2.php\" />
");
	}
	else if($s == "forum.php")
	{
		write("
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed\" href=\"rss2.php\" />
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed (forum)\" href=\"rss2.php?forum={0}\" />
", $_GET['id']);
	}
	else if($s == "thread.php")
	{
		write("
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed\" href=\"rss2.php\" />
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed (thread)\" href=\"rss2.php?thread={0}\" />
", $_GET['id']);
	}
}

/* index
	<link rel=\"alternate\" type=\"application/rss+xml\" title="RSS feed\" href=\"rss2.php\" />	
*/

/* forum
	<link rel=\"alternate\" type=\"application/rss+xml\" title="RSS feed\" href=\"rss2.php\" />
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed (forum)\" href
*/

/* thread
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed (forum)\" href=\"rss2.php?forum={0}\" />", $fid);
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed (thread)\" href=\"rss2.php?thread={0}\" />", $tid);

	<a href=\"printthread.php?id=".$tid."\"><img src=\"img/print.png\" alt=\"Print\" title=\"Printable view\" /></a>
*/

register("writers", "Feeds_Userbar", 1);
register("pageHeader", "Feeds_PageHeader");

?>