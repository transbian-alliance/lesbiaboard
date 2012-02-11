<?php

include("lib/common.php");
if (!$loguserid)
	Kill("You must be logged in to use the post radar.");
else if ($loguser['powerlevel'] < 0)
	Kill("You are banned.");

if (isset($_GET['remove']))
{
	$remove = (int)$_GET['remove'];
	$qUser = "SELECT * FROM users WHERE id=".$remove;
	$rUser = Query($qUser);
	if (NumRows($rUser))
	{
		$qRemove = "DELETE FROM postradar WHERE user=".$remove." and userid=".$loguserid;
		Query($qRemove);
		Alert("User removed.");
	}
	else
		Alert("User doesn't exist.");
}
if (isset($_POST['add']))
{
	$qCount = "SELECT count(*) FROM postradar WHERE userid=".$loguserid."";
	$count = FetchResult($qCount, 0, 0);
	if ($count > 4)
		Alert("You can only have up to 5 users in your post radar at a time");
	else
	{
		$adduser = htmlspecialchars($_POST['add']);
		if (strtolower($adduser) == strtolower($loguser['name']))
			Alert("You can't add yourself.");
		else
		{
			$qUser = "SELECT * FROM users WHERE name='".justEscape($adduser)."' OR displayname='".justEscape($adduser)."'";
			$user = Query($qUser);
			if (NumRows($user))
			{
				$user = Fetch($user);
				$qExists = "SELECT * FROM postradar WHERE user=".$user['id']." AND userid=".$loguserid;
				$rExists = Query($qExists);
				if (NumRows($rExists))
					Alert("This user has already been added.");
				else
				{
					$qInsert = "INSERT INTO postradar (userid, user) VALUES (".$loguserid.", ".$user['id'].")";
					Query($qInsert);
					Alert("User added.", "Notice");
				}
			}
			else
				Alert("User not found.");
		}
	}
}

Write("
	<table class=\"outline margin\">
		<tr class=\"header1\">
			<th style=\"width: 20%;\">Name</th>
			<th style=\"width: 5%;\">Posts</th>
			<th style=\"width: 65%;\">Difference</th>
		</tr>
");

$count = FetchResult("SELECT count(*) FROM postradar WHERE userid=".$loguserid."", 0, 0);

if ($count == 0)
{
	Write(
"
		<tr class=\"cell0\">
			<td colspan=\"3\">
				There are no users in your post radar.
			</td>
		</tr>");
}
else
{
	$qEntries = "SELECT id, name, displayname, sex, powerlevel, posts FROM postradar LEFT JOIN users ON postradar.user = users.id WHERE userid=".$loguserid." order by posts DESC";
	$rEntries = Query($qEntries);
	$c = 0;
	while ($user = Fetch($rEntries))
	{
		$delta = "";
		if ($user['posts'] == $loguser['posts'])
		{
			$delta = "Equal postcount.";
		}
		else if ($user['posts'] > $loguser['posts'])
		{
			$delta = format("<strong>{1}</strong> posts behind.", UserLink($user), $user['posts'] - $loguser['posts']);
		}
		else if ($user['posts'] < $loguser['posts'])
		{
			$delta = format("<strong>{1}</strong> posts ahead.", UserLink($user), $loguser['posts'] - $user['posts']);
		}

		Write("
		<tr class=\"cell{0}\">
			<td>
				{1}
				<sup>[<a href=\"postradar.php?remove={2}\" title=\"Remove {5}\">r</a>]</sup>
			</td>
			<td>
				{3}
			</td>
			<td>
				{4}
			</td>
		</tr>
",	$c, UserLink($user), $user['id'], $user['posts'], $delta, $user['name']);

		$c = ($c + 1) % 2;
	}
}
Write(
"
	</table>

	<form action=\"postradar.php\" method=\"post\">
		<table class=\"outline margin width50\">
			<tr class=\"header0\">
				<th colspan=\"2\">Post radar</th>
			</tr>
			<tr class=\"cell1\">
				<td>
					Current users
				</td>
				<td>
					{0} of 5
				</td>
			</tr>
			<tr class=\"header1\">
				<th colspan=\"2\">Add a user</th>
			</tr>
			<tr class=\"cell0\">
				<td>Name</td>
				<td>
					<input name=\"add\" type=\"text\" />
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>&nbsp;</td>
				<td>
					<input type=\"submit\" value=\"Add user\" />
				</td>
			</tr>
		</table>
	</form>
", $count);

?>