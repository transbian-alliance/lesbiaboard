<?php
//  AcmlmBoard XD - Local moderator assignment tool
//  Access: administrators only

$title = __("Manage localmod assignments");

AssertForbidden("editMods");

if($loguser['powerlevel'] < 3)
	Kill(__("You're not an administrator. There is nothing for you here."));

if(!isset($_GET['action']))
{
	$rFora = Query("select * from {forums} order by catid, forder");
	while($forum = Fetch($rFora))
	{
		$modList = "";
		$rMods = Query("select * from {forummods} where forum={0}", $forum['id']);
		while($mods = Fetch($rMods))
		{
			$rMod = Query("select u.(_userfields) from {users} u where u.id={0}", $mods['user']);
			$mod = getDataPrefix(Fetch($rMod), "u_");
			$modList .= "<li>".UserLink($mod)."<sup>";
			$modList .= actionLinkTag("&#x2718;", "managemods", "", "action=delete&fid={$forum['id']}&mid={$mods['user']}");
			$modList .= "</sup></li>";
		}
		$theList .= format(
"
		<li>
			{0}
			<ul>
				{2}
				".actionLinkTagItem(__("Add"), "managemods", "", "action=add&fid={1}")."
			</ul>
		</li>
", $forum['title'], $forum['id'], $modList);
	}
	write(
"
	<div class=\"faq outline margin\">
		<h3>".__("Moderators as of {0}")."</h3>
		<ul>
			{1}
		</ul>
	</div>
", gmdate("F jS Y"), $theList);
}
elseif($_GET['action'] == "delete")
{
	if(!isset($_GET['fid']))
		Kill(__("Forum ID unspecified."));
	if(!isset($_GET['mid']))
		Kill(__("Mod ID unspecified."));

	$fid = (int)$_GET['fid'];
	$mid = (int)$_GET['mid'];

	$rMod = Query("delete from {forummods} where forum={0} and user={1}", $fid, $mid);
	
	die(header("Location: ".actionLink("managemods")));
}
elseif($_GET['action'] == "add")
{
	if(!isset($_GET['fid']))
		Kill(__("Forum ID unspecified."));

	$fid = (int)$_GET['fid'];

	if(!isset($_GET['mid']))
	{
		$modList = "";
		$rMod = Query("select * from {users} where powerlevel=1 order by name asc");
		while($mod = Fetch($rMod))
		{
			$rCheck = Query("select user from {forummods} where forum={0} and user={1}", $fid, $mod['id']);
			if(NumRows($rCheck))
				$add = __("already there");
			else
				$add = actionLinkTag("Add", "managemods", "", "action=add&fid=$fid&mid={$mod['id']}");

			$modList .= format(
"
<li>
{0}
<sup>[{1}]</sup>
</li>
", UserLink($mod), $add);
		}
		write(
"
		<div class=\"faq outline margin\">
			".__("Pick a mod, any mod.")."
			<ul>
				{0}
			</ul>
		</div>
",	$modList);
	}
	else
	{
		$mid = (int)$_GET['mid'];
		$rMod = Query("insert into {forummods} (forum, user) values ({0}, {1})", $fid, $mid);

		die(header("Location: ".actionLink("managemods")));
	}
}

?>
