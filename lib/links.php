<?php

function getRefreshActionLink()
{
	$args = "ajax=1";
	
	if(isset($_GET["from"]))
		$args .= "&from=".$_GET["from"];
	
	return actionLink((isset($_GET["page"]) ? $_GET['page'] : 0), (isset($_GET['id']) ? $_GET["id"] : 0), $args);
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
function actionLink($action, $id="", $args="")
{
	global $boardroot, $mainPage;
	if($boardroot == "")
		$boardroot = "./";

	$bucket = "linkMangler"; include('lib/pluginloader.php');

	$res = "";
	
	if($action != $mainPage)
		$res .= "&page=$action";
	
	if($id != "")
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

$powerlevels = array(-1 => " [".__("banned")."]", 0 => "", 1 => " [".__("local mod")."]", 2 => " [".__("full mod")."]", 3 => " [".__("admin")."]", 4 => " [".__("root")."]", 5 => " [".__("system")."]");

function userLink($user, $showMinipic = false)
{
	global $dataUrl, $dataDir, $powerlevels;

	$bucket = "userMangler"; include("./lib/pluginloader.php");

	$fpow = $user['powerlevel'];
	$fsex = $user['sex'];
	$fname = ($user['displayname'] ? $user['displayname'] : $user['name']);
	$fname = htmlspecialchars($fname);
	$fname = str_replace(" ", "&nbsp;", $fname);
	$textname = $fname;
	
	$minipic = "";
	if($showMinipic || Settings::get("alwaysMinipic"))
	{
		if($user["minipic"] == "#INTERNAL#")
			$minipic = "<img src=\"${dataUrl}minipics/${user["id"]}\" alt=\"\" class=\"minipic\" />&nbsp;";
		else if($user["minipic"])
			$minipic = "<img src=\"".$user['minipic']."\" alt=\"\" class=\"minipic\" />&nbsp;";
	}
	
	$fname = $minipic.$fname;
	
	if($fpow < 0) $fpow = -1;
	$classing = " class=\"nc" . $fsex . (($fpow < 0) ? "x" : $fpow)."\"";

/*
	if($hacks['alwayssamepower'])
		$fpow = $hacks['alwayssamepower'] - 1;
	if($hacks['alwayssamesex'])
		$fsex = $hacks['alwayssamesex'];

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
	*/
	
	$bucket = "userLink"; include('lib/pluginloader.php');
	$title = $textname . " (".$user["id"].") ".$powerlevels[$user['powerlevel']];
	$userlink = actionLinkTag("<span$classing title=\"$title\">$fname</span>", "profile", $user["id"]);
	return $userlink;
}

function userLinkById($id)
{
	global $userlinkCache;
	
	if(!isset($userlinkCache[$id]))
	{
		$rUser = Query("SELECT u.(_userfields) FROM {users} u WHERE u.id={0}", $id);
		if(NumRows($rUser))
			$userlinkCache[$id] = getDataPrefix(Fetch($rUser), "u_");
		else
			$userlinkCache[$id] = array('id' => 0, 'name' => "Unknown User", 'sex' => 0, 'powerlevel' => -1);
	}
	return UserLink($userlinkCache[$id]);
}

function pageLinks($url, $epp, $from, $total)
{
	$url = htmlspecialchars($url);
	
	$numPages = ceil($total / $epp);
	$page = ceil($from / $epp) + 1;

	$first = ($from) ? "<a class=\"pagelink\" href=\"".$url."0\">&#x00AB;</a> " : "";
	$prev = ($from) ? "<a class=\"pagelink\"  href=\"".$url.($from - $epp)."\">&#x2039;</a> " : "";
	$next = ($from < $total - $epp) ? " <a class=\"pagelink\"  href=\"".$url.($from + $epp)."\">&#x203A;</a>" : "";
	$last = ($from < $total - $epp) ? " <a class=\"pagelink\"  href=\"".$url.(($numPages * $epp) - $epp)."\">&#x00BB;</a>" : "";

	$pageLinks = array();
	for($p = $page - 5; $p < $page + 10; $p++)
	{
		if($p < 1 || $p > $numPages)
			continue;
		if($p == $page || ($from == 0 && $p == 1))
			$pageLinks[] = "<span class=\"pagelink\">$p</span>";
		else
			$pageLinks[] = "<a class=\"pagelink\"  href=\"".$url.(($p-1) * $epp)."\">".$p."</a>";
	}
	
	return $first.$prev.join(array_slice($pageLinks, 0, 11), "").$next.$last;
}

function absoluteActionLink($action, $id=0, $args="")
{
    return ($https?"https":"http") . "://" . $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).substr(actionLink($action, $id, $args), 1);
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

function getFullURL()
{
	return getFullRequestedURL();
}

?>
