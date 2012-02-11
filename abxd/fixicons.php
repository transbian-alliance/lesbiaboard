<?php
include("lib/common.php");
$ticons = Query("SELECT id, icon FROM threads WHERE icon like '%.gif'");
while($ticon = Fetch($ticons))
{
	if(preg_match("/img\/icons\/icon[0-9].gif/", $ticon['icon']))
		Query("UPDATE threads SET icon = '".str_replace(".gif", ".png", $ticon['icon'])."' WHERE id = ".$ticon['id']);
}
?>