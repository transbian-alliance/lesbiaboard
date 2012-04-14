<?php
//  AcmlmBoard XD - Thread listing page
//  Access: all

if(!isset($_GET['id']))
	Kill(__("Forum ID unspecified."));

$fid = (int)$_GET['id'];
AssertForbidden("viewForum", $fid);

$pl = $loguser['powerlevel'];
if($pl < 0) $pl = 0;

$qFora = "select * from forums where id=".$fid;
$rFora = Query($qFora);
if(NumRows($rFora))
{
	$forum = Fetch($rFora);
	if($forum['minpower'] > $pl)
		Kill(__("You are not allowed to browse this forum."));
} else
	Kill(__("Unknown forum ID."));

$title = $forum['title'];

$qCat = "select * from categories where id=".$forum['catid'];
$rCat = Query($qCat);
if(NumRows($rCat))
{
	$cat = Fetch($rCat);
} else
	Kill(__("Unknown category ID."));


$isIgnored = FetchResult("select count(*) from ignoredforums where uid=".$loguserid." and fid=".$fid, 0, 0) == 1;
if(isset($_GET['ignore']))
{
	if(!$isIgnored)
	{
		Query("insert into ignoredforums values (".$loguserid.", ".$fid.")");
		Alert(__("Forum ignored. You will no longer see any \"New\" markers for this forum."));
	}
}
else if(isset($_GET['unignore']))
{
	if($isIgnored)
	{
		Query("delete from ignoredforums where uid=".$loguserid." and fid=".$fid);
		Alert(__("Forum unignored."));
	}
}

$isIgnored = FetchResult("select count(*) from ignoredforums where uid=".$loguserid." and fid=".$fid, 0, 0) == 1;
if($loguserid && $forum['minpowerthread'] <= $loguser['powerlevel'])
{
	if($isIgnored)
		$links .= "<li>".actionLinkTag(__("Unignore Forum"), "forum", $fid, "unignore")."</li>";
	else
		$links .= "<li>".actionLinkTag(__("Ignore Forum"), "forum", $fid, "ignore")."</li>";

		$links .= "<li>".actionLinkTag(__("Post Thread"), "newthread", $fid)."</li>";
		$links .= "<li>".actionLinkTag(__("Post Poll"), "newthread", $fid, "poll=1")."</li>";
}

$OnlineUsersFid = $fid;

MakeCrumbs(array(__("Main")=>"./", $forum['title']=>actionLink("forum", $fid)), $links);

$total = $forum['numthreads'];
$tpp = $loguser['threadsperpage'];
if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

if(!$tpp) $tpp = 50;

$rThreads = Query("	SELECT 
						t.*,
						".($loguserid ? "tr.date readdate," : '')."
						su.id suid, su.name suname, su.displayname sudisplayname, su.powerlevel supowerlevel, su.sex susex,
						lu.id luid, lu.name luname, lu.displayname ludisplayname, lu.powerlevel lupowerlevel, lu.sex lusex
					FROM 
						threads t
						".($loguserid ? "LEFT JOIN threadsread tr ON tr.thread=t.id AND tr.id=".$loguserid : '')."
						LEFT JOIN users su ON su.id=t.user
						LEFT JOIN users lu ON lu.id=t.lastposter
					WHERE forum=".$fid." 
					ORDER BY sticky DESC, lastpostdate DESC LIMIT ".$from.", ".$tpp);

$numonpage = NumRows($rThreads);

$pagelinks = PageLinks(actionLink("forum", $fid, "from="), $tpp, $from, $total);
		
if($pagelinks)
	echo "<div class=\"smallFonts pages\">".__("Pages:")." ".$pagelinks."</div>";

$ppp = $loguser['postsperpage'];
if(!$ppp) $ppp = 20;

$bucket = "topBar"; include("./lib/pluginloader.php");

if(NumRows($rThreads))
{	
	$forumList = "";
	while($thread = Fetch($rThreads))
	{
		$user = array('id'=>$thread['suid'], 'name'=>$thread['suname'], 'displayname'=>$thread['sudisplayname'], 'powerlevel'=>$thread['supowerlevel'], 'sex'=>$thread['susex']);
		$bucket = "userMangler"; include("./lib/pluginloader.php");
		$starter = $user;
		
		$user = array('id'=>$thread['luid'], 'name'=>$thread['luname'], 'displayname'=>$thread['ludisplayname'], 'powerlevel'=>$thread['lupowerlevel'], 'sex'=>$thread['lusex']);
		$bucket = "userMangler"; include("./lib/pluginloader.php");
		$last = $user;

		$tags = ParseThreadTags($thread['title']);

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

		$cellClass = ($cellClass + 1) % 2;

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

		$forumList .= "
		<tr class=\"cell$cellClass\">
			<td class=\"cell2 threadIcon\"> $NewIcon</td>
			<td class=\"threadIcon\" style=\"border-right: 0px none;\">
				 $ThreadIcon
			</td>
			<td style=\"border-left: 0px none;\">
				$poll
				".actionLinkTag(strip_tags($thread['title']), "thread", $thread['id'])."
				$pl
				$tags
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
	}
	
	
	Write(
"
	<table class=\"outline margin width100\">
		<tr class=\"header1\">
			<th style=\"width: 20px;\">&nbsp;</th>
			<th style=\"width: 16px;\">&nbsp;</th>
			<th style=\"width: 60%;\">".__("Title")."</th>
			<th>".__("Started by")."</th>
			<th>".__("Replies")."</th>
			<th>".__("Views")."</th>
			<th>".__("Last post")."</th>
		</tr>
		{0}
	</table>
",	$forumList);
} else
	if($forum['minpowerthread'] > $loguser['powerlevel'])
		Alert(__("You cannot start any threads here."), __("Empty forum"));
	elseif($loguserid)
		Alert(format(__("Would you like to {0}?"), actionLinkTag("post something", "newthread", $fid)), __("Empty forum"));
	else
		Alert(format(__("{0} so you can post something."), actionLinkTag("Log in", "login")), __("Empty forum"));

if($pagelinks)
	Write("<div class=\"smallFonts pages\">".__("Pages:")." {0}</div>", $pagelinks);

ForumJump();


function ForumJump()
{
	global $fid, $loguser;
	
	$pl = $loguser['powerlevel'];
	if($pl < 0) $pl = 0;
	
	$lastCatID = -1;	
	$rFora = Query("	SELECT 
							f.id, f.title, f.catid,
							c.name cname
						FROM 
							forums f
							LEFT JOIN categories c ON c.id=f.catid
						WHERE f.minpower<=".$pl.(($pl < 1) ? " AND f.hidden=0" : '')."
						ORDER BY c.corder, c.id, f.forder");
	
	$theList = "";
	$optgroup = "";
	while($forum = Fetch($rFora))
	{
		if($forum['catid'] != $lastCatID)
		{
			$lastCatID = $forum['catid'];
			$theList .= format(
"
			{0}
			<optgroup label=\"{1}\">
", $optgroup, strip_tags($forum['cname']));
			$optgroup = "</optgroup>";
		}

		$theList .= format(
"
				<option value=\"{0}\"{2}>{1}</option>
",	actionLink("forum", $forum['id']), strip_tags($forum['title']), ($forum['id'] == $fid ? " selected=\"selected\"" : ""));
	}
	
	write(
"
	<label>
		".__("Forum Jump:")."
		<select onchange=\"document.location=this.options[this.selectedIndex].value;\">
			{0}
			</optgroup>
		</select>
	</label>
",	$theList);
}

?>
