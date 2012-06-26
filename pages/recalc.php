<?php
//  AcmlmBoard XD - Report/content mismatch fixing utility
//  Access: staff

AssertForbidden("recalculate");

if($loguser['powerlevel'] < 1)
		Kill(__("Staff only, please."));
MakeCrumbs(array(__("Admin") => actionLink("admin"), __("Recalculate statistics") => actionLink("recalc")), "");

print "<table class=\"outline margin width50\">";

print "<tr class=\"header1\"><th>".__("Name")."</th><th>".__("Actual")."</th><th>".__("Reported")."</th><th>&nbsp;</th></tr>";

print "<tr class=\"header0\"><th colspan=\"4\">".__("Counting user's posts&hellip;")."</th></tr>";
$rUsers = Query("select * from {users}");
while($user = Fetch($rUsers))
{
	$cellClass = ($cellClass+1) % 2;
	print "<tr class=\"cell".$cellClass."\">";
	print "<td>".htmlspecialchars($user['name'])."</td>";
	$posts = FetchResult("select count(*) from {posts} where user={0}", $user['id']);
	print "<td>".$posts."</td><td>".$user['posts']."</td>";
	print "<td style=\"background: ".($posts==$user['posts'] ? "green" : "red").";\"></td>";
	print "</tr>";

	$rUser = Query("update {users} set posts={0} where id={1} limit 1", $posts, $user['id']);
	RecalculateKarma($user['id']);
}

print "<tr class=\"header0\"><th colspan=\"4\">".__("Counting thread replies&hellip;")."</th></tr>";
$rThreads = Query("select * from {threads}");
while($thread = Fetch($rThreads))
{
	$thread['title'] = htmlspecialchars($thread['title']);
	$cellClass = ($cellClass+1) % 2;
	print "<tr class=\"cell".$cellClass."\">";
	print "<td>".$thread['title']."</td>";
	$posts = FetchResult("select count(*) from {posts} where thread={0}", $thread['id']);
	print "<td>".($posts-1)."</td><td>".$thread['replies']."</td>";
	print "<td style=\"background: ".($posts-1==$thread['replies'] ? "green" : "red").";\"></td>";
	print "</tr>";

	$rThread = Query("update {threads} set replies={0} where id={1} limit 1", ($posts-1), $thread['id']);
}

print "<tr class=\"header0\"><th colspan=\"4\">".__("Counting forum threads and posts&hellip;")."</th></tr>";
$rFora = Query("select * from {forums}");
while($forum = Fetch($rFora))
{
	$cellClass = ($cellClass+1) % 2;
	print "<tr class=\"cell".$cellClass."\">";
	print "<td>".$forum['title']."</td>";
	$rThreads = Query("select * from {threads} where forum={0}", $forum['id']);
	$threads = NumRows($rThreads);

	$postcount = 0;
	while($thread = Fetch($rThreads))
	{
		$posts = FetchResult("select count(*) from {posts} where thread={0}", $thread['id']);
		$postcount += $posts;
	}
	print "<td>".$threads." / ".$postcount."</td><td>".$forum['numthreads']." / ".$forum['numposts']."</td>";
	print "<td style=\"background: ".($threads==$forum['numthreads'] && $postcount==$forum['numposts'] ? "green" : "red").";\"></td>";
	print "</tr>";

	$rForum = Query("update {forums} set numposts={0}, numthreads={1} where id={2} limit 1", $postcount, $threads, $forum['id']);
}

print "<tr class=\"header0\"><th colspan=\"4\">".__("All counters reset.")."</th></tr>";
print "</table>";




$rForum = Query("select * from {forums}");
while($forum = Fetch($rForum))
{
	print $forum['title']."<br/>";
	$rThread = Query("select * from {threads} where forum = {0} order by lastpostdate desc", $forum['id']);
	$first = 1;
	while($thread = Fetch($rThread))
	{
		print "&raquo; ".htmlspecialchars($thread['title'])."<br/>";
		$lastPost = Fetch(Query("select * from {posts} where thread = {0} order by date desc limit 0,1"), $thread['id']);
		print "&raquo; &raquo; Last post ID is ".$lastPost['id']." by user #".$lastPost['user']."<br/>";
		Query("update {threads} set lastpostid = {0}, lastposter = {1}, lastpostdate = {2} where id = {3}", (int)$lastPost['id'], (int)$lastPost['user'], (int)$lastPost['date'], $thread['id']);
		if($first)
			Query("update {forums} set lastpostid = {0}, lastpostuser = {1}, lastpostdate = {2} where id = {3}", (int)$lastPost['id'], (int)$lastPost['user'], (int)$lastPost['date'], $forum['id']);
		$first = 0;
	}
}

?>
