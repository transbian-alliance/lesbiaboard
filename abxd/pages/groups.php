<?php
$ajax = isset($_GET['action']);
$noAutoHeader = $ajax;
$noViewCount = $ajax;
$noOnlineUsers = $ajax;
$noFooter = $ajax;
include("lib/common.php");

if($_POST['action'] == __("Create") && $loguser['powerlevel'] > 2)
{
	$gid = FetchResult("SELECT id+1 FROM groups WHERE (SELECT COUNT(*) FROM groups g2 WHERE g2.id=groups.id+1)=0 ORDER BY id ASC LIMIT 1");
	if($gid < 1) $gid = 1;
	Query("insert into groups (id, name) values (".$gid.",'".justEscape($_POST['name'])."')");
	$groups[$gid] = array('id' => $gid, 'name' => $_POST['name'], 'leader' => 0, 'members' => array() );
}

$groups = array();
$rGroups = Query("select * from groups order by name asc");
if(NumRows($rGroups))
{
	while($group = Fetch($rGroups))
		$groups[$group['id']] = array('id' => $group['id'], 'name' => $group['name'], 'leader' => $group['leader'], 'members' => array() );

	$rAffils = Query("select name, displayname, gid, uid, powerlevel, sex, status from groupaffiliations left join users on users.id = uid order by gid asc, name asc");
	while($affil = Fetch($rAffils))
	{
		$gid = $affil['gid'];
		$groups[$gid]['members'][$affil['uid']] = array('id' => $affil['uid'], 'name' => $affil['name'], 'displayname' => $affil['displayname'], 'powerlevel' => $affil['powerlevel'], 'sex' => $affil['sex'], 'status' => $affil['status']);
	}
}

if(isset($_GET['action']))
{
	if($_GET['action'] == "removeGroup")
	{
		if($loguser['powerlevel'] < 3)
			die(__("You're not an administrator. There is nothing for you here."));
		$gid = (int)$_GET['gid'];
		if($gid == 0 || !array_key_exists($gid, $groups))
			die(__("Invalid group."));
		else
		{
			Query("delete from groups where id=".$gid);
			Query("delete from groupaffiliations where gid=".$gid);
			$groups[$gid] = 0;
		}
	}
	else if($_GET['action'] == "getGroupContent")
	{
		$gid = (int)$_GET['gid'];
		if($gid == 0 || !array_key_exists($gid, $groups))
			die();
		die(getGroupContent($groups[$gid]));
	}
	else if($_GET['action'] == "requestJoin")
	{
		if(!$loguserid)
			die(__("You're not logged in."));
		$gid = (int)$_GET['gid'];
		if($gid == 0 || !array_key_exists($gid, $groups))
			Alert(__("Invalid group."));
		else
		{
			$group = $groups[$gid];
			if(!array_key_exists($loguserid, $group['members']))
			{
				Query("insert into groupaffiliations (gid,uid,status) values (".$gid.",".$loguserid.",1)");
				$groups[$gid]['members'][$loguserid] = $loguser;
				$groups[$gid]['members'][$loguserid]['status'] = 1;
			}
		}
	}
	else if($_GET['action'] == "acceptJoin")
	{
		$gid = (int)$_GET['gid'];
		if($gid == 0 || !array_key_exists($gid, $groups))
			die(__("Invalid group."));
		else
		{
			$uid = (int)$_GET['uid'];
			if($uid == 0)
				die(__("Invalid user."));
			else
			{
				$group = $groups[$gid];
				if($group['leader'] == $loguserid || $loguser['powerlevel'] >=3)
				{
					Query("update groupaffiliations set status=0 where uid=".$uid." and gid=".$gid);
					$groups[$gid]['members'][$uid]['status'] = 0;
				}
				else
					die(__("You're not the group leader or an administrator."));
			}
		}
	}
	else if($_GET['action'] == "denyOrKick")
	{
		$gid = (int)$_GET['gid'];
		if($gid == 0 || !array_key_exists($gid, $groups))
			die(__("Invalid group."));
		else
		{
			$uid = (int)$_GET['uid'];
			$group = $groups[$gid];
			if($group['leader'] == $loguserid || $loguser['powerlevel'] >=3)
			{
				Query("delete from groupaffiliations where uid=".$uid." and gid=".$gid);
				$groups[$gid]['members'][$uid]['status'] = 3;
			}
		}
	}
	else if($_GET['action'] == "leaveGroup")
	{
		$gid = (int)$_GET['gid'];
		if($gid == 0 || !array_key_exists($gid, $groups))
			die(__("Invalid group."));
		else
		{
			$uid = $loguserid;
			{
				$group = $groups[$gid];
				Query("delete from groupaffiliations where uid=".$uid." and gid=".$gid);
				$groups[$gid]['members'][$uid]['status'] = 3;
			}
		}
	}
	else if($_GET['action'] == "passLeadership")
	{
		$gid = (int)$_GET['gid'];
		if($gid == 0 || !array_key_exists($gid, $groups))
			die(__("Invalid group."));
		else
		{
			$uid = (int)$_GET['uid'];
			if($uid == 0)
				die(__("Invalid user."));
			else
			{
				$group = $groups[$gid];
				if($group['leader'] == $loguserid || $loguser['powerlevel'] > 2)
				{
					Query("update groups set leader=".$uid." where id=".$gid);
					$groups[$gid]['leader'] = $uid;
				}
				else
					die(__("You're not the group leader or an administrator."));
			}
		}
	}
}
else
{
	$title = __("User groups");
	include("lib/header.php");
?>
<script type="text/javascript">
function removeGroup(id, name)
{
	if(!confirm("<?php print __("Are you sure you want to remove the user group \\\"\"+name+\"\\\"?", 2); ?>")
	|| !confirm("<?php print __("Seriously?"); ?>"))
		return false;

	$.get("groups.php", "action=removeGroup&gid="+id, function(data)
	{
		if(data != "")
			alert(data);
		else
			$("#groupRow"+id).hide();
	});
}

function manipulateGroup(action, gid, uid)
{
	$.get("groups.php", "action="+action+"&gid="+gid+"&uid="+uid, function(data)
	{
		if(data != "")
			alert(data);
		else
			$.get("groups.php", "action=getGroupContent&gid="+gid, function(data)
			{
				$("#groupMembers"+gid).html(data);
			});
	});
}
</script>
<?php
	AssertForbidden("editGroups");

	if(count($groups) == 0)
		$groupList = format(
	"
			<tr>
				<td class=\"cell2\" colspan=\"2\">
					".__("There are no groups.")."
				</td>
			</tr>
	");
	else
	{
		$groupList = "";
		foreach($groups as $group)
		{
			$groupContent = getGroupContent($group);

			if($loguser['powerlevel'] > 2)
				$delete = "<sup><a href=\"#\" title=\"".__("Delete group")."\" onclick=\"removeGroup(".$group['id'].",'".$group['name']."'); return false;\">&#x2718;</a></sup>";

			$groupList .= format(
"
		<tr id=\"groupRow{0}\">
			<td class=\"cell2\">
				{1}
				{2}
			</td>
			<td class=\"cell1\" id=\"groupMembers{0}\">
				{3}
			</td>
		</tr>
",	$group['id'], $group['name'], $delete, $groupContent);
		}
	}

	write("
	<table class=\"outline margin width100\">
		<tr class=\"header1\">
			<th>
				".__("Name")."
			</th>
			<th>
				".__("Members")."
			</th>
		</tr>
		{0}
	</table>
",	$groupList);

	if($loguser['powerlevel'] > 2)
	{
		write(
"
	<form action=\"groups.php\" method=\"post\" id=\"myForm\">
		<table class=\"outline margin width25\">
			<tr class=\"header1\">
				<th colspan=\"2\">
					".__("Create group")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"nm\">".__("Name")."</label>
				</td>
				<td class=\"cell0\">
					<input type=\"text\" id=\"nm\" name=\"name\" style=\"width: 98%;\" maxlength=\"64\" />
				</td>
			</tr>
			<tr class=\"cell2\">
				<td></td>
				<td>
					<input type=\"submit\" name=\"action\" value=\"".__("Create")."\" />
				</td>
			</tr>
		</table>
	</form>
");
	}
	die();
}


function getGroupContent($group)
{
	global $loguserid, $loguser;
	if($group == 0)
		continue;
	$members = array();
	$waiters = array();
	$leader = ($group['leader'] == $loguserid || $loguser['powerlevel'] >=3);
	foreach($group['members'] as $member)
	{
		$links = "";
		if($member['status'] == 0)
		{
			if ($member['id'] == $loguserid)
				$links .= "<a href=\"#\" onclick=\"manipulateGroup('leaveGroup',".$group['id'].",".$member['id']."); return false;\" title=\"".__("Leave")."\">&#x229D;</a>";
			if($leader && $member['id'] != $group['leader'])
			{
				$links .= "<a href=\"#\" onclick=\"manipulateGroup('passLeadership',".$group['id'].",".$member['id']."); return false;\" title=\"".__("Pass leadership")."\">&#x2359;</a>";
				$links .= "<a href=\"#\" onclick=\"manipulateGroup('denyOrKick',".$group['id'].",".$member['id']."); return false;\" title=\"".__("Kick out")."\">&#x2298;</a>";
				$links .= "<a href=\"#\" onclick=\"if(confirm('Are you sure you want to just up and ban ".$member['name']."? This is quite permanent.') && confirm('".__("Seriously?")."')) manipulateGroup('ban',".$group['id'].",".$member['id']."); return false;\" title=\"".__("Ban")."\">&#x2297;</a>";
			}
			$n = ($member['displayname'] != "") ? $member['displayname'] : $member['name'];
			$members[$n] = UserLink($member).($links != "" ? "<sup>[".$links."]</sup>" : "");
			ksort($members);
		}
		else if($member['status'] == 1)
		{
			if($leader)
			{
				$links = "";
				if(IsAllowed("joinGroups"))
					$links .= "<a href=\"#\" onclick=\"manipulateGroup('acceptJoin',".$group['id'].",".$member['id']."); return false;\" title=\"".__("Join group")."\">&#x2714;</a>";
				$links .= "<a href=\"#\" onclick=\"manipulateGroup('denyOrKick',".$group['id'].",".$member['id']."); return false;\" title=\"".__("Deny membership")."\">&#x2718;</a>";
			}
			$n = ($member['displayname'] != "") ? $member['displayname'] : $member['name'];
			$waiters[$n] = UserLink($member).($links != "" ? "<sup>[".$links."]</sup>" : "");
			ksort($waiters);
			continue;
		}
		else if($member['status'] == 3)
			continue;
	}

	if(count($members))
		$members = implode("&nbsp;", $members);
	else
		$members = "<small>".__("This group has no members.")."</small>";
	if(count($waiters))
		$waiters = "<br /><small>".__("Waiting:")." ".implode("&nbsp;", $waiters)."</small>";
	else
		$waiters = "";

	$join = "";
	if(!array_key_exists($loguserid, $group['members']) && $loguserid && IsAllowed("joinGroups"))
		$join = "<br /><small><a href=\"#\" onclick=\"manipulateGroup('requestJoin',".$group['id'].",0); return false;\">".__("Request join")."</a></small>";
	
	return $members.$waiters.$join;
}

?>