<?php
//  AcmlmBoard XD support - Post functions

include_once("write.php");


function ParseThreadTags($title)
{
	preg_match_all("/\[(.*?)\]/", $title, $matches);
	foreach($matches[1] as $tag)
	{
		$title = str_replace("[".$tag."]", "", $title);
		$tag = htmlspecialchars(strtolower($tag));

		//Start at a hue that makes "18" red.
		$hash = -105;
		for($i = 0; $i < strlen($tag); $i++)
			$hash += ord($tag[$i]);

		//That multiplier is only there to make "nsfw" and "18" the same color.
		$color = "hsl(".(($hash * 57) % 360).", 70%, 40%)";

		$tags .= "<span class=\"threadTag\" style=\"background-color: ".$color.";\">".$tag."</span>";
	}
	if($tags)
		$tags = " ".$tags;

	$title = str_replace("<", "&lt;", $title);		
	$title = str_replace(">", "&gt;", $title);		
	return array(trim($title), $tags);
}

function filterPollColors($input)
{
	return preg_replace("@[^#0123456789abcdef]@si", "", $input);
}

function loadSmilies($byOrder = FALSE)
{
	global $smilies, $smiliesOrdered;
	
	if($byOrder)
	{
		if(isset($smiliesOrdered))
			return;
		$rSmilies = Query("select * from {smilies} order by id asc");
		$smiliesOrdered = array();
		while($smiley = Fetch($rSmilies))
			$smiliesOrdered[] = $smiley;
	}
	else
	{
		if(isset($smilies))
			return;
		$rSmilies = Query("select * from {smilies} order by length(code) desc");
		$smilies = array();
		while($smiley = Fetch($rSmilies))
		{
			$smilies[] = $smiley;
		}
	}
}

function applySmilies($text)
{
	global $smilies, $smiliesReplaceOrig, $smiliesReplaceNew;
	
	if (!isset($smiliesReplaceOrig))
	{
		$smiliesReplaceOrig = $smiliesReplaceNew = array();
		for ($i = 0; $i < count($smilies); $i++)
		{
			$smiliesReplaceOrig[] = "/(?<!\w)".preg_quote(htmlspecialchars($smilies[$i]['code']), "/")."(?!\w)/";
			$smiliesReplaceNew[] = "<img class=\"smiley\" alt=\"\" src=\"img/smilies/".$smilies[$i]['image']."\" />";
		}
	}
	return preg_replace($smiliesReplaceOrig, $smiliesReplaceNew, $text);
}

function loadBlockLayouts()
{
	global $blocklayouts, $loguserid;

	if(isset($blocklayouts))
		return;

	$rBlocks = Query("select * from {blockedlayouts} where blockee = {0}", $loguserid);
	$blocklayouts = array();

	while($block = Fetch($rBlocks))
		$blocklayouts[$block['user']] = 1;
}

function loadRanks($rankset)
{
	global $ranks;
	if(isset($ranks[$rankset]))
		return;
	$ranks[$poster['rankset']] = array();
	$rRanks = Query("select * from {ranks} where rset={0} order by num", $rankset);
	while($rank = Fetch($rRanks))
		$ranks[$rankset][$rank['num']] = $rank['text'];
}

function getRank($poster)
{
	global $ranks;
	if($poster['rankset'] == 0)
		return "";
	LoadRanks($poster['rankset']);
	$thisSet = $ranks[$poster['rankset']];
	if(!is_array($thisSet))
		return "";
	$ret = "";
	foreach($thisSet as $num => $text)
	{
		if($num > $poster['posts'])
			return $ret;
		$ret = $text;
	}
}

function getToNextRank($poster)
{
	global $ranks;
	if($poster['rankset'] == 0)
		return "";
	LoadRanks($poster['rankset']);
	$thisSet = $ranks[$poster['rankset']];
	if(!is_array($thisSet))
		return 0;
	$ret = 0;
	foreach($thisSet as $num => $text)
	{
		$ret = $num - $poster['posts'];
		if($num > $poster['posts'])
			return $ret;
	}
}

function makeUserAtLink($matches)
{
	global $members;
	$username = $matches[1];
	foreach($members as $id => $data)
	{
		if($data['name'] == $username)
		{
			return UserLink($members[$data['id']]);
		}
	}
	//Didn't find it in the cache.
	$rUser = Query("select u.(_userfields) from {users} u where name={0} or displayname={0}", $username);
	if(NumRows($rUser))
	{
		$hit = getDataPrefix(Fetch($rUser), "u_");
		$members[$hit['id']] = $hit;
		return UserLink($hit);
	}
	else
		return $username; //Return the actual name attempted.
}


function applyNetiquetteToLinks($match)
{
	if (substr($match[1], 0, 7) != 'http://')
		return $match[0];

	if (stripos($match[1], 'http://'.$_SERVER['SERVER_NAME']) === 0)
		return $match[0];

	return $match[0].' target="_blank"';
}


function getSyndrome($activity)
{
	include("syndromes.php");
	$soFar = "";
	foreach($syndromes as $minAct => $syndrome)
		if($activity >= $minAct)
			$soFar = "<em style=\"color: ".$syndrome[1].";\">".$syndrome[0]."</em><br />";
	return $soFar;
}

function postDoReplaceText($s)
{
	global $postNoSmilies, $postNoBr, $postPoster, $smilies;
	
	$s = preg_replace_callback("'@\"([\w ]+)\"'si", "MakeUserAtLink", $s);
	$s = preg_replace("'>>([0-9]+)'si",">>".actionLinkTag("\\1", "thread", "", "pid=\\1#\\1"), $s);
	if($postPoster)
		$s = preg_replace("'/me '","<b>* ".$postPoster."</b> ", $s);

	LoadSmilies();
	
	//Smilies
	if(!$postNoSmilies)
		$s = ApplySmilies($s);

	include("macros.php");
	foreach($macros as $macro => $img)
		$s = str_replace(":".$macro.":", "<img src=\"img/macros/".$img."\" alt=\":".$macro.":\" />", $s);

	$s = preg_replace_callback("@(?<![\]=\"'])https?://[^\s<]+[^<.,!?):\"'\s]@si", 'bbcodeURLAuto', $s);

	$bucket = "postMangler"; include("./lib/pluginloader.php");
	
	return $s;
}

function cleanUpPost($postText, $poster = "", $noSmilies = false, $noBr = false)
{
	global $postNoSmilies, $postNoBr, $smilies, $postPoster;
	static $orig, $repl;
	
	$postNoSmilies = $noSmilies;
	$postNoBr = $noBr;
	$postPoster = $poster;
	
	$s = $postText;
	
	$s = parseBBCode($s);

	$s = preg_replace_callback("@<a[^>]+href\s*=\s*\"(.*?)\"@si", 'ApplyNetiquetteToLinks', $s);
	$s = preg_replace_callback("@<a[^>]+href\s*=\s*'(.*?)'@si", 'ApplyNetiquetteToLinks', $s);
	$s = preg_replace_callback("@<a[^>]+href\s*=\s*([^\"'][^\s>]*)@si", 'ApplyNetiquetteToLinks', $s);

	$s = securityPostFilter($s);
	
	return $s;
}


function applyTags($text, $tags)
{
	if(!stristr($text, "&"))
		return $text;
	$s = $text;
	foreach($tags as $tag => $val)
		$s = str_replace("&".$tag."&", $val, $s);
	if(is_numeric($tags['numposts']))
		$s = preg_replace('@&(\d+)&@sie', 'max($1 - '.$tags['numposts'].', 0)', $s);
	else
		$s = preg_replace("'&(\d+)&'si", "preview", $s);
	return $s;
}

//This function is CRITICAL for the post security.
//Should always run LAST and on the WHOLE post.

$badTags = array('script','iframe','frame','blink','textarea','noscript','meta','xmp','plaintext','marquee','embed','object');

function filterJS($match)
{
	$url = html_entity_decode($match[2]);
	if (stristr($url, "javascript:"))
		return "";
	return $match[0];
}

//Scans for any numerical entities that decode to the 7-bit printable ASCII range and removes them.
//This makes a last-minute hack impossible where a javascript: link is given completely in absurd and malformed entities.
function eatThatPork($s)
{
	$s = preg_replace_callback("/(&#)(x*)([a-f0-9]+(?![a-f0-9]))(;*)/i", "checkKosher", $s);
	return $s;
}

function checkKosher($matches)
{
	$num = ltrim($matches[3], "0");
	if($matches[2])
		$num = hexdec($num);
	if($num < 127)
		return ""; //"&#xA4;";
	else
		return "&#x".dechex($num).";";
}

function securityPostFilter($s)
{
	$s = str_replace("\r\n","\n", $s);

	$s = EatThatPork($s);

	$s = preg_replace("@(on)(\w+?\s*?)=@si", '$1$2&#x3D;', $s);

	$s = preg_replace("'-moz-binding'si"," -mo<em></em>z-binding", $s);
	$s = preg_replace("'filter:'si","filter<em></em>:>", $s);
	$s = preg_replace("'javascript:'si","javascript<em></em>:>", $s);

	$s = preg_replace_callback("@(href|src)\s*=\s*\"([^\"]+)\"@si", "FilterJS", $s);
	$s = preg_replace_callback("@(href|src)\s*=\s*'([^']+)'@si", "FilterJS", $s);
	$s = preg_replace_callback("@(href|src)\s*=\s*([^\s>]+)@si", "FilterJS", $s);

	return $s;
}

$activityCache = array();
function getActivity($id)
{
	global $activityCache;
	
	if(!isset($activityCache[$id]))
		$activityCache[$id] = FetchResult("select count(*) from {posts} where user = {0} and date > {1}", $id, (time() - 86400));

	return $activityCache[$id];
}

$layouCache = array();

function makePostText($post)
{
	global $loguser, $loguserid, $theme, $hacks, $isBot, $postText, $sideBarStuff, $sideBarData, $salt, $layoutCache, $blocklayouts;

	LoadBlockLayouts();
	$poster = getDataPrefix($post, "u_");
	$isBlocked = $poster['globalblock'] || $loguser['blocklayouts'] || $post['options'] & 1 || isset($blocklayouts[$poster['id']]);

	$noSmilies = $post['options'] & 2;
	$noBr = $post['options'] & 4;

	//Do Ampersand Tags
	$tags = array
	(
//This tag breaks because of layout caching.
//		"postnum" => $post['num'],
		"postcount" => $poster['posts'],
		"numdays" => floor((time()-$poster['regdate'])/86400),
		"date" => formatdate($post['date']),
		"rank" => GetRank($poster),
	);
	$bucket = "amperTags"; include("./lib/pluginloader.php");

	$postText = $post['text'];
	$postText = ApplyTags($postText, $tags);
	$postText = CleanUpPost($postText, $poster['name'], $noSmilies, $noBr);

	//Post header and footer.
	$magicString = "###POSTTEXTGOESHEREOMG###";
	$separator = "";
	
	if($isBlocked)
		$postLayout = $magicString;
	else
	{
		if(!isset($layoutCache[$poster["id"]]))
		{
			$postLayout = $poster['postheader'].$magicString.$poster['signature'];
			$postLayout = ApplyTags($postLayout, $tags);
			$postLayout = CleanUpPost($postLayout, $poster['name'], $noSmilies, true);
			$layoutCache[$poster["id"]] = $postLayout;
		}
		else
			$postLayout = $layoutCache[$poster["id"]];
		
		if($poster['signature'])
			if(!$poster['signsep'])
				$separator = "<br />_________________________<br />";
			else
				$separator = "<br />";
	}
	
	$postText = str_replace($magicString, "<!-- LOL -->".$postText.$separator, $postLayout);
	return $postText;
}

define('POST_NORMAL', 0);			// standard post box
define('POST_PM', 1);				// PM post box
define('POST_DELETED_SNOOP', 2);	// post box with close/undelete (for mods 'view deleted post' feature)
define('POST_SAMPLE', 3);			// sample post box (profile sample post, newreply post preview, etc)

$sideBarStuff = "";
$sideBarData = 0;

// $post: post data (typically returned by SQL queries or forms)
// $type: one of the POST_XXX constants
// $params: an array of extra parameters, depending on the post box type. Possible parameters:
//		* tid: the ID of the thread the post is in (POST_NORMAL and POST_DELETED_SNOOP only)
//		* fid: the ID of the forum the thread containing the post is in (POST_NORMAL and POST_DELETED_SNOOP only)
// 		* threadlink: if set, a link to the thread is added next to 'Posted on blahblah' (POST_NORMAL and POST_DELETED_SNOOP only)
//		* noreplylinks: if set, no links to newreply.php (Quote/ID) are placed in the metabar (POST_NORMAL only)
//		* forcepostnum: if set, forces sidebar to show "Posts: X/X" (POST_SAMPLE only)
//		* metatext: if non-empty, this text is displayed in the metabar instead of 'Sample post' (POST_SAMPLE only)
function makePost($post, $type, $params=array())
{
	global $loguser, $loguserid, $theme, $hacks, $isBot, $blocklayouts, $postText, $sideBarStuff, $sideBarData, $salt, $dataDir, $dataUrl;

	$sideBarStuff = "";
	$poster = getDataPrefix($post, "u_");
	LoadBlockLayouts();
	$isBlocked = $poster['globalblock'] || $loguser['blocklayouts'] || $post['options'] & 1 || isset($blocklayouts[$poster['id']]);
	
	if(isset($_GET['pid']))
		$highlight = (int)$_GET['pid'];

	if($post['deleted'] && $type == POST_NORMAL)
	{
		$meta = format(__("Posted on {0}"), formatdate($post['date']));
		$meta .= __(', deleted');
		if ($post['deletedby'])
		{
			$db_link = UserLink(getDataPrefix($post, "du_"));
			$meta .= __(' by ').$db_link;
			
			if ($post['reason'])
				$meta .= ': '.htmlspecialchars($post['reason']);
		}
		
		$links = new PipeMenu();

		if(CanMod($loguserid,$params['fid']))
		{
			if (IsAllowed("editPost", $post['id']))
				$links->add(new PipeMenuLinkEntry(__("Undelete"), "editpost", $post['id'], "delete=2&key=".$loguser['token']));
			$links->add(new PipeMenuHtmlEntry("<a href=\"#\" onclick=\"replacePost(".$post['id'].",true); return false;\">".__("View")."</a>"));
		}

		$links->add(new PipeMenuTextEntry(format(__("ID: {0}"), $post['id'])));
		write(
"
		<table class=\"post margin deletedpost\" id=\"post{0}\">
			<tr>
				<td class=\"side userlink\" id=\"{0}\">
					{1}
				</td>
				<td class=\"smallFonts meta right\">
					<div style=\"float:left\">
						{2}
					</div>
					{3}
				</td>
			</tr>
		</table>
",	$post['id'], userLink($poster), $meta, $links->build()
);
		return;
	}

	$links = new PipeMenu();

	if ($type == POST_SAMPLE)
		$meta = $params['metatext'] ? $params['metatext'] : __("Sample post");
	else
	{
		$forum = $params['fid'];
		$thread = $params['tid'];
		$canmod = CanMod($loguserid, $forum);
		$replyallowed = IsAllowed("makeReply", $thread);
		$editallowed = IsAllowed("editPost", $post['id']);
		$canreply = $replyallowed && ($canmod || (!$post['closed'] && $loguser['powerlevel'] > -1));

		if (!$isBot)
		{
			if ($type == POST_DELETED_SNOOP)
			{
				$links->add(new PipeMenuTextEntry(__("Post deleted.")));
				if ($editallowed)
					$links->add(new PipeMenuLinkEntry(__("Undelete"), "editpost", $post['id'], "delete=2&key=".$loguser['token']));
				$links->add(new PipeMenuHtmlEntry("<a href=\"#\" onclick=\"replacePost(".$post['id'].",false); return false;\">".__("Close")."</a>"));
				$links->add(new PipeMenuHtmlEntry(format(__("ID: {0}"), $post['id'])));
			}
			else if ($type == POST_NORMAL)
			{
				$links->add(new PipeMenuLinkEntry(__("Link"), "thread", "", "pid=".$post['id']."#".$post['id']));

				if ($canreply && !$params['noreplylinks'])
					$links->add(new PipeMenuLinkEntry(__("Quote"), "newreply", $thread, "quote=".$post['id']));

				if ($editallowed && ($canmod || ($poster['id'] == $loguserid && $loguser['powerlevel'] > -1 && !$post['closed'])))
					$links->add(new PipeMenuLinkEntry(__("Edit"), "editpost", $post['id']));

				if ($editallowed && $canmod)
				{
					// TODO: perhaps make delete links not require a key to be passed
					//  * POST-form delete confirmation, on separate page, a la Jul?
					//  * hidden form and Javascript-submit() link?
					$link = actionLink('editpost', $post['id'], 'delete=1&key='.$loguser['token']);
					$links->add(new PipeMenuHtmlEntry("<a href=\"{$link}\" onclick=\"deletePost(this);return false;\">".__('Delete')."</a>"));
				}
				if ($canreply && !$params['noreplylinks'])
					$links->add(new PipeMenuHtmlEntry(format(__("ID: {0}"), actionLinkTag($post['id'], "newreply", $thread, "link=".$post['id']))));
				else
					$links->add(new PipeMenuHtmlEntry(format(__("ID: {0}"), $post['id'])));
				if ($loguser['powerlevel'] > 0)
					$links->add(new PipeMenuTextEntry($post['ip']));
			
				$bucket = "topbar"; include("./lib/pluginloader.php");
			}
		}

		if ($type == POST_PM)
			$message = __("Sent on {0}");
		else
			$message = __("Posted on {0}");

		$meta = format($message, formatdate($post['date']));

		//Threadlinks for listpost.php
		if ($params['threadlink'])
		{
			$thread = array();
			$thread["id"] = $post["thread"];
			$thread["title"] = $post["threadname"];
			
			$meta .= " ".__("in")." ".makeThreadLink($thread);
		}

		//Revisions
		if($post['revision'])
		{
			if ($post['revuser'])
			{
				$ru_link = UserLink(getDataPrefix($post, "ru_"));
				$revdetail = " ".format(__("by {0} on {1}"), $ru_link, formatdate($post['revdate']));
			}
			else
				$revdetail = '';

			if ($canmod)
				$meta .= " (<a href=\"javascript:void(0);\" onclick=\"showRevisions(".$post['id'].")\">".format(__("rev. {0}"), $post['revision'])."</a>".$revdetail.")";
			else
				$meta .= " (".format(__("rev. {0}"), $post['revision']).$revdetail.")";
		}
		//</revisions>
	}
	

	// POST SIDEBAR 

	$sideBarStuff .= GetRank($poster);
	if($sideBarStuff)
		$sideBarStuff .= "<br />";
	if($poster['title'])
		$sideBarStuff .= strip_tags(CleanUpPost($poster['title'], "", true), "<b><strong><i><em><span><s><del><img><a><br /><small>")."<br />";
	else
	{
		$levelRanks = array(-1=>__("Banned"), 0=>"", 1=>__("Local mod"), 2=>__("Full mod"), 3=>__("Administrator"));
		$sideBarStuff .= $levelRanks[$poster['powerlevel']]."<br />";
	}
	$sideBarStuff .= GetSyndrome(getActivity($poster["id"]));

	if($post['mood'] > 0)
	{
		if(file_exists("${dataDir}avatars/".$poster['id']."_".$post['mood']))
			$sideBarStuff .= "<img src=\"${dataUrl}avatars/".$poster['id']."_".$post['mood']."\" alt=\"\" />";
	}
	else
	{
		if($poster["picture"] == "#INTERNAL#")
			$sideBarStuff .= "<img src=\"${dataUrl}avatars/".$poster['id']."\" alt=\"\" />";
		else if($poster["picture"])
			$sideBarStuff .= "<img src=\"".htmlspecialchars($poster["picture"])."\" alt=\"\" />";
	}

	$lastpost = ($poster['lastposttime'] ? timeunits(time() - $poster['lastposttime']) : "none");
	$lastview = timeunits(time() - $poster['lastactivity']);

	if(!$params['forcepostnum'] && ($type == POST_PM || $type == POST_SAMPLE))
		$sideBarStuff .= "<br />\n".__("Posts:")." ".$poster['posts'];
	else
		$sideBarStuff .= "<br />\n".__("Posts:")." ".$post['num']."/".$poster['posts'];

	$sideBarStuff .= "<br />\n".__("Since:")." ".cdate($loguser['dateformat'], $poster['regdate'])."<br />";

	$bucket = "sidebar"; include("./lib/pluginloader.php");

	if(Settings::get("showExtraSidebar"))
	{
		$sideBarStuff .= "<br />\n".__("Last post:")." ".$lastpost;
		$sideBarStuff .= "<br />\n".__("Last view:")." ".$lastview;

		if($poster['lastactivity'] > time() - 300)
			$sideBarStuff .= "<br />\n".__("User is <strong>online</strong>");
	}
	
	// OTHER STUFF
	
	if($type == POST_NORMAL)
		$anchor = "<a name=\"".$post['id']."\" />";
	
	if(!$isBlocked)
	{
		$pTable = "table".$poster['id'];
		$row1 = "row".$poster['id']."_1";
		$row2 = "row".$poster['id']."_2";
		$topBar1 = "topbar".$poster['id']."_1";
		$topBar2 = "topbar".$poster['id']."_2";
		$sideBar = "sidebar".$poster['id'];
		$mainBar = "mainbar".$poster['id'];
	}

	$highlightClass = "";
	if($post['id'] == $highlight)
		$highlightClass = "highlightedPost";

	$postText = makePostText($post);

	//PRINT THE POST!
	
	echo "
		<table class=\"post margin $highlightClass $pTable\" id=\"post${post['id']}\">
			<tr class=\"$row1\">
				<td class=\"side userlink $topBar1\">
					$anchor
					".UserLink($poster)."
				</td>
				<td class=\"meta right $topBar2\">
					<div style=\"float: left;\" id=\"meta_${post['id']}\">
						$meta
					</div>
					<div style=\"float: left; text-align:left; display: none;\" id=\"dyna_${post['id']}\">
						Hi.
					</div>
					" . $links->build() . "
				</td>
			</tr>
			<tr class=\"".$row2."\">
				<td class=\"side $sideBar\">
					<div class=\"smallFonts\">
						$sideBarStuff
					</div>
				</td>
				<td class=\"post $mainBar\" id=\"post_${post['id']}\">
					$postText
				</td>
			</tr>
		</table>";
}

?>
