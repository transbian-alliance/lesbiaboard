<?php


$hours = 72;

$rPosts = Query("select 
	{posts}.id, {posts}.date, 
	{users}.id as uid, {users}.name, {users}.displayname, {users}.powerlevel, {users}.sex, 
	{threads}.title as ttit, {threads}.id as tid, 
	{forums}.title as ftit, {forums}.id as fid
	from {posts} 
	left join {users} on {users}.id = {posts}.user 
	left join {threads} on {threads}.id = {posts}.thread 
	left join {forums} on {threads}.forum = {forums}.id
	where {forums}.minpower <= {0} and {posts}.date >= {1} 
	order by date desc limit 0, 100", $loguser['powerlevel'], (time() - ($hours * 60*60)));

while($post = Fetch($rPosts))
{
	$thread = array();
	$thread["title"] = $post["ttit"];
	$thread["id"] = $post["tid"];

	$c = ($c+1) % 2;
	$theList .= format(
"
	<tr class=\"cell{5}\">
		<td>
			{3}
		</td>
		<td>
			{4}
		</td>
		<td>
			{2}
		</td>
		<td>
			{1}
		</td>
		<td>
			&raquo; ".actionLinkTag("{0}", "thread", "", "pid={0}#{0}")."
		</td>
	</tr>
", $post['id'], formatdate($post['date']), UserLink($post, "uid"), actionLinkTag($post["ftit"], "forum", $post["fid"]), makeThreadLink($thread), $c);
}

if($theList == "")
	$theList = format(
"
	<tr class=\"cell1\">
		<td colspan=\"5\" style=\"text-align: center\">
			".__("Nothing has been posted in the last {0}.")."
		</td>
	</tr>
", Plural($hours, __("hour")));

write(
"
<table class=\"margin outline\">
	<tr class=\"header0\">
		<th colspan=\"5\">".__("Last posts")."</th>
	</tr>
	<tr class=\"header1\">
		<th>".__("Forum")."</th>
		<th>".__("Thread")."</th>
		<th>".__("User")."</th>
		<th>".__("Date")."</th>
		<th></th>
	</tr>
	{0}
</table>
", $theList);

?>
