<?php
//  AcmlmBoard XD - Thread listing page
//  Access: all

if(!isset($_GET['id']))
	Kill(__("Forum ID unspecified."));

$fid = (int)$_GET['id'];

if($loguserid && $_GET['action'] == "markasread")
{
	Query("REPLACE INTO {threadsread} (id,thread,date) SELECT {0}, {threads}.id, {1} FROM {threads} WHERE {threads}.forum={2}",
		$loguserid, time(), $fid);

	redirectAction("board");
}

AssertForbidden("viewForum", $fid);

$pl = $loguser['powerlevel'];
if($pl < 0) $pl = 0;

$rFora = Query("select * from {forums} where id={0}", $fid);
if(NumRows($rFora))
{
	$forum = Fetch($rFora);
	if($forum['minpower'] > $pl)
		Kill(__("You are not allowed to browse this forum."));
} else
	Kill(__("Unknown forum ID."));

$title = $forum['title'];

setUrlName("newthread", $fid, $forum["title"]);

$isIgnored = FetchResult("select count(*) from {ignoredforums} where uid={0} and fid={1}", $loguserid, $fid) == 1;
if(isset($_GET['ignore']))
{
	if(!$isIgnored)
		Query("insert into {ignoredforums} values ({0}, {1})", $loguserid, $fid);
	redirectAction("forum", $fid);
}
else if(isset($_GET['unignore']))
{
	if($isIgnored)
		Query("delete from {ignoredforums} where uid={0} and fid={1}", $loguserid, $fid);
	redirectAction("forum", $fid);
}

$links = new PipeMenu();

if($loguserid)
	$links->add(new PipeMenuLinkEntry(__("Mark forum read"), "forum", $fid, "action=markasread"));

if($loguserid && $forum['minpowerthread'] <= $loguser['powerlevel'])
{
	if($isIgnored)
		$links->add(new PipeMenuLinkEntry(__("Unignore forum"), "forum", $fid, "unignore"));
	else
		$links->add(new PipeMenuLinkEntry(__("Ignore forum"), "forum", $fid, "ignore"));

	$links->add(new PipeMenuLinkEntry(__("Post thread"), "newthread", $fid));
}

makeLinks($links);

$crumbs = new PipeMenu();
makeForumCrumbs($crumbs, $forum);
makeBreadcrumbs($crumbs);

$OnlineUsersFid = $fid;

makeForumListing($fid);

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
						su.(_userfields),
						lu.(_userfields)
					FROM
						{threads} t
						".($loguserid ? "LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={3}" : '')."
						LEFT JOIN {users} su ON su.id=t.user
						LEFT JOIN {users} lu ON lu.id=t.lastposter
					WHERE forum={0}
					ORDER BY sticky DESC, lastpostdate DESC LIMIT {1u}, {2u}", $fid, $from, $tpp, $loguserid);

$numonpage = NumRows($rThreads);

$pagelinks = PageLinks(actionLink("forum", $fid, "from="), $tpp, $from, $total);

if($pagelinks)
	echo "<div class=\"smallFonts pages\">".__("Pages:")." ".$pagelinks."</div>";

if(NumRows($rThreads))
	echo listThreads($rThreads, true, false);
else
	if($forum['minpowerthread'] > $loguser['powerlevel'])
		Alert(__("You cannot start any threads here."), __("Empty forum"));
	elseif($loguserid)
		Alert(format(__("Would you like to {0}?"), actionLinkTag(__("post something"), "newthread", $fid)), __("Empty forum"));
	else
		Alert(format(__("{0} so you can post something."), actionLinkTag(__("Log in"), "login")), __("Empty forum"));

if($pagelinks)
	Write("<div class=\"smallFonts pages\">".__("Pages:")." {0}</div>", $pagelinks);

ForumJump();
printRefreshCode();

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
							{forums} f
							LEFT JOIN {categories} c ON c.id=f.catid
						WHERE f.minpower<={0}".(($pl < 1) ? " AND f.hidden=0" : '')."
						ORDER BY c.corder, c.id, f.forder, f.id", $pl);

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
", $optgroup, htmlspecialchars($forum['cname']));
			$optgroup = "</optgroup>";
		}

		$theList .= format(
"
				<option value=\"{0}\"{2}>{1}</option>
",	htmlentities(actionLink("forum", $forum['id'])), htmlspecialchars($forum['title']), ($forum['id'] == $fid ? " selected=\"selected\"" : ""));
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
