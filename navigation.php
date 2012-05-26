
<?php
	if($loguser['powerlevel'] > 2 && IsAllowed("viewAdminRoom"))
		print actionLinkTagItem(__("Admin"), "admin");

	$bucket = "topMenuStart"; include("./lib/pluginloader.php");

	print actionLinkTagItem(__("Main"), "index");
	print actionLinkTagItem(__("FAQ"), "faq");

	if(IsAllowed("viewMembers"))
		print actionLinkTagItem(__("Member list"), "memberlist");
	if(IsAllowed("viewRanks"))
		print actionLinkTagItem(__("Ranks"), "ranks");
	if(IsAllowed("viewAvatars"))
		print actionLinkTagItem(__("Avatars"), "avatarlibrary");
	if(IsAllowed("viewOnline"))
		print actionLinkTagItem(__("Online users"), "online");
	if(IsAllowed("search"))
		print actionLinkTagItem(__("Search"), "search");

	print actionLinkTagItem(__("Last posts"), "lastposts");

	$bucket = "topMenu"; include("./lib/pluginloader.php");
?>

