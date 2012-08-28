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

$title = __("Post list");

$minpower = $loguser['powerlevel'];
if($minpower < 0)
	$minpower = 0;
	

$total = FetchResult("
			SELECT 
				count(p.id)
			FROM 
				{posts} p 
				LEFT JOIN {threads} t ON t.id=p.thread
				LEFT JOIN {forums} f ON f.id=t.forum
			WHERE p.user={0} AND f.minpower <= {1}", 
		$id, $minpower);


$ppp = $loguser['postsperpage'];
if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

if(!$ppp) $ppp = 25;


$rPosts = Query("	SELECT 
				p.*,
				pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
				u.(_userfields), u.(rankset,title,picture,posts,postheader,signature,signsep,lastposttime,lastactivity,regdate,globalblock),
				ru.(_userfields),
				du.(_userfields),
				t.id thread, t.title threadname,
				f.id fid
			FROM 
				{posts} p 
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
				LEFT JOIN {users} u ON u.id = p.user
				LEFT JOIN {users} ru ON ru.id=pt.user
				LEFT JOIN {users} du ON du.id=p.deletedby
				LEFT JOIN {threads} t ON t.id=p.thread
				LEFT JOIN {forums} f ON f.id=t.forum
				LEFT JOIN {categories} c ON c.id=f.catid
			WHERE u.id={1} AND f.minpower <= {2}
			ORDER BY date ASC LIMIT {3u}, {4u}", $loguserid, $id, $minpower, $from, $ppp);

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
