<?php
//  AcmlmBoard XD - Post editing page
//  Access: users

$title = __("Edit post");

if(!$loguserid)
	Kill(__("You must be logged in to edit your posts."));

if($loguser['powerlevel'] < 0)
	Kill(__("Banned users can't edit their posts."));
	
$key = hash('sha256', "{$loguserid},{$loguser['pss']},{$salt}");

if(isset($_POST['id']))
	$_GET['id'] = $_POST['id'];

if(!isset($_GET['id']))
	Kill(__("Post ID unspecified."));

$pid = (int)$_GET['id'];
AssertForbidden("editPost", $pid);

$rPost = Query("select {posts}.*, {posts_text}.text from {posts} left join {posts_text} on {posts_text}.pid = {posts}.id and {posts_text}.revision = {posts}.currentrevision where id={0}", $pid);
if(NumRows($rPost))
{
	$post = Fetch($rPost);
	$tid = $post['thread'];
} else
	Kill(__("Unknown post ID."));

$rThread = Query("select * from {threads} where id={0}", $tid);
if(NumRows($rThread))
	$thread = Fetch($rThread);
else
	Kill(__("Unknown thread ID."));
AssertForbidden("viewThread", $tid);

$rFora = Query("select * from {forums} where id={0}", $thread['forum']);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill(__("Unknown forum ID."));
$fid = $forum['id'];
AssertForbidden("viewForum", $fid);

//-- Mark as New if last post is edited --
//print $thread['lastpostdate']."<br/>";
//print $post['date']."<br/>";
$wasLastPost = ($thread['lastpostdate'] == $post['date']);
//print (int)$wasLastPost;

$thread['title'] = htmlspecialchars($thread['title']);
$fid = $thread['forum'];

if((int)$_GET['delete'] == 1)
{
	if ($_GET['key'] != $key) Kill(__("No."));
	if(!CanMod($loguserid,$fid))
		Kill(__("You're not allowed to delete posts."));
	$rPosts = Query("update {posts} set deleted=1,deletedby={0},reason={1} where id={2} limit 1", $loguserid, $_GET['reason'], $pid);
	
	die(header("Location: ".actionLink("thread", $tid)));
} elseif((int)$_GET['delete'] == 2)
{
	if ($_GET['key'] != $key) Kill(__("No."));
	if(!CanMod($loguserid,$fid))
		Kill(__("You're not allowed to undelete posts."));
	$rPosts = Query("update {posts} set deleted=0 where id={0} limit 1", $pid);
	
	die(header("Location: ".actionLink("thread", $tid)));
}

if ($post['deleted'])
	Kill(__("This post has been deleted."));

if(!CanMod($loguserid, $fid) && $post['user'] != $loguserid)
	Kill(__("You are not allowed to edit posts."));

if($thread['closed'] && !CanMod($loguserid, $fid))
	Kill(__("This thread is closed."));
	
MakeCrumbs(array($forum['title']=>actionLink("forum", $fid), actionLink("thread", $tid) => ParseThreadTags($thread['title']), __("Edit post")=>""), $links);

write("
	<script type=\"text/javascript\">
			window.addEventListener(\"load\",  hookUpControls, false);
	</script>
");

if($_POST['text'])
{
	$words = explode(" ", trim($_POST['text']));
	$wordCount = count($words);
	if($wordCount < $minWords)
	{
		$_POST['action'] = "";
		Alert(__("Your post is too short to have any real meaning. Try a little harder."), __("I'm sorry, Dave."));
	}
}

if(!isset($_POST['action']))
{
	$_POST['nopl'] = $post['options'] & 1;
	$_POST['nosm'] = $post['options'] & 2;
	$_POST['nobr'] = $post['options'] & 4;
}

if($_POST['action'] == __("Edit"))
{
	if ($_POST['key'] != $key) Kill(__("No."));
	
	if($_POST['text'])
	{
		$options = 0;
		if($_POST['nopl']) $options |= 1;
		if($_POST['nosm']) $options |= 2;
		if($_POST['nobr']) $options |= 4;

		$rRev = Query("select max(revision) from {posts_text} where pid={0}", $pid);
		$rev = Fetch($rRev);
		$rev = $rev[0]; //note: no longer a fetched row.
		$rev++;
		$rPostsText = Query("insert into {posts_text} (pid,text,revision,user,date) values ({0}, {1}, {2}, {3}, {4})", $pid, $_POST["text"], $rev, $loguserid, time());

		$rPosts = Query("update {posts} set options={0}, mood={1}, currentrevision = currentrevision + 1 where id={2} limit 1", $options, (int)$_POST['mood'], $pid);

		//Update thread lastpostdate if we edited the last post
		if($wasLastPost)
		{
			Query("DELETE FROM {threadsread} WHERE thread={0}", $thread['id']);
		}

		if($forum['minpower'] < 1)
			Report("Post edited by [b]".$loguser['name']."[/] in [b]".$thread['title']."[/] (".$forum['title'].") -> [g]#HERE#?pid=".$pid);

			die(header("Location: ".actionLink("thread", 0, "pid=$pid#$pid")));
		exit();
	}
	else
		Alert(__("Enter a message and try again."), __("Your post is empty."));
}

if($_POST['text'])
{
	$prefill = $_POST['text'];
}

if($_POST['action'] == __("Preview"))
{
	$rUser = Query("select * from {users} where id={0}", $post['user']);
	if(NumRows($rUser))
		$user = Fetch($rUser);
	else
		Kill(__("Unknown user ID."));
	$bucket = "userMangler"; include("./lib/pluginloader.php");

	if($_POST['text'])
	{
		$layoutblocked = $user['globalblock'];
		if ($post['user'] != $loguserid)
			$layoutblocked = $layoutblocked || FetchResult("SELECT COUNT(*) FROM {blockedlayouts} WHERE user={0} AND blockee={1}", $post['user'], $loguserid);
		$previewPost['layoutblocked'] = $layoutblocked;
		
		$previewPost['text'] = $prefill;
		$previewPost['num'] = $post['num'];
		$previewPost['id'] = $pid;
		$previewPost['uid'] = $post['user'];
		$copies = explode(",","title,name,displayname,picture,sex,powerlevel,avatar,postheader,signature,signsep,posts,regdate,lastactivity,lastposttime,rankset");
		foreach($copies as $toCopy)
			$previewPost[$toCopy] = $user[$toCopy];
		$previewPost['options'] = 0;
		if($_POST['nopl']) $previewPost['options'] |= 1;
		if($_POST['nosm']) $previewPost['options'] |= 2;
		if($_POST['nobr']) $previewPost['options'] |= 4;
		$previewPost['mood'] = (int)$_POST['mood'];
		MakePost($previewPost, POST_SAMPLE, array('forcepostnum'=>1, 'metatext'=>__("Preview")));
	}
	else
		Alert(__("Enter a message and try again."), __("Your post is empty."));
}

if(!$_POST['text']) $prefill = $post['text'];
else $prefill = $_POST['text'];

if($_POST['nopl'])
	$nopl = "checked=\"checked\"";
if($_POST['nosm'])
	$nosm = "checked=\"checked\"";
if($_POST['nobr'])
	$nobr = "checked=\"checked\"";

if(!isset($_POST['mood']))
	$_POST['mood'] = $post['mood'];
if($_POST['mood'])
	$moodSelects[(int)$_POST['mood']] = "selected=\"selected\" ";
$moodOptions = Format("<option {0}value=\"0\">".__("[Default avatar]")."</option>\n", $moodSelects[0]);
$rMoods = Query("select mid, name from {moodavatars} where uid={0} order by mid asc", $post['user']);
while($mood = Fetch($rMoods))
	$moodOptions .= Format("<option {0}value=\"{1}\">{2}</option>\n", $moodSelects[$mood['mid']], $mood['mid'], htmlspecialchars($mood['name']));

Write(
"
	<table style=\"width: 100%;\">
		<tr>
			<td style=\"vertical-align: top; border: none;\">
				<form action=\"".actionLink("editpost")."\" method=\"post\">
					<table class=\"outline margin width100\">
						<tr class=\"header1\">
							<th colspan=\"2\">
								".__("Edit Post")."
							</th>
						</tr>
						<tr class=\"cell0\">
							<td>
								".__("Post")."
							</td>
							<td>
								<textarea id=\"text\" name=\"text\" rows=\"16\" style=\"width: 98%;\">{0}</textarea>
							</td>
						</tr>
						<tr class=\"cell2\">
							<td></td>
							<td>
								<input type=\"submit\" name=\"action\" value=\"".__("Edit")."\" /> 
								<input type=\"submit\" name=\"action\" value=\"".__("Preview")."\" />
								<select size=\"1\" name=\"mood\">
									{1}
								</select>
								<label>
									<input type=\"checkbox\" name=\"nopl\" {3} />&nbsp;".__("Disable post layout", 1)."
								</label>
								<label>
									<input type=\"checkbox\" name=\"nosm\" {4} />&nbsp;".__("Disable smilies", 1)."
								</label>
								<label>
									<input type=\"checkbox\" name=\"nobr\" {5} />&nbsp;".__("Disable auto-<br>", 1)."
								</label>
								<input type=\"hidden\" name=\"id\" value=\"{2}\" />
								<input type=\"hidden\" name=\"key\" value=\"{6}\" />
							</td>
						</tr>
					</table>
				</form>
			</td>
			<td style=\"width: 200px; vertical-align: top; border: none;\">
",	htmlspecialchars($prefill), $moodOptions, $pid, $nopl, $nosm, $nobr, $key);

DoSmileyBar();
DoPostHelp();

Write(
"
			</td>
		</tr>
	</table>
");

doThreadPreview($tid);

?>
