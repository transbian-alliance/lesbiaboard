<?php
// AcmlmBoard XD support - Main hub
// Leaving this error in until Niko's new installer is there to whine about it instead. -- Kawa

if(ini_get('register_globals'))
	die("<p>PHP, as it is running on this server, has the <code>register_globals</code> setting turned on. This is something of a security hazard, and is a <a href=\"http://en.wikipedia.org/wiki/Deprecation\" target=\"_blank\">deprecated function</a>. For more information on this topic, please refer to the <a href=\"http://php.net/manual/en/security.globals.php\" target=\"_blank\">PHP manual</a>.</p><p>At any rate, the ABXD messageboard software is designed to run with <code>register_globals</code> turned <em>off</em>. If your provider allows the use of <code>.htaccess</code> files, you can try adding the line <code>php_flag register_globals off</code> to an <code>.htaccess</code> file in your board's root directory, though we suggest placing it on your website root directory (often something like <code>public_html</code>). If not, ask your provider to edit <code>php.ini</code> accordingly and make the internet a little safer for all of us.</p>");

// I can't believe there are PRODUCTION servers that have E_NOTICE turned on. What are they THINKING? -- Kawa
error_reporting(E_ALL ^ E_NOTICE | E_STRICT);

if(!is_file("lib/database.php"))
	die(header("Location: install.html"));

// Deslash GPC variables if we have magic quotes on
if (get_magic_quotes_gpc())
{
	function AutoDeslash($val)
	{
		if (is_array($val))
			return array_map('AutoDeslash', $val);
		else if (is_string($val))
			return stripslashes($val);
		else
			return $val;
	}
	
	$_REQUEST = array_map('AutoDeslash', $_REQUEST);
	$_GET = array_map('AutoDeslash', $_GET);
	$_POST = array_map('AutoDeslash', $_POST);
	$_COOKIE = array_map('AutoDeslash', $_COOKIE);
}

include("salt.php");

include("settingsfile.php");
include("links.php");

include("mysql.php");
include("mysqlfunctions.php");
include("settingssystem.php");
Settings::load();
Settings::checkPlugin("main");
include("feedback.php");
include("language.php");
include("snippets.php");

class KillException extends Exception { }
date_default_timezone_set("GMT");
$timeStart = usectime();

if(!isset($title))
	$title = "";

//HAX below ~Dirbaio
//TODO: CLEAN IT!
//$userSelectSU = "su.id suid, su.name suname, su.displayname sudisplayname, su.powerlevel supowerlevel, su.sex susex, su.birthday subirthday";
$userSelect = "id, name, displayname, powerlevel, sex, birthday";
$userSelectSU = "su.id suid, su.name suname, su.displayname sudisplayname, su.powerlevel supowerlevel, su.sex susex, su.birthday subirthday";
$userSelectLU = "lu.id luid, lu.name luname, lu.displayname ludisplayname, lu.powerlevel lupowerlevel, lu.sex lusex, lu.birthday lubirthday";
$userSelectUsers = "users.id as uid, users.name, users.displayname, users.powerlevel, users.sex, users.birthday";


function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
function UserStructure($row, $prefix)
{
	$result = array();
	
	foreach($row as $key=>$value)
	{
		if(startsWith($key, $prefix))
			$result[substr($key, strlen($prefix))] = $value;
	}
	
	return $result;
}

//END DIRBAIO'S HAX

//WARNING: These things need to be kept in a certain order of execution.


$thisURL = $_SERVER['SCRIPT_NAME'];
if($q = $_SERVER['QUERY_STRING'])
	$thisURL .= "?$q";

include("notifications.php");
include("loguser.php");
include("permissions.php");
include("pluginsystem.php");
include("bbcode.php");
include("bbcode_callbacks.php");
include("post.php");
include("onlineusers.php");

$theme = $loguser['theme'];
include("write.php");
include('lib/layout.php');

include("lists.php");

$bucket = "init"; include('lib/pluginloader.php');

