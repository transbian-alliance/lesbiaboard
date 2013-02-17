<?php
$logText = array
(
	// register/login actions
	'register' => 'New user: {user}',
	'login' => '{user} logged in',
	'loginfail' => '{user} attempted to log in as {user2}',
	
	// post related actions
	'newreply' => 'New reply by {user} in {thread}: {post}',
	'editpost' => '{user} edited {user2 s} post in {thread}: {post}',
	'deletepost' => '{user} deleted {user2 s} post in {thread}: {post}',
	
	// thread related actions
	'newthread' => 'New thread by {user}: {thread}',
	'editthread' => '{user} edited thread {thread}',
	'movethread' => '{user} moved thread {thread}}',
	'stickthread' => '{user} stickied thread {thread}',
	'unstickthread' => '{user} unstickied thread {thread}',
	'closethread' => '{user} closed thread {thread}',
	'openthread' => '{user} opened thread {thread}',
	'trashthread' => '{user} trashed thread {thread}',
	'deletethread' => '{user} deleted thread {thread}',
	
	
	// admin actions
	'edituser' => '{user} edited {user2 s} profile',
	'pmsnoop' => '{user} read {user2 s} PM: {pm}',
	
	//Add other log actions in here
);

// CONSIDER: most of the log texts if not all, are going to be like "{user} did action foo"
// take out the {user} part and put it in a separate column on log.php?

// TODO move the fields/callbacks from pages/log.php here and make everything use the same plugin bucket?
$bucket = 'log_texts'; include('lib/pluginloader.php');

function logAction($type, $params)
{
	global $loguserid;
	
	$fields = array();
	$values = array();
	
	foreach ($params as $field=>$val)
	{
		$fields[] = $field;
		$values[] = $val;
	}
	
	Query("INSERT INTO {log} (user,date,type,ip,".implode(',',$fields).")
		VALUES ({0},{1},{2},{3},{4c})",
		$loguserid, time(), $type, $_SERVER['REMOTE_ADDR'], $values);
	
	$bucket = 'logaction'; include('lib/pluginloader.php');
}
