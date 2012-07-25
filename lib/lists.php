<?php

function listThread($thread, $cellClass)
{
	global $haveStickies, $loguserid, $loguser, $misc;
	
	$forumList = "";

	$starter = getDataPrefix($thread, "su_");
	$last = getDataPrefix($thread, "lu_");


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

function doThreadPreview($tid)
{	
	$rPosts = Query("
		select 
			{posts}.id, {posts}.date, {posts}.num, {posts}.deleted, {posts}.options, {posts}.mood, {posts}.ip, 
			{posts_text}.text, {posts_text}.text, {posts_text}.revision, 
			u.(_userfields)
		from {posts} 
		left join {posts_text} on {posts_text}.pid = {posts}.id and {posts_text}.revision = {posts}.currentrevision 
		left join {users} u on u.id = {posts}.user
		where thread={0} and deleted=0 
		order by date desc limit 0, 20", $tid);
	
	if(NumRows($rPosts))
	{
		$posts = "";
		while($post = Fetch($rPosts))
		{
			$cellClass = ($cellClass+1) % 2;

			$poster = getDataPrefix($post, "u_");

			$nosm = $post['options'] & 2;
			$nobr = $post['options'] & 4;

			$posts .= Format(
	"
			<tr>
				<td class=\"cell2\" style=\"width: 15%; vertical-align: top;\">
					{1}
				</td>
				<td class=\"cell{0}\">
					<button style=\"float: right;\" onclick=\"insertQuote({2});\">".__("Quote")."</button>
					<button style=\"float: right;\" onclick=\"insertChanLink({2});\">".__("Link")."</button>
					{3}
				</td>
			</tr>
	",	$cellClass, UserLink($poster), $post['id'], CleanUpPost($post['text'], $poster['name'], $nosm));
		}
		Write(
	"
		<table class=\"outline margin\">
			<tr class=\"header0\">
				<th colspan=\"2\">".__("Thread review")."</th>
			</tr>
			{0}
		</table>
	",	$posts);
	}
}
