<?php

require('lib/common.php');

ob_start();
require('navigation.php');
$layout_navigation = ob_get_contents();
ob_end_clean();

ob_start();
require('userpanel.php');
$layout_userpanel = ob_get_contents();
ob_end_clean();

ob_start();
require('footer.php');
$layout_footer = ob_get_contents();
ob_end_clean();


if(!isset($_GET["action"]))
	$_GET["action"] = "index";
if(!ctype_alnum($_GET["action"]))
	$_GET["action"] = "index";
	
ob_start();
require('pages/'.$_GET["action"].'.php');
$layout_contents = ob_get_contents();
ob_end_clean();

$layout_time = "TIME!!";
$layout_onlineusers = "Online Users: LOL";
$layout_birthdays = "";
$layout_views = "Views: 123,456,789";
$layout_title = "Hello World";

if(file_exists("themes/$theme/logo.png"))
	$layout_logopic = themeResourceLink("logo.png");
else if(file_exists("themes/$theme/logo.jpg"))
	$layout_logopic = themeResourceLink("logo.jpg");
else if(file_exists("themes/$theme/logo.gif"))
	$layout_logopic = themeResourceLink("logo.gif");
else
	$layout_logopic = resourceLink("img/logo.png");

require("layout.php");
?>
