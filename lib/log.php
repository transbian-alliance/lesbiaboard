<?php
$logText = array
(
	// register/login actions
	'register' => 'New user: {user}',
	'login' => '{user} logged in',
	'loginfail' => '{user} attempted to log in as {user2}',
	
	// post related actions
	'newreply' => 'New reply by {user} in {thread} ({forum}): {post}',
	'editpost' => '{user} edited {user2 s} post in {thread} ({forum}): {post}',
	'deletepost' => '{user} deleted {user2 s} post in {thread} ({forum}): {post}',
	'undeletepost' => '{user} undeleted {user2 s} post in {thread} ({forum}): {post}',
	
	// thread related actions
	'newthread' => 'New thread by {user}: {thread}',
	'editthread' => '{user} edited {user2 s} thread {thread} in forum {forum}',
	'movethread' => '{user} moved {user2 s} thread {thread} from {forum} to {forum2}',
	'stickthread' => '{user} sticked {user2 s} thread {thread} in forum {forum}',
	'unstickthread' => '{user} unsticked {user2 s} thread {thread} in forum {forum}',
	'closethread' => '{user} closed {user2 s} thread {thread} in forum {forum}',
	'openthread' => '{user} opened {user2 s} thread {thread} in forum {forum}',
	'trashthread' => '{user} trashed {user2 s} thread {thread} from forum {forum}',
	'deletethread' => '{user} deleted {user2 s} thread {thread} from forum {forum}',
	
	
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
