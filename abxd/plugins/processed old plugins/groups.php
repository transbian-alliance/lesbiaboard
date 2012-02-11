<?php

registerPlugin("Groups");

//old deprecated method, but it doesn't count as a query.
function TableExists($table)
{
	global $dbname;
	$tables = mysql_list_tables($dbname);
	while (list($temp) = mysql_fetch_array($tables))
		if ($temp == $table)
			return TRUE;
	return FALSE;
}

function Groups_Write()
{
	global $user;

	if(!TableExists("groupaffilitions"))
		Query("CREATE TABLE IF NOT EXISTS `groupaffiliations` (`id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL DEFAULT '0', `gid` int(11) NOT NULL DEFAULT '0', `status` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) )");
	if(!TableExists("groups"))
		Query("CREATE TABLE IF NOT EXISTS `groups` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL DEFAULT '', `leader` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) )");


	$groups = array();
	$qGroups = "select name from groups left join groupaffiliations on groups.id = groupaffiliations.gid where uid = ".$user['id']." and status = 0";
	$rGroups = Query($qGroups);
	while($group = Fetch($rGroups))
		$groups[] = $group['name'];
	$groups = implode(", ", $groups);
	
	if($groups)
		write(
"
				<tr>
					<td class=\"cell0\">Groups</td>
					<td class=\"cell1\">{0}</td>
				</tr>
", $groups);

}

function Groups_Header($tag)
{
	if($tag == "top" && IsAllowed("editGroups"))
		Write(
"
	<li>
		<a href=\"groups.php\">Groups</a>
	</li>
");
}

register("headers", "Groups_Header", 1);
register("profileTable", "Groups_Write");

?>