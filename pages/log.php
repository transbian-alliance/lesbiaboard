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
	'forum2' => array('table' => 'forums', 'key' => 'id', 'fields' => 'id,title'),
	'pm' => array('table' => 'pmsgs', 'key' => 'id', 'fields' => 'id'),
);

$bucket = 'log_fields'; include('lib/pluginloader.php');


$joinfields = '';
$joinstatements = '';
foreach ($log_fields as $field=>$data)
{
	$joinfields .= ", {$field}.({$data['fields']}) \n";
	$joinstatements .= "LEFT JOIN {{$data['table']}} AS {$field} ON l.{$field}!='0' AND {$field}.{$data['key']}=l.{$field} \n";
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
	$event = preg_replace_callback("@\{(\w+)( (\w+))?\}@", 'addLogInput', $event);

	$cellClass = ($cellClass + 1) % 2;
	$log .= "
		<tr>
			<td class=\"cell2\">
				".str_replace(" ", "&nbsp;", TimeUnits(time() - $item['date']))."
			</td>
			<td class=\"cell$cellClass\">
				$event
			</td>
		</tr>";
}

echo "
	<table class=\"outline margin\">
		<tr class=\"header1\">
			<th>
				".__("Time")."
			</th>
			<th>
				".__("Event")."
			</th>
		</tr>
		$log
	</table>";


function addLogInput($m)
{
	global $item;
	
	$func = 'logFormat_'.$m[1];
	$option = $m[3];
	return $func($item, $option);
}


function logFormat_user($data, $option)
{
	$userdata = getDataPrefix($data, 'user_');
	$res = userLink($userdata);
	if($option == "s")
		$res .= "'s";
	return $res;
}

function logFormat_user2($data, $option)
{
	$userdata = getDataPrefix($data, 'user2_');
	$res = userLink($userdata);
	if($option == "s")
		$res .= "'s";
	return $res;
}

function logFormat_thread($data)
{
	$thread = getDataPrefix($data, "thread_");
	return makeThreadLink($thread);
}

function logFormat_post($data)
{
	return actionLinkTag('#'.$data['post_id'], 'post', $data['post_id']);
}

function logFormat_forum($data)
{
	return actionLinkTag($data['forum_title'], 'forum', $data['forum_id'], "", $data['forum_title']);
}

function logFormat_forum2($data)
{
	return actionLinkTag($data['forum2_title'], 'forum', $data['forum2_id'], "", $data['forum2_title']);
}

function logFormat_pm($data)
{
	return actionLinkTag('PM #'.$data['pm_id'], 'showprivate', $data['pm_id'], 'snoop=1');
}

?>
