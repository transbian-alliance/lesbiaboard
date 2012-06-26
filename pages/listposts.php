<?php
//  AcmlmBoard XD - Posts by user viewer
//  Access: all

AssertForbidden("listPosts");

if(!isset($_GET['id']))
	Kill(__("User ID unspecified."));

$id = (int)$_GET['id'];

$rUser = Query("select * from {users} where id={0}", $id);
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

$rPosts = Query("	SELECT 
				p.thread, p.id, p.date, p.num, p.deleted, p.deletedby, p.reason, p.options, p.mood, p.ip, 
				pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
				u.id as uid, u.name, u.displayname, u.rankset, u.powerlevel, u.title, u.sex, u.picture, u.posts, u.postheader, u.signature, u.signsep, u.lastposttime, u.lastactivity, u.regdate,
				(u.globalblock OR !ISNULL(bl.user)) layoutblocked,
				u2.name AS ru_name, u2.displayname AS ru_dn, u2.powerlevel AS ru_power, u2.sex AS ru_sex,
				t.id thread, t.title threadname,
				f.id fid
			FROM 
				{posts} p 
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
				LEFT JOIN {users} u ON u.id = p.user
				LEFT JOIN {blockedlayouts} bl ON bl.user=u.id AND bl.blockee={0}
				LEFT JOIN {users} u2 ON IF(p.deleted, u2.id=p.deletedby, u2.id=pt.user)
				LEFT JOIN {threads} t ON t.id=p.thread
				LEFT JOIN {forums} f ON f.id=t.forum
				LEFT JOIN {categories} c ON c.id=f.catid
			WHERE u.id={1} AND f.minpower <= {2}
			ORDER BY date ASC LIMIT {3}, {4}", $loguserid, $id, $minpower, $from, $ppp);
$numonpage = NumRows($rPosts);

$uname = $user["name"];
if($user["displayname"])
	$uname = $user["displayname"];
	
MakeCrumbs(array(__("Member list")=>actionLink("memberlist"), $uname => actionLink("profile", $id), __("List of posts")=>""), $links);

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
