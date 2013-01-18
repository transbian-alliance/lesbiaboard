<?php

if($loguser['powerlevel'] < 3)
	Kill(__("Access denied."));

MakeCrumbs(array(__("Admin") => actionLink("admin"), __("Log") => actionLink("log")), "");

$log_fields = array
(
	'user' => array('table' => 'users', 'key' => 'id', 'fields' => '_userfields'),
	'user2' => array('table' => 'users', 'key' => 'id', 'fields' => '_userfields'),
	'thread' => array('table' => 'threads', 'key' => 'id', 'fields' => 'id,title'),
	'post' => array('table' => 'posts', 'key' => 'id', 'fields' => 'id'),
	'forum' => array('table' => 'forums', 'key' => 'id', 'fields' => 'id,title'),
	'pm' => array('table' => 'pmsgs', 'key' => 'id', 'fields' => 'id'),
);

function logFormat_user($data)
{
	$userdata = getDataPrefix($data, 'user_');
	return userLink($userdata);
}
function logFormat_user2($data)
{
	$userdata = getDataPrefix($data, 'user2_');
	return userLink($userdata);
}

function logFormat_thread($data)
{
	$thread = getDataPrefix($data, "thread_");
	return makeThreadLink($thread);
}

function logFormat_post($data)
{
	return actionLinkTag('post #'.$data['post_id'], 'post', $data['post_id']);
}

function logFormat_forum($data)
{
	return actionLinkTag($data['forum_title'], 'forum', $data['forum_id'], "", $data['forum_title']);
}

function logFormat_pm($data)
{
	return actionLinkTag('PM #'.$data['pm_id'], 'showprivate', $data['pm_id'], 'snoop=1');
}

$bucket = 'log_fields'; include('lib/pluginloader.php');

$joinfields = '';
$joinstatements = '';
foreach ($log_fields as $field=>$data)
{
	$joinfields .= ", {$field}.({$data['fields']}) \n";
	$joinstatements .= "LEFT JOIN {{$data['table']}} {$field} ON l.{$field}!='0' AND {$field}.{$data['key']}=l.{$field} \n";
}

$logR = Query("	SELECT 
					l.*
					{$joinfields}
				FROM 
					{log} l
					{$joinstatements}
				ORDER BY date DESC"); // TODO: limit

while($item = Fetch($logR))
{
	$event = $logText[$item['type']];
	$event = preg_replace_callback("@\{(\w+)\}@", 'addLogInput', $event);

	$cellClass = ($cellClass + 1) % 2;
	$log .= format(
"
		<tr>
			<td class=\"cell2\">
				{1}&nbsp;
			</td>
			<td class=\"cell{0}\">
				{2}
			</td>
		</tr>
", $cellClass, str_replace(" ", "&nbsp;", TimeUnits(time() - $item['date'])), $event);
}

write(
"
	<table class=\"outline margin\">
		<tr class=\"header1\">
			<th>
				".__("Time")."
			</th>
			<th>
				".__("Event")."
			</th>
		</tr>
		{0}
	</table>
", $log);


function addLogInput($m)
{
	global $item;
	
	$func = 'logFormat_'.$m[1];
	return $func($item);
}

?>
