<?php

$title = __("Mood avatars");

$crumbs = new PipeMenu();
$crumbs->add(new PipeMenuLinkEntry(__("Mood avatars"), "editavatars"));
makeBreadcrumbs($crumbs);

AssertForbidden("editMoods");

if(!$loguserid)
	Kill(__("You must be logged in to edit your avatars."));

$userid = $loguserid;

if(isset($_POST['action']))
{
	$mid = (int)$_POST['mid'];
	if($_POST['action'] == __("Rename"))
	{
		Query("update {moodavatars} set name={0} where mid={1} and uid={2}", $_POST['name'], $mid, $loguserid);
		Alert(__("Avatar renamed."), __("Okay"));
	}
	else if($_POST['action'] == __("Delete"))
	{
		Query("delete from {moodavatars} where uid={0} and mid={1}", $loguserid, $mid);
		Query("update {posts} set mood=0 where user={0} and mood={1}", $loguserid, $mid);
		if(file_exists("{$dataDir}avatars/".$loguserid."_".$mid))
			unlink("{$dataDir}avatars/".$loguserid."_".$mid);
		Alert(__("Avatar deleted."), __("Okay"));
	}
	else if($_POST['action'] == __("Add"))
	{
		$highest = FetchResult("select mid from {moodavatars} where uid={0} order by mid desc limit 1", $loguserid);
		if($highest < 1)
			$highest = 1;
		$mid = $highest + 1;

		//Begin copypasta from edituser/editprofile_avatar...
		if($fname = $_FILES['picture']['name'])
		{
			$res = HandlePicture('picture', 0, "avatar", $mid);

			if($res === true)
			{
				if($_POST['name'] == "")
					$_POST['name'] = "#".$mid;

				Query("insert into {moodavatars} (uid, mid, name) values ({0}, {1}, {2})", $loguserid, $mid, $_POST['name']);
			} else
				Kill(__("Could not update your avatar for the following reason(s):")."<ul>".$res."</ul>");
		}
	}
}

$moodRows = "";
$rMoods = Query("select mid, name from {moodavatars} where uid={0} order by mid asc", $loguserid);
while($mood = Fetch($rMoods))
{
	$cellClass = ($cellClass+1) % 2;
	$moodRows .= format(
"
		<tr class=\"cell{0}\">
			<td style=\"width: {$dimx}px;\">
				<img src=\"{$dataDir}avatars/{1}_{2}\" alt=\"\">
			</td>
			<td>
				<form method=\"post\" action=\"".actionLink("editavatars")."\">
					<input type=\"hidden\" name=\"mid\" value=\"{2}\" />
					<input type=\"text\" id=\"name{2}\" name=\"name\" style=\"width: 60%;\" value=\"{3}\" />
					<input type=\"submit\" name=\"action\" value=\"".__("Rename")."\" />
					<input type=\"submit\" name=\"action\" value=\"".__("Delete")."\" />
				</form>
			</td>
		</tr>
",	$cellClass, $loguserid, $mood['mid'], htmlspecialchars($mood['name']));
}

write(
"
	<table class=\"margin outline width50\">
		<tr class=\"header1\">
			<th colspan=\"2\">
				".__("Mood avatars")."
			</th>
		</tr>
		{0}
		<tr class=\"header1\">
			<th colspan=\"2\">
				".__("Add new")."
			</th>
		</tr>
		<tr class=\"cell2\">
			<td>
			</td>
			<td>
				<form method=\"post\" action=\"".actionLink("editavatars")."\" enctype=\"multipart/form-data\">
					<label for=\"newName\">".__("Name:")."</label>
					<input type=\"text\" id=\"newName\" name=\"name\" style=\"width: 60%;\" /><br />

					<label for=\"pic\">".__("Image:")."</label>
					<input type=\"file\" id=\"pic\" name=\"picture\"  style=\"width: 75%;\" />

					<input type=\"submit\" name=\"action\" value=\"".__("Add")."\" />
				</form>
			</td>
	</table>
", $moodRows);

?>
