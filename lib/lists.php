<?php

function listThread($thread, $cellClass)
{
	global $haveStickies, $loguserid, $loguser, $misc;
	
	$forumList = "";
	
	$user = array('id'=>$thread['suid'], 'name'=>$thread['suname'], 'displayname'=>$thread['sudisplayname'], 'powerlevel'=>$thread['supowerlevel'], 'sex'=>$thread['susex']);
	$bucket = "userMangler"; include("./lib/pluginloader.php");
	$starter = $user;
	
	$user = array('id'=>$thread['luid'], 'name'=>$thread['luname'], 'displayname'=>$thread['ludisplayname'], 'powerlevel'=>$thread['lupowerlevel'], 'sex'=>$thread['lusex']);
	$bucket = "userMangler"; include("./lib/pluginloader.php");
	$last = $user;

	if (Settings::get("tagsDirection") === 'Left')
		$tagsl = ParseThreadTags($thread['title']);
	else
		$tagsr = ParseThreadTags($thread['title']);

	$NewIcon = "";
	$newstuff = 0;
	if($thread['closed'])
		$NewIcon = "off";
	if($thread['replies'] >= $misc['hotcount'])
		$NewIcon .= "hot";
	if((!$loguserid && $thread['lastpostdate'] > time() - 900) ||
		($loguserid && $thread['lastpostdate'] > $thread['readdate']) &&
		!$isIgnored)
	{
		$NewIcon .= "new";
		$newstuff++;
	}
	else if(!$thread['closed'] && !$thread['sticky'] && Settings::get("oldThreadThreshold") > 0 && $thread['lastpostdate'] < time() - (2592000 * Settings::get("oldThreadThreshold")))
		$NewIcon = "old";
	
	if($NewIcon)
		$NewIcon = "<img src=\"img/status/".$NewIcon.".png\" alt=\"\"/>";

	if($thread['icon'])
		$ThreadIcon = "<img src=\"".htmlspecialchars($thread['icon'])."\" alt=\"\" class=\"smiley\"/>";
	else
		$ThreadIcon = "";


	//if($thread['sticky'])
	//	$cellClass = 2;

	if($thread['sticky'] == 0 && $haveStickies == 1)
	{
		$haveStickies = 2;
		$forumList .= "<tr class=\"header1\"><th colspan=\"7\" style=\"height: 8px;\"></th></tr>";
	}
	if($thread['sticky'] && $haveStickies == 0) $haveStickies = 1;

	$poll = ($thread['poll'] ? "<img src=\"img/poll.png\" alt=\"Poll\"/> " : "");


	$n = 4;
	$total = $thread['replies'];

	$ppp = $loguser['postsperpage'];
	if(!$ppp) $ppp = 20;

	$numpages = floor($total / $ppp);
	$pl = "";
	if($numpages <= $n * 2)
	{
		for($i = 1; $i <= $numpages; $i++)
			$pl .= " ".actionLinkTag($i+1, "thread", $thread['id'], "from=".($i * $ppp));
	}
	else
	{
		for($i = 1; $i < $n; $i++)
		$pl .= " ".actionLinkTag($i+1, "thread", $thread['id'], "from=".($i * $ppp));
		$pl .= " &hellip; ";
		for($i = $numpages - $n + 1; $i <= $numpages; $i++)
			$pl .= " ".actionLinkTag($i+1, "thread", $thread['id'], "from=".($i * $ppp));
	}
	if($pl)
		$pl = " <span class=\"smallFonts\">[".
			actionLinkTag(1, "thread", $thread['id']). $pl . "]</span>";

	$lastLink = "";
	if($thread['lastpostid'])
		$lastLink = " ".actionLinkTag("&raquo;", "thread", 0, "pid=".$thread['lastpostid']."#".$thread['lastpostid']);

	$threadlink = makeThreadLink($thread);
	
	$forumList .= "
	<tr class=\"cell$cellClass\">
		<td class=\"cell2 threadIcon\"> $NewIcon</td>
		<td class=\"threadIcon\" style=\"border-right: 0px none;\">
			 $ThreadIcon
		</td>
		<td style=\"border-left: 0px none;\">
			$poll
			$threadlink
			$pl
		</td>
		<td class=\"center\">
			".UserLink($starter)."
		</td>
		<td class=\"center\">
			{$thread['replies']}
		</td>
		<td class=\"center\">
			{$thread['views']}
		</td>
		<td class=\"smallFonts center\">
			".formatdate($thread['lastpostdate'])."<br />
			".__("by")." ".UserLink($last)." {$lastLink}</td>
	</tr>";
	
	return $forumList;
}
