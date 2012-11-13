<?php

$title = __("Ranks");
MakeCrumbs(array(__("Ranks")=>actionLink("ranks")), $links);
AssertForbidden("viewRanks");

loadRanksets();
if(count($ranksetData) == 0)
	Kill(__("No ranksets have been defined."));

$users = array();
$rUsers = Query("select u.(_userfields), u.posts as u_posts from {users} u order by id asc");
while($user = Fetch($rUsers))
	$users[$user['u_id']] = getDataPrefix($user, "u_");

$rankset = $loguser['rankset'];
if(!$rankset)
{
	$rankset = array_keys($ranksetData);
	$rankset = $rankset[0];
}
if(isset($_POST['rankset']))
	$rankset = $_POST['rankset'];

$selected[$rankset] = " selected = \"selected\"";
$ranksets = "";
foreach($ranksetNames as $name => $title)
	$ranksets .= "<option value=\"$name\" {$selected[$name]}>$title</option>";

write(
"
<form action=\"".actionLink("ranks")."\" method=\"post\" id=\"myForm\">
	<table class=\"outline margin width25\">
		<tr class=\"header0\">
			<th colspan=\"2\">
				".__("User ranks")."
			</th>
		</tr>
		<tr class=\"cell0\">
			<td>
				".__("Set")."
			</td>
			<td>
				<select name=\"rankset\" size=\"1\" onchange=\"myForm.submit();\">
					{0}
				</select>
				<input type=\"submit\" value=\"".__("Change")."\" />
			</td>
		</tr>
	</table>
</form>
", $ranksets);

/*
//Handle climbing the ranks again
//$users[1]['posts'] = 6000;
$climbingAgain = array();
for($i = 0; $i < count($users); $i++)
{
	if($users[$i]['posts'] > 5100)
	{
		//print $users[$i]['name']." has ".$users[$i]['posts']." posts. ";
		$climbingAgain[] = UserLink($users[$i]);
		$users[$i]['posts'] %= 5000;
		if($users[$i]['posts'] < 10)
			$users[$i]['posts'] = 10;
		//print "Reset to ".$users[$i]['posts']."...";
	}
}
if(count($climbingAgain))
	$climbingAgain = format(
"
	<tr class=\"header0\">
		<th colspan=\"3\" style=\"height: 4px;\"></th>
	</tr>
	<tr class=\"cell0\">
		<td colspan=\"2\">".__("Climbing the Ranks Again")."</td>
		<td>
			{0}
		</td>
	</tr>
", join(", ", $climbingAgain));
else
	$climbingAgain = "";
*/

$ranks = $ranksetData[$rankset];

$ranklist = "";
for($i = 0; $i < count($ranks); $i++)
{
	$rank = $ranks[$i];
	$nextRank = $ranks[$i+1];
	if($nextRank['num'] == 0)
		$nextRank['num'] = $ranks[$i]['num'] + 1;
	$members = array();
	foreach($users as $user)
	{
		if($user['posts'] >= $rank['num'] && $user['posts'] < $nextRank['num'])
			$members[] = UserLink($user);
	}
	$showRank = $loguser['powerlevel'] > 0 || $loguser['posts'] >= $rank['num'] || count($members) > 0;
	if($showRank)
		$rankText = getRankHtml($rankset, $rank);
	else
		$rankText = "???";

	if(count($members) == 0)
		$members = "&nbsp;";
	else
		$members = join(", ", $members);

	$cellClass = ($cellClass+1) % 2;

	$ranklist .= format(
"
	<tr class=\"cell{0}\">
		<td class=\"cell2\">{1}</td>
		<td>{2}</td>
		<td>{3}</td>
	</tr>
", $cellClass, $rankText, $rank['num'], $members);
}
write(
"
<table class=\"width75 margin outline\">
	<tr class=\"header1\">
		<th>
			".__("Rank")."
		</th>
		<th>
			".__("To get", 1)."
		</th>
		<th>
			&nbsp;
		</th>
	</tr>
	{0}
	{1}
</table>
",	$ranklist, $climbingAgain);

?>
