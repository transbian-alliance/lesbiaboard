<?php
if($loguserid)
{
	if($_SERVER['HTTPS'] == "on") print "Secure browsing through HTTPS :)<br><br>";
	print "<li>Logged in as ".UserLink($loguser)."</li>";
	print "<li><a href=\"#\" onclick=\"document.forms[0].submit();\">Log out</a></li>";

	if(IsAllowed("editProfile"))
		print actionLinkTagItem(__("Edit profile"), "editprofile");
	if(IsAllowed("viewPM"))
		print actionLinkTagItem(__("Private messages"), "private");
	if(IsAllowed("editMoods"))
		print actionLinkTagItem(__("Mood avatars"), "editavatars");

	$bucket = "bottomMenu"; include("./lib/pluginloader.php");

	if(!isset($_POST['id']) && isset($_GET['id']))
		$_POST['id'] = (int)$_GET['id'];

	//TODO FIX
	if(strpos($_SERVER['SCRIPT_NAME'], "forum.php"))
		print actionLinkTagItem(__("Mark forum read"), "index", 0, "id=".$_POST['id']."&amp;action=markasread");
	elseif(strpos($_SERVER['SCRIPT_NAME'], $boardIndex))
		print actionLinkTagItem(__("Mark all forums read"), "index", 0, "action=markallread");
}
else
{
	print actionLinkTagItem(__("Register"), "register");
	print actionLinkTagItem(__("Log in"), "login");
}

					
?>
