<?php
//  AcmlmBoard XD - User profile page
//  Access: all

AssertForbidden("viewProfile");

if(isset($_POST['id']))
	$_GET['id'] = $_POST['id'];

if(!isset($_GET['id']))
	Kill(__("User ID unspecified."));

$id = (int)$_GET['id'];

$rUser = Query("select * from {users} where id={0}", $id);
if(NumRows($rUser))
	$user = Fetch($rUser);
else
	Kill(__("Unknown user ID."));

if($id == $loguserid)
{
	Query("update {users} set newcomments = 0 where id={0}", $loguserid);
	$loguser['newcomments'] = false;
}

$canDeleteComments = ($id == $loguserid || $loguser['powerlevel'] > 2) && IsAllowed("deleteComments");
$canComment = true;

if($loguser['powerlevel'] < 0)
{
	$canDeleteComments = false;
	$canComment = false;
}

if(isset($_GET['block']) && $loguserid && $_GET['token'] == $loguser['token'])
{
	AssertForbidden("blockLayouts");
	$block = (int)$_GET['block'];
	$rBlock = Query("select * from {blockedlayouts} where user={0} and blockee={1}", $id, $loguserid);
	$isBlocked = NumRows($rBlock);
	if($block && !$isBlocked)
	{
		$rBlock = Query("insert into {blockedlayouts} (user, blockee) values ({0}, {1})", $id, $loguserid);
		Alert(__("Layout blocked."), __("Notice"));
	}
	elseif(!$block && $isBlocked)
	{
		$rBlock = Query("delete from {blockedlayouts} where user={0} and blockee={1} limit 1", $id, $loguserid);
		Alert(__("Layout unblocked."), __("Notice"));
	}
}

$canVote = ($loguser['powerlevel'] > 0 || ((time()-$loguser['regdate'])/86400) > 9) && IsAllowed("vote");
if($loguserid == $id) $canVote = FALSE;

if($loguserid)
{
	if(IsAllowed("blockLayouts"))
	{
		$rBlock = Query("select * from {blockedlayouts} where user={0} and blockee={1}", $id, $loguserid);
		$isBlocked = NumRows($rBlock);
		if($isBlocked)
			$blockLayoutLink = actionLinkTagItem(__("Unblock layout"), "profile", $id, "block=0&token={$loguser['token']}");
		else
			$blockLayoutLink = actionLinkTagItem(__("Block layout"), "profile", $id, "block=1&token={$loguser['token']}");
	}

	if(isset($_GET['vote']) && $canVote && $_GET['token'] == $loguser['token'])
	{
		$vote = (int)$_GET['vote'];
		if($vote > 1) $vote = 1 ;
		if($vote < -1) $vote = -1;
		// TODO: this could be considerably simplified
		// (INSERT ... ON DUPLICATE KEY UPDATE and primary index on uid+voter)
		$k = FetchResult("select count(*) from {uservotes} where uid={0} and voter={1}", $id, $loguserid);
		if($k == 0)
			$_qKarma = "insert into uservotes (uid, voter, up) values ({0}, {1}, {2})";
		else
			$_qKarma = "update {uservotes} set up={2} where uid={0} and voter={1}";
		$rKarma = Query($_qKarma, $id, $loguserid, $vote);
		$user['karma'] = RecalculateKarma($id);
	}

	$k = FetchResult("select up from {uservotes} where uid={0} and voter={1}", $id, $loguserid);
	
	$karmalinks = "";
	if($k != 1)
		$karmaLinks .= actionLinkTag("&#x2191;", "profile", $id, "vote=1&token={$loguser['token']}");
		
	if($k != 0)
		$karmaLinks .= actionLinkTag("&#x2193;", "profile", $id, "vote=0&token={$loguser['token']}");
		
	$karmaLinks = "<small>[$karmaLinks]</small>";
}

$karma = $user['karma'];
if(!$canVote)
	$karmaLinks = "";

$daysKnown = (time()-$user['regdate'])/86400;

$posts = FetchResult("select count(*) from {posts} where user={0}", $id);

$threads = FetchResult("select count(*) from {threads} where user={0}", $id);

$averagePosts = sprintf("%1.02f", $user['posts'] / $daysKnown);
$averageThreads = sprintf("%1.02f", $threads / $daysKnown);

$score = ((int)$daysKnown * 2) + ($posts * 4) + ($threads * 8) + (($karma - 100) * 3);

$minipic = "";
if($user["minipic"] == "#INTERNAL#")
	$minipic = "<img src=\"${dataUrl}minipics/${user["id"]}\" alt=\"\" class=\"minipic\" />&nbsp;";
else if($user["minipic"])
	$minipic = "<img src=\"".$user['minipic']."\" alt=\"\" class=\"minipic\" />&nbsp;";


if($user['rankset'])
{
	$currentRank = GetRank($user);
	$toNextRank = GetToNextRank($user);
	if($toNextRank)
		$toNextRank = Plural($toNextRank, "post");
}
if($user['title'])
	$title = str_replace("<br />", " &bull; ", strip_tags(CleanUpPost($user['title'], "", true), "<b><strong><i><em><span><s><del><img><a><br><small>"));
//$title = "";

if($user['homepageurl'])
{
	if($user['homepagename'])
		$homepage = "<a target=\"_blank\" href=\"".htmlspecialchars($user['homepageurl'])."\">".htmlspecialchars($user['homepagename'])."</a> - ".htmlspecialchars($user['homepageurl']);
	else
		$homepage = "<a target=\"_blank\" href=\"".htmlspecialchars($user['homepageurl'])."\">".htmlspecialchars($user['url'])."</a>";
}

$emailField = __("Private");
if($user['email'] == "")
{
	$emailField = __("None given");
}
elseif($user['showemail'])
{
	$emailField = "<span id=\"emailField\">".__("Public")." <button style=\"font-size: 0.7em;\" onclick=\"$(this.parentNode).load('ajaxcallbacks.php?a=em&amp;id=".$id."');\">".__("Show")."</button></span>";
}

if($user['tempbantime'])
{
	write(
"
	<div class=\"outline margin cell1 smallFonts\">
		".__("This user has been temporarily banned until {0} (GMT). That's {1} left.")."
	</div>
",	gmdate("M jS Y, G:i:s",$user['tempbantime']), TimeUnits($user['tempbantime'] - time())
	);
}



$profileParts = array();

$foo = array();
$foo[__("Name")] = $minipic . htmlspecialchars($user['displayname'] ? $user['displayname'] : $user['name']) . ($user['displayname'] ? " (".$user['name'].")" : "");
$foo[__("Power")] = getPowerlevelName($user['powerlevel']);
$foo[__("Sex")] = getSexName($user['sex']);
if($title)
	$foo[__("Title")] = $title;
if($currentRank)
	$foo[__("Rank")] = $currentRank;
if($toNextRank)
	$foo[__("To next rank")] = $toNextRank;
$foo[__("Karma")] = $karma.$karmaLinks;
$foo[__("Total posts")] = format("{0} ({1} per day)", $posts, $averagePosts);
$foo[__("Total threads")] = format("{0} ({1} per day)", $threads, $averageThreads);
$foo[__("Registered on")] = format("{0} ({1} ago)", formatdate($user['regdate']), TimeUnits($daysKnown*86400));
$foo[__("Score")] = $score;
$foo[__("Browser")] = $user['lastknownbrowser'];
if($loguser['powerlevel'] > 0)
	$foo[__("Last known IP")] = formatIP($user['lastip']);	
$profileParts[__("General information")] = $foo;

$foo = array();
$foo[__("Email address")] = $emailField;
if($homepage)
	$foo[__("Homepage")] = securityPostFilter($homepage);
$profileParts[__("Contact information")] = $foo;

$foo = array();
$infofile = "themes/".$user['theme']."/themeinfo.txt";

$themeinfo = file_get_contents($infofile);
$themeinfo = explode("\n", $themeinfo, 2);

if(file_exists($infofile))
{
	$themename = trim($themeinfo[0]);
	$themeauthor = trim($themeinfo[1]);
}
else
{
	$themename = $user['theme'];
	$themeauthor = "";
}
$foo[__("Theme")] = $themename;
$foo[__("Items per page")] = Plural($user['postsperpage'], __("post")) . ", " . Plural($user['threadsperpage'], __("thread"));
$profileParts[__("Presentation")] = $foo;

$foo = array();
if($user['realname'])
	$foo[__("Real name")] = htmlspecialchars($user['realname']);
if($user['location'])
	$foo[__("Location")] = htmlspecialchars($user['location']);
if($user['birthday'])
	$foo[__("Birthday")] = format("{0} ({1} old)", cdate("F j, Y", $user['birthday']), Plural(floor((time() - $user['birthday']) / 86400 / 365.2425), "year"));
if($user['bio'])
	$foo[__("Bio")] = CleanUpPost($user['bio']);
if(count($foo))
	$profileParts[__("Personal information")] = $foo;

$badgersR = Query("select * from {badges} where owner={0} order by color", $id);
if(NumRows($badgersR))
{
	$badgers = "";
	$colors = array("bronze", "silver", "gold", "platinum");
	while($badger = Fetch($badgersR))
		$badgers .= Format("<span class=\"badge {0}\">{1}</span> ", $colors[$badger['color']], $badger['name']);
	$profileParts['General information']['Badges'] = $badgers;
}

$prepend = "";
$bucket = "profileTable"; include("./lib/pluginloader.php");

write("
	<table>
		<tr>
			<td style=\"width: 60%; border: 0px none; vertical-align: top; padding-right: 1em; padding-bottom: 1em;\">
				{0}
				<table class=\"outline margin\">
", $prepend);
$cc = 0;
foreach($profileParts as $partName => $fields)
{
	write("
					<tr class=\"header0\">
						<th colspan=\"2\">{0}</th>
					</tr>
", $partName);
	foreach($fields as $label => $value)
	{
		$cc = ($cc + 1) % 2;
		write("
							<tr>
								<td class=\"cell2\">{0}</td>
								<td class=\"cell{2}\">{1}</td>
							</tr>
", str_replace(" ", "&nbsp;", $label), $value, $cc);
	}
}

write("
				</table>
");

$bucket = "profileLeft"; include("./lib/pluginloader.php");
write("
			</td>
");

if($canDeleteComments && $_GET['action'] == "delete" && $_GET['token'] == $loguser['token'])
{
	AssertForbidden("deleteComments");
	Query("delete from {usercomments} where uid={0} and id={1}", $id, (int)$_GET['cid']);
}

if($_POST['action'] == __("Post") && IsReallyEmpty($_POST['text']) && $loguserid 
	/*&& $loguserid != $lastCID*/ && $_POST['token'] == $loguser['token'] && $canComment)
{
	AssertForbidden("makeComments");

	$newID = FetchResult("SELECT id+1 FROM {usercomments} WHERE (SELECT COUNT(*) FROM {usercomments} u2 WHERE u2.id={usercomments}.id+1)=0 ORDER BY id ASC LIMIT 1");
	if($newID < 1) $newID = 1;
	$rComment = Query("insert into {usercomments} (id, uid, cid, date, text) values ({0}, {1}, {2}, {3}, {4})", $newID, $id, $loguserid, time(), $_POST['text']);
	if($loguserid != $id)
		Query("update {users} set newcomments = 1 where id={0}", $id);
}


$rComments = Query("SELECT 
		u.(_userfields),
		{usercomments}.id, {usercomments}.cid, {usercomments}.text 
		FROM {usercomments} 
		LEFT JOIN {users} u ON u.id = {usercomments}.cid 
		WHERE uid={0} 
		ORDER BY {usercomments}.date DESC LIMIT 0,10", $id);
		
$commentList = "";
$commentField = "";
if(NumRows($rComments))
{
	while($comment = Fetch($rComments))
	{
		if($canDeleteComments)
			$deleteLink = "<small style=\"float: right; margin: 0px 4px;\">".
				actionLinkTag("&#x2718;", "profile", $id, "action=delete&cid=".$comment['id']."&token={$loguser['token']}")."</small>";
		$cellClass = ($cellClass+1) % 2;
		$thisComment = format(
"
						<tr>
							<td class=\"cell2 width25\">
								{0}
							</td>
							<td class=\"cell{1}\">
								{3}{2}
							</td>
						</tr>
",	UserLink(getDataPrefix($comment, "u_")), $cellClass, CleanUpPost($comment['text']), $deleteLink);
		$commentList = $thisComment . $commentList;
		if(!isset($lastCID))
			$lastCID = $comment['cid'];
	}
}
else
{
	$commentsWasEmpty = true;
	$commentList = $thisComment = format(
"
						<tr>
							<td class=\"cell0\" colspan=\"2\">
								".__("No comments.")."

							</td>
						</tr>
");
}

//print "lastCID: ".$lastCID;

if($loguserid )
{
	$commentField = format(
"
								<div>
									<form method=\"post\" action=\"".actionLink("profile")."\">
										<input type=\"hidden\" name=\"id\" value=\"{0}\" />
										<input type=\"text\" name=\"text\" style=\"width: 80%;\" maxlength=\"255\" />
										<input type=\"submit\" name=\"action\" value=\"".__("Post")."\" />
										<input type=\"hidden\" name=\"token\" value=\"{$loguser['token']}\" />
									</form>
								</div>
", $id);
//	if($lastCID == $loguserid)
//		$commentField = __("You already have the last word.");
	if(!IsAllowed("makeComments") || !$canComment)
		$commentField = __("You are not allowed to post usercomments.");
}

write(
"
			<td style=\"vertical-align: top; border: 0px none;\">
				<table class=\"outline margin\">
					<tr class=\"header1\">
						<th colspan=\"2\">
							".__("Comments about {0}")."
						</th>
					</tr>
					{1}
					<tr>
						<td colspan=\"2\" class=\"cell2\">
							{2}
						</td>
					</tr>
				</table>
",	UserLink($user), $commentList, $commentField);

$bucket = "profileRight"; include("./lib/pluginloader.php");

write(
"
			</td>
		</tr>
	</table>
");

$previewPost['text'] = Settings::get("profilePreviewText");

$previewPost['num'] = "preview";
$previewPost['id'] = "preview";
$copies = explode(",","id,title,name,displayname,picture,sex,powerlevel,avatar,postheader,rankset,signature,signsep,posts,regdate,lastactivity,lastposttime,globalblock");
foreach($copies as $toCopy)
	$previewPost["u_".$toCopy] = $user[$toCopy];

MakePost($previewPost, POST_SAMPLE);


if(IsAllowed("editProfile") && $loguserid == $id)
	$links .= actionLinkTagItem(__("Edit my profile"), "editprofile", $id);
else if(IsAllowed("editUser") && $loguser['powerlevel'] > 2)
	$links .= actionLinkTagItem(__("Edit user"), "editprofile", $id);

if(IsAllowed("snoopPM") && $loguser['powerlevel'] > 2)
	$links .= actionLinkTagItem(__("Show PMs"), "private", "", "user=".$id);

if($loguserid && IsAllowed("sendPM"))
	$links .= actionLinkTagItem(__("Send PM"), "sendprivate", "", "uid=".$id);
if(IsAllowed("listPosts"))
		$links .= actionLinkTagItem(__("Show posts"), "listposts", $id);

$links .= $blockLayoutLink;

$uname = $user["name"];
if($user["displayname"])
	$uname = $user["displayname"];
MakeCrumbs(array(__("Member list")=>actionLink("memberlist"), $uname => actionLink("profile", $id)), $links);

$title = "Profile for ".htmlspecialchars($user['name']);

function IsReallyEmpty($subject)
{
	$trimmed = trim(preg_replace("/&.*;/", "", $subject));
	return strlen($trimmed) != 0;
}


?>
