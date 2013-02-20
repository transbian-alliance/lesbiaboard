<?php
$logText = array
(
	// register/login actions
	'register' => 'New user: {user}',
	'login' => '{user} logged in',
	'logout' => '{user} logged out',
	'loginfail' => '{user} attempted to log in as {user2}',
	'loginfail2' => '{user} attempted to log in as user "{text}"',
	'lostpass' => '{user} requested a password reset for {user2}',
	'lostpass2' => '{user} successfully reset his password.',
	
	// post related actions
	'newreply' => 'New reply by {user} in {thread} ({forum}): {post}',
	'editpost' => '{user} edited {user2 s} post in {thread} ({forum}): {post}',
	'deletepost' => '{user} deleted {user2 s} post in {thread} ({forum}): {post}',
	'undeletepost' => '{user} undeleted {user2 s} post in {thread} ({forum}): {post}',
	
	// thread related actions
	'newthread' => 'New thread by {user}: {thread}',
	'editthread' => '{user} edited {user2 s} thread {thread} ({forum})',
	'movethread' => '{user} moved {user2 s} thread {thread} from {forum} to {forum2}',
	'stickthread' => '{user} sticked {user2 s} thread {thread} ({forum})',
	'unstickthread' => '{user} unsticked {user2 s} thread {thread} ({forum})',
	'closethread' => '{user} closed {user2 s} thread {thread} ({forum})',
	'openthread' => '{user} opened {user2 s} thread {thread} ({forum})',
	'trashthread' => '{user} trashed {user2 s} thread {thread} from forum {forum}',
	'deletethread' => '{user} deleted {user2 s} thread {thread} from forum {forum}',
	
	
	// admin actions
	'edituser' => '{user} edited {user2 s} profile',
	'usercomment' => '{user} commented on {user2 s} profile',
	'pmsnoop' => '{user} read {user2 s} PM: {pm}',
	'editsettings' => '{user} edited the board\'s settings',
	'editplugsettings' => '{user} edited the settings of plugin {text}',
	'enableplugin' => '{user} enabled plugin {text}',
	'disableplugin' => '{user} disabled plugin {text}',
	//Add other log actions in here
);

// CONSIDER: most of the log texts if not all, are going to be like "{user} did action foo"
// take out the {user} part and put it in a separate column on log.php?

// TODO move the fields/callbacks from pages/log.php here and make everything use the same plugin bucket?
$bucket = 'log_texts'; include('lib/pluginloader.php');

function logAction($type, $params)
{
	global $loguserid;
	
	if(!isset($params["user"]))
		$params["user"] = $loguserid;
	
	$fields = array();
	$values = array();
	
	foreach ($params as $field => $val)
	{
		$fields[] = $field;
		$values[] = $val;
	}
	
	Query("INSERT INTO {log} (date,type,ip,".implode(',',$fields).")
		VALUES ({0},{1},{2},{3c})",
		time(), $type, $_SERVER['REMOTE_ADDR'], $values);
	
	$bucket = 'logaction'; include('lib/pluginloader.php');
}
