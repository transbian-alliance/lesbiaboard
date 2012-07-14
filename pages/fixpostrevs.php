<?php

$title = 'postfixxor';

$posts = Query("
	SELECT p.id, (SELECT MAX(pt2.revision) FROM {posts_text} pt2 WHERE pt2.pid=pt.pid) maxrevision, pt.revision 
	FROM posts p LEFT JOIN {posts_text} pt ON pt.pid=p.id 
	WHERE pt.revision>0 AND (pt.user=0 OR pt.date=0) 
	ORDER BY p.id ASC, pt.revision DESC");
	
if (!NumRows($posts)) die('No posts to fix');
	
while ($post = Fetch($posts))
{
	echo "POST ID {$post['id']} REV {$post['revision']} MAXREV {$post['maxrevision']}: ";
	
	$logentry = Fetch(Query("SELECT time,text FROM {reports} WHERE text LIKE 'Post edited by %pid={0}' ORDER BY time DESC LIMIT {1},1", $post['id'], $post['maxrevision']-$post['revision']));
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
	
	$userid = FetchResult("SELECT id FROM {users} WHERE name={0}", $match[1]);
	if ($userid == -1)
	{
		echo " * user '{$match[1]}' not found, skipping<br>";
		continue;
	}
	
	echo " * revision {$post['revision']} by {$match[1]} (user ID {$userid}) on {$logentry['time']}, adjusting table entry<br>";
	Query("UPDATE {posts_text} SET user={0}, date={1} WHERE pid={2} AND revision={3}", $userid, $logentry['time'], $post['id'], $post['revision']);
}

?>