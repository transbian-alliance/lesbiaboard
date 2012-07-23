<?php

$ajaxPage = true;
include("lib/common.php");
header("Cache-Control: no-cache");

$action = $_GET['a'];
$id = (int)$_GET['id'];
$hideTricks = " <a href=\"javascript:void(0)\" onclick=\"hideTricks(".$id.")\">".__("Back")."</a>";
if($action == "q")	//Quote
{
	$qQuote = "	select
					p.id, p.deleted, pt.text,
					f.minpower,
					u.name poster
				from posts p
					left join {posts_text} pt on pt.pid = p.id and pt.revision = p.currentrevision
					left join {threads} t on t.id=p.thread
					left join {forums} f on f.id=t.forum
					left join {users} u on u.id=p.user
				where p.id={0}";
	$rQuote = Query($qQuote, $id);

	if(!NumRows($rQuote))
		die(__("Unknown post ID."));

	$quote = Fetch($rQuote);

	//SPY CHECK!
	//Do we need to translate this line? It's not even displayed in its true form ._.
	if($quote['minpower'] > $loguser['powerlevel'])
		$quote['text'] = str_rot13("Pools closed due to not enough power. Prosecutors will be violated.");

	if ($quote['deleted'])
		$quote['text'] = __("Post is deleted");

	$reply = "[quote=\"".$quote['poster']."\" id=\"".$quote['id']."\"]".$quote['text']."[/quote]";
	$reply = str_replace("/me", "[b]* ".htmlspecialchars($quote['poster'])."[/b]", $reply);
	die($reply);
}
else if ($action == 'rp') // retrieve post
{

	$rPost = Query("	SELECT 
				p.id, p.date, p.num, p.deleted, p.deletedby, p.reason, p.options, p.mood, p.ip, 
				pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
				u.id as uid, u.name, u.displayname, u.rankset, u.powerlevel, u.title, u.sex, u.picture, u.posts, u.postheader, u.signature, u.signsep, u.lastposttime, u.lastactivity, u.regdate,
				(u.globalblock OR !ISNULL(bl.user)) layoutblocked,
				u2.name AS ru_name, u2.displayname AS ru_dn, u2.powerlevel AS ru_power, u2.sex AS ru_sex,
				u3.name AS du_name, u3.displayname AS du_dn, u3.powerlevel AS du_power, u3.sex AS du_sex,
				f.id fid
			FROM 
				{posts} p 
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision 
				LEFT JOIN {users} u ON u.id = p.user
				LEFT JOIN {blockedlayouts} bl ON bl.user=u.id AND bl.blockee={1}
				LEFT JOIN {users} u2 ON u2.id=pt.user
				LEFT JOIN {users} u3 ON u3.id=p.deletedby
				LEFT JOIN {threads} t ON t.id=p.thread
				LEFT JOIN {forums} f ON f.id=t.forum
			WHERE p.id={0}", $id, $loguserid);
	
	if (!NumRows($rPost))
		die(__("Unknown post ID."));
	$post = Fetch($rPost);

	if (!CanMod($loguserid, $post['fid']))
		die(__("No."));

	die(MakePost($post, isset($_GET['o']) ? POST_DELETED_SNOOP : POST_NORMAL, array('tid'=>$post['thread'], 'fid'=>$post['fid'])));
}
else if($action == "ou")	//Online Users
{
	die(OnlineUsers((int)$_GET['f'], false));
}
else if($action == "tf")	//Theme File
{
	$theme = $_GET['t'];

	$themeFile = "themes/$theme/style.css";
	if(!file_exists($themeFile))
		$themeFile = "themes/$theme/style.php";


function checkForImage(&$image, $external, $file)
{
	global $dataDir, $dataUrl;
	
	if($image) return;
	
	if($external)
	{		
		if(file_exists($dataDir.$file))
			$image = $dataUrl.$file;
	}
	else
	{
		if(file_exists($file))
			$image = $file;
	}
}

	checkForImage($layout_logopic, true, "logos/logo_$theme.png");
	checkForImage($layout_logopic, true, "logos/logo_$theme.jpg");
	checkForImage($layout_logopic, true, "logos/logo_$theme.gif");
	checkForImage($layout_logopic, true, "logos/logo.png");
	checkForImage($layout_logopic, true, "logos/logo.jpg");
	checkForImage($layout_logopic, true, "logos/logo.gif");
	checkForImage($layout_logopic, false, "themes/$theme/logo.png");
	checkForImage($layout_logopic, false, "themes/$theme/logo.jpg");
	checkForImage($layout_logopic, false, "themes/$theme/logo.gif");
	checkForImage($layout_logopic, false, "themes/$theme/logo.png");
	checkForImage($layout_logopic, false, "img/logo.png");

	die($themeFile."|".$layout_logopic);
}
elseif($action == "srl")	//Show Revision List
{
	$qPost = "select currentrevision, thread from {posts} where id={0}";
	$rPost = Query($qPost, $id);
	if(NumRows($rPost))
		$post = Fetch($rPost);
	else
		die(format(__("Unknown post ID #{0}."), $id)." ".$hideTricks);

	$qThread = "select forum from {threads} where id={0}";
	$rThread = Query($qThread, $post['thread']);
	$thread = Fetch($rThread);
	$qForum = "select minpower from {forums} where id={0}";
	$rForum = Query($qForum, $thread['forum']);
	$forum = Fetch($rForum);
	if($forum['minpower'] > $loguser['powerlevel'])
		die(__("No.")." ".$hideTricks);


	$qRevs = "SELECT 
				revision, user AS revuser, date AS revdate,
				u2.name AS ru_name, u2.displayname AS ru_dn, u2.powerlevel AS ru_power, u2.sex AS ru_sex
			FROM 
				{posts_text}
				LEFT JOIN {users} u2 ON u2.id = user
			WHERE pid={0} 
			ORDER BY revision ASC";
	$revs = Query($qRevs, $id);
	
	
	$reply = __("Show revision:")."<br>";
	while($revision = Fetch($revs))
	{
		$reply .= " <a href=\"javascript:void(0)\" onclick=\"showRevision(".$id.",".$revision["revision"].")\">".format(__("rev. {0}"), $revision["revision"])."</a>";

			if ($revision['revuser'])
			{
				$ru_link = UserLink(array('id'=>$revision['revuser'], 'name'=>$revision['ru_name'], 'displayname'=>$revision['ru_dn'], 'powerlevel'=>$revision['ru_power'], 'sex'=>$revision['ru_sex']));
				$revdetail = " ".format(__("by {0} on {1}"), $ru_link, formatdate($revision['revdate']));
			}
			else
				$revdetail = '';
		$reply .= $revdetail;
		$reply .= "<br>";
	}
				
	$hideTricks = " <a href=\"javascript:void(0)\" onclick=\"showRevision(".$id.",".$post["currentrevision"]."); hideTricks(".$id.")\">".__("Back")."</a>";
	$reply .= $hideTricks;
	die($reply);
}
elseif($action == "sr")	//Show Revision
{
	$qPost = "	SELECT 
				p.id, p.date, p.num, p.deleted, p.deletedby, p.reason, p.options, p.mood, p.ip, p.thread,
				pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
				u.id as uid, u.name, u.displayname, u.rankset, u.powerlevel, u.title, u.sex, u.picture, u.posts, u.postheader, u.signature, u.signsep, u.lastposttime, u.lastactivity, u.regdate,
				(u.globalblock OR !ISNULL(bl.user)) layoutblocked,
				u2.name AS ru_name, u2.displayname AS ru_dn, u2.powerlevel AS ru_power, u2.sex AS ru_sex
			FROM 
				{posts} p 
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = ".(int)$_GET['rev']."
				LEFT JOIN {users} u ON u.id = p.user
				LEFT JOIN {blockedlayouts} bl ON bl.user=u.id AND bl.blockee=".$loguserid."
				LEFT JOIN {users} u2 ON IF(p.deleted, u2.id=p.deletedby, u2.id=pt.user)
			WHERE pt.pid=".$id;
			
	$rPost = Query($qPost);
	if(NumRows($rPost))
		$post = Fetch($rPost);
	else
		die(format(__("Unknown post ID #{0} or revision missing."), $id));

	$qThread = "select forum from {threads} where id={0}";
	$rThread = Query($qThread, $post['thread']);
	$thread = Fetch($rThread);
	$qForum = "select minpower from {forums} where id={0}";
	$rForum = Query($qForum, $thread['forum']);
	$forum = Fetch($rForum);
	if($forum['minpower'] > $loguser['powerlevel'])
		die(__("No."));

//	die(var_dump($post));
	die(makePostText($post));
}
elseif($action == "em")	//Email
{
	$blah = FetchResult("select email from {users} where id={0} and showemail=1", $id);
	die(htmlspecialchars($blah));
}
elseif($action == "vc")	//View Counter
{
	$blah = FetchResult("select views from {misc}");
	die(number_format($blah));
}

die(__("Unknown action."));
?>
