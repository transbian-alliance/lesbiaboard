<?php
//  AcmlmBoard XD - Posts by user viewer
//  Access: all

AssertForbidden("listPosts");

if(!isset($_GET['id']))
	Kill(__("User ID unspecified."));

$id = (int)$_GET['id'];

$qUser = "select * from users where id=".$id;
$rUser = Query($qUser);
if(NumRows($rUser))
	$user = Fetch($rUser);
else
	Kill(__("Unknown user ID."));
$bucket = "userMangler"; include("./lib/pluginloader.php");

$title = __("Post list");

$total = $user['posts'];
$ppp = $loguser['postsperpage'];
if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

if(!$ppp) $ppp = 25;

$minpower = $loguser['powerlevel'];
if($minpower < 0)
	$minpower = 0;

$qPosts = "	SELECT 
				p.thread, p.id, p.date, p.num, p.deleted, p.options, p.mood, p.ip, 
				pt.text, pt.revision, 
				u.id as uid, u.name, u.displayname, u.rankset, u.powerlevel, u.title, u.sex, u.picture, u.posts, u.postheader, u.signature, u.signsep, u.lastposttime, u.lastactivity, u.regdate,
				(u.globalblock OR !ISNULL(bl.user)) layoutblocked,
				t.id thread, t.title threadname,
				f.id fid
			FROM 
				posts p 
				LEFT JOIN posts_text pt ON pt.pid = p.id AND pt.revision = p.currentrevision
				LEFT JOIN users u ON u.id = p.user
				LEFT JOIN blockedlayouts bl ON bl.user=u.id AND bl.blockee=".$loguserid."
				LEFT JOIN threads t ON t.id=p.thread
				LEFT JOIN forums f ON f.id=t.forum
				LEFT JOIN categories c ON c.id=f.catid
			WHERE u.id=".$id." AND f.minpower <= ".$minpower."
			ORDER BY date ASC LIMIT ".$from.", ".$ppp;

$rPosts = Query($qPosts);
$numonpage = NumRows($rPosts);

MakeCrumbs(array(__("Main")=>"./", $user['name']=>actionLink("profile", $id), __("List of posts")=>""), $links);

// TODO: use a function for page links, consistent pagelinking needed
// (some places use compact pagelinking while this is still using Acmlmboard style pagelinking)


$pagelinks = PageLinks(actionLink("listposts", $id, "from="), $ppp, $from, $total);

if($pagelinks)
	write("<div class=\"smallFonts pages\">".__("Pages:")." {0}</div>", $pagelinks);

if(NumRows($rPosts))
{
	while($post = Fetch($rPosts))
		MakePost($post, POST_NORMAL, array('threadlink'=>1, 'tid'=>$post['thread'], 'fid'=>$post['fid'], 'noreplylinks'=>1));
}

if($pagelinks)
	write("<div class=\"smallFonts pages\">".__("Pages:")." {0}</div>", $pagelinks);

?>
