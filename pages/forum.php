<?php
//  AcmlmBoard XD - Thread listing page
//  Access: all

if(!isset($_GET['id']))
	Kill(__("Forum ID unspecified."));

$fid = (int)$_GET['id'];

if($loguserid && $_GET['action'] == "markasread")
{
	Query("REPLACE INTO threadsread (id,thread,date) SELECT ".$loguserid.", threads.id, ".time()." FROM threads WHERE threads.forum=$fid");
}

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

$user_panel = actionLinkTagItem(__("Mark forum read"), "forum", 0, "action=markasread&id=$fid");

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

MakeCrumbs(array($forum['title']=>actionLink("forum", $fid)), $links);

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
	$haveStickies = 0;
	$cellClass = 0;
	
	while($thread = Fetch($rThreads))
	{
		$forumList .= listThread($thread, $cellClass);
		$cellClass = ($cellClass + 1) % 2;
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
