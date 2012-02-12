<?php
if($loguserid)
{
	if($_SERVER['HTTPS'] == "on") print "Secure browsing through HTTPS :)<br><br>";
	print "Logged in as ".UserLink($loguser).".<br>";
	print "<ul class=\"pipemenu\">";
	print "<li><a href=\"#\" onclick=\"document.forms[0].submit();\">Log out</a></li>";

	if(IsAllowed("editProfile"))
		print "<li><a href=\"editprofile.php\">".__("Edit profile")."</a></li>";
	if(IsAllowed("viewPM"))
		print "<li><a href=\"private.php\">".__("Private messages")."</a></li>";
	if(IsAllowed("editMoods"))
		print "<li><a href=\"editavatars.php\">".__("Mood avatars")."</a></li>";

	$bucket = "bottomMenu"; include("./lib/pluginloader.php");

	if(!isset($_POST['id']) && isset($_GET['id']))
		$_POST['id'] = (int)$_GET['id'];

	if(strpos($_SERVER['SCRIPT_NAME'], "forum.php"))
		print "<li><a href=\"board.php?fid=".$_POST['id']."&amp;action=markasread\">".__("Mark forum read")."</a></li>";
	elseif(strpos($_SERVER['SCRIPT_NAME'], $boardIndex))
		print "<li><a href=\"board.php?action=markallread\">".__("Mark all forums read")."</a></li>";

}
else
{
	print "<ul class=\"pipemenu\">";
	print "<li><a href=\"register.php\">".__("Register")."</a></li>";
	print "<li><a href=\"login.php\">".__("Log in")."</a></li>";
}
					
?>
