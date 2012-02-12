<?php
include("lib/common.php");
$posts = Query("select id from posts");
while($post = Fetch($posts))
{
	$rev = FetchResult("select revision from posts_text where pid=".$post['id']." order by revision desc limit 1", 0, 0);
	Query("update posts set currentrevision = ".$rev." where id=".$post['id']);
}
?>