<?php
$navigation = new PipeMenu();

if($loguser['powerlevel'] >= 3 && isAllowed("viewAdminRoom"))
	$navigation->add(new PipeMenuLinkEntry(__("Admin"), "admin"));

$bucket = "topMenuStart"; include("./lib/pluginloader.php");

$navigation->add(new PipeMenuLinkEntry(__("Main"), "index"));
$navigation->add(new PipeMenuLinkEntry(__("FAQ"), "faq"));

if(isAllowed("viewMembers"))
	$navigation->add(new PipeMenuLinkEntry(__("Member list"), "memberlist"));
if(isAllowed("viewRanks"))
	$navigation->add(new PipeMenuLinkEntry(__("Ranks"), "ranks"));
if(isAllowed("viewAvatars"))
	$navigation->add(new PipeMenuLinkEntry(__("Avatars"), "avatarlibrary"));
if(isAllowed("viewOnline"))
	$navigation->add(new PipeMenuLinkEntry(__("Online users"), "online"));
if(isAllowed("search"))
	$navigation->add(new PipeMenuLinkEntry(__("Search"), "search"));

$navigation->add(new PipeMenuLinkEntry(__("Last posts"), "lastposts"));

$bucket = "topMenu"; include("./lib/pluginloader.php");

print $navigation->build();
?>
