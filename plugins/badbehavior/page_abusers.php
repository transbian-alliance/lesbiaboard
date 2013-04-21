<?php

$title = "Service Abusers";

if ($loguser['powerlevel'] < 3)
	Kill('No.');

echo "
	<table class=\"outline margin width100\">
		<tr class=\"header0\">
			<th>
				Date
			</th>
			<th>
				IP
			</th>
			<th>
				Request
			</th>
			<th>
				Key
			</th>
			<th>
				Users
			</th>
		</tr>
";

$abusers = query('SELECT * FROM {bad_behavior} ORDER BY `date` DESC');
while ($abuser = fetch($abusers))
{
	$date = formatdate(strtotime($foo['date']));

	$userlisting = '';
	$users = Query("SELECT * FROM {users} WHERE lastip={0} ORDER BY name", $abuser['ip']);
	while ($user = Fetch($users))
		$userlisting .= UserLink($user).', ';

	if (!$userlisting) $userlisting = 'None';
	else $userlisting = substr($userlisting, 0, strlen($userlisting)-2);

	echo "
		<tr class=\"cell0\">
			<td>
				{$date}
			</td>
			<td>
				".formatIP($abuser['ip'])."
			</td>
			<td>
				<pre style='white-space:pre-wrap'>". htmlspecialchars(preg_replace('/logsession=\w+/', 'logsession=?????', $abuser['http_headers'])) . "</pre>
			</td>
			<td>
				{$abuser['key']}
			</td>
			<td>
				{$userlisting}
			</td>
		</tr>
";
}

echo "
	</table>
";

?>
