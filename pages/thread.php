<?php
//  AcmlmBoard XD - Thread display page
//  Access: all


if(isset($_GET['id']))
	$tid = (int)$_GET['id'];
else if(isset($_GET['pid']))
{
	$pid = (int)$_GET['pid'];
	$rPost = Query("select * from {posts} where id={0}", $pid);
	if(NumRows($rPost))
		$post = Fetch($rPost);
	else
		Kill(__("Unknown post ID."));
	$tid = $post['thread'];
}
else
	Kill(__("Thread ID unspecified."));

AssertForbidden("viewThread", $tid);

$rThread = Query("select * from {threads} where id={0}", $tid);

if(NumRows($rThread))
	$thread = Fetch($rThread);
else
	Kill(__("Unknown thread ID."));

$fid = $thread['forum'];
AssertForbidden("viewForum", $fid);

$pl = $loguser['powerlevel'];
if($pl < 0) $pl = 0;

$rFora = Query("select * from {forums} where id={0}", $fid);
if(NumRows($rFora))
{
	$forum = Fetch($rFora);
	if($forum['minpower'] > $pl)
		Kill(__("You are not allowed to browse this forum."));
}
else
	Kill(__("Unknown forum ID."));

$rCategories = Query("select * from {categories} where id={0}", $forum['catid']);
if(NumRows($rCategories))
{
	$category = Fetch($rCategories);
}
else
	Kill(__("Unknown category ID."));

$threadtags = ParseThreadTags($thread['title']);
$title = $threadtags[0];

Query("update {threads} set views=views+1 where id={0} limit 1", $tid);

if(isset($_GET['vote']))
{
	AssertForbidden("vote");
	if(!$loguserid)
		Kill(__("You can't vote without logging in."));
	if($thread['closed'])
		Kill(__("Poll's closed!"));
	if($thread['poll'])
	{
		$vote = (int)$_GET['vote'];
		
		if ($loguser["token"] != $_GET['token'])
			Kill(__("Invalid token."));
		
		$doublevote = FetchResult("select doublevote from {poll} where id={0}", $thread['poll']);
		if($doublevote)
		{
			//Multivote.
			$existing = FetchResult("select count(*) from {pollvotes} where poll={0} and choice={1} and user={2}", $thread['poll'], $vote, $loguserid);
			if ($existing)
				Query("delete from {pollvotes} where poll={0} and choice={1} and user={2}", $thread['poll'], $vote, $loguserid);
			else
				Query("insert into {pollvotes} (poll, choice, user) values ({0}, {1}, {2})", $thread['poll'], $vote, $loguserid);
		}
		else
		{
			//Single vote only?
			//Remove any old votes by this user on this poll, then add a new one.
			Query("delete from {pollvotes} where poll={0} and user={1}", $thread['poll'], $loguserid);
			Query("insert into {pollvotes} (poll, choice, user) values ({0}, {1}, {2})", $thread['poll'], $vote, $loguserid);
		}
	}
	else
		Kill(__("This is not a poll."));
}

if(!$thread['sticky'] && Settings::get("oldThreadThreshold") > 0 && $thread['lastpostdate'] < time() - (2592000 * Settings::get("oldThreadThreshold")))
	$replyWarning = " onclick=\"if(!confirm('".__("Are you sure you want to reply to this old thread? This will move it to the top of the list. Please only do this if you have something new and relevant to share about this thread's topic that is not better placed in a new thread.")."')) return false;\"";
if($thread['closed'])
	$replyWarning = " onclick=\"if(!confirm('".__("This thread is actually closed. Are you sure you want to abuse your staff position to post in a closed thread?")."')) return false;\"";

if($loguser['powerlevel'] < 0)
	$links .= "<li>".__("You're banned.");
elseif(IsAllowed("makeReply", $tid) && (!$thread['closed'] || $loguser['powerlevel'] > 2))
	$links .= actionLinkTagItem(__("Post reply"), "newreply", $tid);
elseif(IsAllowed("makeReply", $tid))
	$links .= "<li>".__("Thread closed.");
if(CanMod($loguserid,$forum['id']) && IsAllowed("editThread", $tid))
{
	$links .= actionLinkTagItem(__("Edit"), "editthread", $tid);
	if($thread['closed'])
		$links .= actionLinkTagItem(__("Open"), "editthread", $tid, "action=open&key=".$loguser['token']);
	else
		$links .= actionLinkTagItem(__("Close"), "editthread", $tid, "action=close&key=".$loguser['token']);
	if($thread['sticky'])
		$links .= actionLinkTagItem(__("Unstick"), "editthread", $tid, "action=unstick&key=".$loguser['token']);
	else
		$links .= actionLinkTagItem(__("Stick"), "editthread", $tid, "action=stick&key=".$loguser['token']);
	$links .= actionLinkTagItemConfirm(__("Delete"), __("Are you sure you want to just up and delete this whole thread?"), "editthread", $tid, "action=delete&key=".$loguser['token']);
	
	if($forum['id'] != Settings::get('trashForum'))
		$links .= actionLinkTagItem(__("Trash"), "editthread", $tid, "action=trash&key=".$loguser['token']);
}
else if($thread['user'] == $loguserid)
	$links .= actionLinkTagItem(__("Edit"), "editthread", $tid);

if($isBot)
	$links = "";

$OnlineUsersFid = $fid;
write(
"
	<script type=\"text/javascript\">
			window.addEventListener(\"load\",  hookUpControls, false);
	</script>
");

MakeCrumbs(array($forum['title']=>actionLink("forum", $fid), actionLink("thread", $tid) => $threadtags), $links);

if($thread['poll'])
{
	$rPoll = Query("select * from {poll} where id={0}", $thread['poll']);
	if(NumRows($rPoll))
	{
		$poll = Fetch($rPoll);

		$rCheck = Query("select * from {pollvotes} where poll={0} and user={1}", $thread['poll'], $loguserid);
		if(NumRows($rCheck))
		{
			while($check = Fetch($rCheck))
				$pc[$check['choice']] = "&#x2714; "; //use &#x2605; for a star
		}

		$totalVotes = FetchResult("select count(*) from {pollvotes} where poll={0}", $thread['poll']);

		$rOptions = Query("select * from {poll_choices} where poll={0}", $thread['poll']);
		$pops = 0;
		$options = array();
		$voters = array();
		$noColors = 0;
		$defaultColors = array(
					  "#0000B6","#00B600","#00B6B6","#B60000","#B600B6","#B66700","#B6B6B6",
			"#676767","#6767FF","#67FF67","#67FFFF","#FF6767","#FF67FF","#FFFF67","#FFFFFF",);
		while($option = Fetch($rOptions))
			$options[] = $option;

		foreach($options as $option)
		{			
			if($option['color'] == "")
				$option['color'] = $defaultColors[($pops + 9) % 15];
				
			$option['choice'] = htmlspecialchars($option['choice']);

			$rVotes = Query("select * from {pollvotes} where poll={0} and choice={1}", $thread['poll'], $pops);
			$votes = NumRows($rVotes);
			while($vote = Fetch($rVotes))
				if(!in_array($vote['user'], $voters))
					$voters[] = $vote['user'];

			$cellClass = ($cellClass+1) % 2;
			if($loguserid && !$thread['closed'] && IsAllowed("vote"))
				$label = $pc[$pops]." ".actionLinkTag($option['choice'], "thread", $thread['id'], "vote=$pops&token=".$loguser["token"]);
			else
				$label = format("{0} {1}", $pc[$pops], $option['choice']);
			
			$bar = "&nbsp;0";
			if($totalVotes > 0)
			{
				$width = 99 * ($votes / $totalVotes) + 0.1;
				$alt = format("{0}&nbsp;of&nbsp;{1},&nbsp;{2}%", $votes, $totalVotes, $width);
				$bar = format("<div class=\"pollbar\" style=\"background-color: {0}; width: {1}%;\" title=\"{2}\">&nbsp;{3}</div>", $option['color'], $width, $alt, $votes);
				if($width == 0)
					$bar = "&nbsp;".$votes;
			}

			$pollLines .= format(
"
		<tr class=\"cell{0}\">
			<td>
				{1}
			</td>
			<td class=\"width75\">
				<div class=\"pollbarContainer\">
					{2}
				</div>
			</td>
		</tr>
", $cellClass, $label, $bar);
			$pops++;
		}
		$voters = count($voters);
		write(
"
	<table class=\"outline margin\">
		<tr class=\"header0\">
			<th colspan=\"2\">
				".__("Poll")."
			</th>
		</tr>
		<tr class=\"cell0\">
			<td colspan=\"2\">
				{1}
			</td>
		</tr>
		{2}
		<tr class=\"cell0\">
			<td colspan=\"2\" class=\"smallFonts\">
				{3}
			</td>
		</tr>
	</table>
",	$cellClass, htmlspecialchars($poll['question']), $pollLines,
	format($voters == 1 ? __("{0} user has voted so far") : __("{0} users have voted so far"), $voters));
	}
}

$rRead = Query("delete from {threadsread} where id={0} and thread={1}", $loguserid, $tid);
$rRead = Query("insert into {threadsread} (id,thread,date) values ({0}, {1}, {2})", $loguserid, $tid, time());

$total = $thread['replies'] + 1; //+1 for the OP
$ppp = $loguser['postsperpage'];
if(!$ppp) $ppp = 20;
if(isset($_GET['from']))
	$from = $_GET['from'];
else
	if(isset($pid))
		$from = (floor(FetchResult("SELECT COUNT(*) FROM {posts} WHERE thread={1} AND date<={2} AND id!={0}", $pid, $tid, $post['date']) / $ppp)) * $ppp;
	else
		$from = 0;

$rPosts = Query("
			SELECT 
				p.*,
				pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
				u.(_userfields), u.(rankset,title,picture,posts,postheader,signature,signsep,lastposttime,lastactivity,regdate,globalblock),
				ru.(_userfields),
				du.(_userfields)
			FROM 
				{posts} p 
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision 
				LEFT JOIN {users} u ON u.id = p.user
				LEFT JOIN {users} ru ON ru.id=pt.user
				LEFT JOIN {users} du ON du.id=p.deletedby
			WHERE thread={1} 
			ORDER BY date ASC LIMIT {2u}, {3u}", $loguserid, $tid, $from, $ppp);

$numonpage = NumRows($rPosts);

$pagelinks = PageLinks(actionLink("thread", $tid, "from="), $ppp, $from, $total);
if ($pagelinks) write("<div class=\"smallFonts pages\">".__("Pages:")." {0}</div>", $pagelinks);

if(NumRows($rPosts))
{
	while($post = Fetch($rPosts))
	{
		$post['closed'] = $thread['closed'];
		MakePost($post, POST_NORMAL, array('tid'=>$tid, 'fid'=>$fid));
	}
}

if($loguserid && $loguser['powerlevel'] >= $forum['minpowerreply'] && (!$thread['closed'] || $loguser['powerlevel'] > 0) && !isset($replyWarning))
{
	$ninja = FetchResult("select id from {posts} where thread={0} order by date desc limit 0, 1", $tid);
	
	//Quick reply goes here		
	if(CanMod($loguserid, $fid))
	{
		//print $thread['closed'];
		if(!$thread['closed'])
			$mod .= "<label><input type=\"checkbox\" name=\"lock\">&nbsp;".__("Close thread", 1)."</label>\n";
		else
			$mod .= "<label><input type=\"checkbox\" name=\"unlock\">&nbsp;".__("Open thread", 1)."</label>\n";
		if(!$thread['sticky'])
			$mod .= "<label><input type=\"checkbox\" name=\"stick\">&nbsp;".__("Sticky", 1)."</label>\n";
		else
			$mod .= "<label><input type=\"checkbox\" name=\"unstick\">&nbsp;".__("Unstick", 1)."</label>\n";
	}
	$moodOptions = "<option ".$moodSelects[0]."value=\"0\">".__("[Default avatar]")."</option>\n";
	$rMoods = Query("select mid, name from {moodavatars} where uid={0} order by mid asc", $loguserid);
	while($mood = Fetch($rMoods))
		$moodOptions .= format(
"
	<option {0} value=\"{1}\">{2}</option>
",	$moodSelects[$mood['mid']], $mood['mid'], htmlspecialchars($mood['name']));

	write(
	"
	<form action=\"".actionLink("newreply")."\" method=\"post\">
		<input type=\"hidden\" name=\"ninja\" value=\"{0}\" />
		<table class=\"outline margin width75\" style=\"margin: 4px auto;\" id=\"quickreply\">
			<tr class=\"header1\">
				<th onclick=\"expandTable('quickreply', this)\" style=\"cursor: pointer;\">
					".__("Quick-E Post&trade;")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<textarea id=\"text\" name=\"text\" rows=\"8\" style=\"width: 98%;\">{3}</textarea>
				</td>
			</tr>
			<tr class=\"cell2\">
				<td>
					<input type=\"submit\" name=\"actionpost\" value=\"".__("Post")."\" /> 
					<input type=\"submit\" name=\"actionpreview\" value=\"".__("Preview")."\" />
					<select size=\"1\" name=\"mood\">
						{4}
					</select>
					<label>
						<input type=\"checkbox\" name=\"nopl\" {5} />&nbsp;".__("Disable post layout", 1)."
					</label>
					<label>
						<input type=\"checkbox\" name=\"nosm\" {6} />&nbsp;".__("Disable smilies", 1)."
					</label>
					<input type=\"hidden\" name=\"id\" value=\"{7}\" />
					{8}
				</td>
			</tr>
		</table>
	</form>
",	$ninja, 0, 0, $prefill, $moodOptions, $nopl, $nosm, $tid, $mod);
}

if ($pagelinks) write("<div class=\"smallFonts pages\">".__("Pages:")." {0}</div>", $pagelinks);

?>
