<?php
$logText = array(
	'newthread' => 'New thread by {user} in {forum}: {thread}',
	//Add other log actions in here
);

function LogAction($type, $params)
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
}
