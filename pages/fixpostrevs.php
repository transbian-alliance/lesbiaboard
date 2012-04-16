<?php

$title = 'postfixxor';

$posts = Query("SELECT p.id, pt.revision FROM posts p LEFT JOIN posts_text pt ON pt.pid=p.id WHERE pt.revision>0 AND (pt.user=0 OR pt.date=0) ORDER BY p.id, pt.revision");
while ($post = Fetch($posts))
{
	echo "POST ID {$post['id']} REV {$post['revision']}: ";
	
	$logentry = Fetch(Query("SELECT time,text FROM reports WHERE text LIKE 'Post edited by %pid={$post['id']}' ORDER BY time LIMIT ".($post['revision']-1).",1"));
	if (!$logentry)
	{
		echo "no log entry found, skipping<br>";
		continue;
	}
	
	echo "log entry found: {$logentry['time']} {$logentry['text']}<br>";
	
	$match = array();
	if (!preg_match('@Post edited by \[b\](.*?)\[/\]@si', $logentry['text'], $match))
	{
		echo " * invalid log entry, skipping<br>";
		continue;
	}
	
	$userid = FetchResult("SELECT id FROM users WHERE name='".mysql_real_escape_string($match[1])."'");
	if ($userid == -1)
	{
		echo " * user '{$match[1]}' not found, skipping<br>";
		continue;
	}
	
	echo " * revision {$post['revision']} by {$match[1]} (user ID {$userid}) on {$logentry['time']}, adjusting table entry<br>";
	Query("UPDATE posts_text SET user={$userid}, date={$logentry['time']} WHERE pid={$post['id']} AND revision={$post['revision']}");
}

?>