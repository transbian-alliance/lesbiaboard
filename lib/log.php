<?php
$logText = array
(
	// register/login actions
	'register' => 'New user: {user2}{text}', // 'text' would contain stuff like IP/password matches
	'login' => '{user} logged in',
	'loginfail' => 'A guest attempted to log in as {user2}',
	
	// profile related actions
	'editprofile' => '{user} edited his profile',
	// add mood avatar editing and other stuff?
	
	// post related actions
	'newreply' => 'New reply by {user} in {thread} ({forum}): {post}',
	'editpost' => 'Post edited by {user} in {thread} ({forum}): {post}',
	'deletepost' => 'Post deleted by {user} in {thread} ({forum}): {post}',
	
	// thread related actions
	'newthread' => 'New thread by {user} in {forum}: {thread}',
	'editthread' => 'Thread {thread} ({forum}) edited by {user}',
	'movethread' => 'Thread {thread} ({forum}) moved by {user}',
	'stickthread' => 'Thread {thread} ({forum}) sticked by {user}',
	'unstickthread' => 'Thread {thread} ({forum}) unsticked by {user}',
	'closethread' => 'Thread {thread} ({forum}) closed by {user}',
	'openthread' => 'Thread {thread} ({forum}) opened by {user}',
	'trashthread' => 'Thread {thread} ({forum}) trashed by {user}',
	'deletethread' => 'Thread {thread} ({forum}) deleted by {user}',
	
	// admin actions
	'edituser' => '{user} edited {user2}\'s profile',
	'pmsnoop' => '{user} read {user2}\'s PM: {pm}',
	
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
