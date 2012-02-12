<?php

require('lib/common.php');

//TODO Place this in an appropiate place
function getOnlineUsersText()
{
	global $OnlineUsersFid;
	
	$refreshCode = "";

	if(!isset($OnlineUsersFid))
		$OnlineUsersFid = 0;
		
	if(!$noAjax)
	{
		$refreshCode = format(
	"
		<script type=\"text/javascript\">
			onlineFID = {0};
			window.addEventListener(\"load\",  startOnlineUsers, false);
		</script>
	", $OnlineUsersFid);
	}

	$onlineUsers = OnlineUsers($OnlineUsersFid);

	return "<span id=\"onlineUsers\">
			$onlineUsers
		</span> $refreshCode";
}

function getBirthdaysText()
{
	// Mega-Mario: could be optimized to
	// $rBirthdays = Query("select birthday, id, name, displayname, powerlevel, sex from users where birthday>0 and from_unixtime(birthday, '%c-%e')='".date('n-j')."' order by name");
	// but then I don't know about birthday timezones and all
	// and especially why we're using gmdate()
	$rBirthdays = Query("select birthday, id, name, displayname, powerlevel, sex from users where birthday > 0 order by name");
	$birthdays = array();
	while($user = Fetch($rBirthdays))
	{
		$bucket = "userMangler"; include("./lib/pluginloader.php");
		$b = $user['birthday'];
		if(gmdate("m-d", $b) == gmdate("m-d"))
		{
			$y = gmdate("Y") - gmdate("Y", $b);
			$birthdays[] = UserLink($user)." (".$y.")";
		}
	}
	if(count($birthdays))
		$birthdaysToday = implode(", ", $birthdays);
	if($birthdaysToday)
		return "<br>".__("Birthdays today:")." ".$birthdaysToday;
	else
		return "";
}

//=======================
// Do the page

if(!isset($_GET["page"]))
	$_GET["page"] = "index";
if(!ctype_alnum($_GET["page"]))
	$_GET["page"] = "index";

ob_start();
$layout_crumbs = "";
try {
	require('pages/'.$_GET["page"].'.php');
}
catch(KillException $e) {}

if($ajaxPage)
{
	ob_end_flush();
	die();
}

$layout_contents = ob_get_contents();
ob_end_clean();

//Do this only if it's not an ajax page.
include("lib/views.php");

//=======================
// Panels and footer

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


//=======================
// Notification bars

ob_start();

$bucket = "userBar"; include("./lib/pluginloader.php");
/*
if($rssBar)
{
	write("
	<div style=\"float: left; width: {1}px;\">&nbsp;</div>
	<div id=\"rss\">
		{0}
	</div>
", $rssBar, $rssWidth + 4);
}*/
DoPrivateMessageBar();
$bucket = "topBar"; include("./lib/pluginloader.php");
$layout_bars = ob_get_contents();
ob_end_clean();


//=======================
// Misc stuff

$layout_time = cdate($dateformat);
$layout_onlineusers = getOnlineUsersText();
$layout_birthdays = getBirthdaysText();
$layout_views = '<span id="viewCount">'. __("Views:")." ".number_format($misc['views']).'</span>';
$layout_title = "Hello World";


//=======================
// Board logo

if(file_exists("themes/$theme/logo.png"))
	$layout_logopic = themeResourceLink("logo.png");
else if(file_exists("themes/$theme/logo.jpg"))
	$layout_logopic = themeResourceLink("logo.jpg");
else if(file_exists("themes/$theme/logo.gif"))
	$layout_logopic = themeResourceLink("logo.gif");
else
	$layout_logopic = resourceLink("img/logo.png");


//=======================
// Print everything!

require("layout.php");


?>

