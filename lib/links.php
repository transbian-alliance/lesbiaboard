<?php

function getRefreshActionLink()
{
	$args = "ajax=1";
	
	if(isset($_GET["from"]))
		$args .= "&from=".$_GET["from"];
	
	return actionLink($_GET["page"], $_GET["id"], $args);
}

function printRefreshCode()
{
	if(Settings::get("ajax"))
		write(
	"
		<script type=\"text/javascript\">
			refreshUrl = ".json_encode(getRefreshActionLink()).";
			window.addEventListener(\"load\",  startPageUpdate, false);
		</script>
	");
}
function actionLink($action, $id=0, $args="")
{
	global $boardroot;
	if($boardroot == "")
		$boardroot = "./";

	$bucket = "linkMangler"; include('lib/pluginloader.php');

	$res = "";
	
	if($action != "index")
		$res .= "&page=$action";
	
	if($id)
		$res .= "&id=$id";
	if($args)
		$res .= "&$args";

	if(strpos($res, "&amp"))
	{
		debug_print_backtrace();
		Kill("Found &amp;amp; in link");
	}
			 
	if($res == "")
		return $boardroot;
	else
		return $boardroot."?".substr($res, 1);

//Possible URL Rewriting :D
//	return "$boardroot/$action/$id?$args";
	
}

function actionLinkTag($text, $action, $id=0, $args="")
{
	return '<a href="'.htmlentities(actionLink($action, $id, $args)).'">'.$text.'</a>';
}
function actionLinkTagItem($text, $action, $id=0, $args="")
{
	return '<li><a href="'.htmlentities(actionLink($action, $id, $args)).'">'.$text.'</a></li>';
}

function actionLinkTagConfirm($text, $prompt, $action, $id=0, $args="")
{
	return '<a onclick="return confirm(\''.$prompt.'\'); " href="'.htmlentities(actionLink($action, $id, $args)).'">'.$text.'</a>';
}
function actionLinkTagItemConfirm($text, $prompt, $action, $id=0, $args="")
{
	return '<li><a onclick="return confirm(\''.$prompt.'\'); " href="'.htmlentities(actionLink($action, $id, $args)).'">'.$text.'</a></li>';
}

function resourceLink($what)
{
	global $boardroot;
	return "$boardroot$what";
}

function themeResourceLink($what)
{
	global $theme, $boardroot;
	return $boardroot."themes/$theme/$what";
}

function UserLink($user, $field = "id")
{
	global $hacks;

	$fpow = $user['powerlevel'];
	$fsex = $user['sex'];
	$fname = ($user['displayname'] ? $user['displayname'] : $user['name']);
	$fname = htmlspecialchars($fname);
	if($fpow < 0) $fpow = -1;

	if($hacks['alwayssamepower'])
		$fpow = $hacks['alwayssamepower'] - 1;
	if($hacks['alwayssamesex'])
		$fsex = $hacks['alwayssamesex'];

	$classing = " class=\"nc" . $fsex . (($fpow < 0) ? "x" : $fpow)."\"";

	if($hacks['themenames'] == 1)
	{
		global $lastJokeNameColor;
		$classing = " style=\"color: ";
		if($lastJokeNameColor % 2 == 1)
			$classing .= "#E16D6D; \"";
		else
			$classing .= "#44D04B; \"";
		if($fpow == -1)
			$classing = " class=\"nc0x\"";
		$lastJokeNameColor++;
	} else if($hacks['themenames'] == 2 && $fpow > -1)
	{
		$classing = " style =\"color: #".GetRainbowColor()."\"";
	} else if($hacks['themenames'] == 3)
	{
		if($fpow > 2)
		{
			$fname = "Administration";
			$classing = " class=\"nc23\"";
		} else if($fpow == -1)
		{
			$fname = "Idiot";
			$classing = " class=\"nc2x\"";
		} else
		{
			$fname = "Anonymous";
			$classing = " class=\"nc22\"";
		}
	}
	
	$levels = array(-1 => " [".__("banned")."]", 0 => "", 1 => " [".__("local mod")."]", 2 => " [".__("full mod")."]", 3 => " [".__("admin")."]", 4 => " [".__("root")."]", 5 => " [".__("system")."]");
	
	$bucket = "userLink"; include('lib/pluginloader.php');
	
	$userlink = format("<a href=\"".actionLink("profile", "{0}")."\"><span{1} title=\"{3} ({0}){4}\">{2}</span></a>", $user[$field], $classing, $fname, str_replace(" ", "&nbsp;", htmlspecialchars($user['name'])), $levels[$user['powerlevel']]);
	return $userlink;
}

function PageLinks($url, $epp, $from, $total)
{
	$numPages = ceil($total / $epp);
	$page = ceil($from / $epp) + 1;

	$first = ($from) ? "<a href=\"".$url."0\">&#x00AB;</a> " : "";
	$prev = ($from) ? "<a href=\"".$url.($from - $epp)."\">&#x2039;</a> " : "";
	$next = ($from < $total - $epp) ? " <a href=\"".$url.($from + $epp)."\">&#x203A;</a>" : "";
	$last = ($from < $total - $epp) ? " <a href=\"".$url.(($numPages * $epp) - $epp)."\">&#x00BB;</a>" : "";

	$pageLinks = array();
	for($p = $page - 5; $p < $page + 10; $p++)
	{
		if($p < 1 || $p > $numPages)
			continue;
		if($p == $page || ($from == 0 && $p == 1))
			$pageLinks[] = $p;
		else
			$pageLinks[] = "<a href=\"".$url.(($p-1) * $epp)."\">".$p."</a>";
	}
	
	return $first.$prev.join(array_slice($pageLinks, 0, 11), " ").$next.$last;
}

function getRequestedURL()
{
    return $_SERVER['REQUEST_URI'];
}

function getServerURL($https = false)
{
    return ($https?"https":"http") . "://" . $_SERVER['SERVER_NAME'] . "/";
}

function getFullRequestedURL($https = false)
{
    return getServerURL($https) . $_SERVER['REQUEST_URI'];
}

function GetFullURL()
{
	return getFullRequestedURL();
}

?>
