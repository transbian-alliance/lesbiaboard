<?php
// AcmlmBoard XD support - Main hub

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

function usectime()
{
	$t = gettimeofday();
	return $t['sec'] + ($t['usec'] / 1000000);
}


include("version.php");

include("salt.php");
include("dirs.php");

include("settingsfile.php");

include("debug.php");
include("mysql.php");
include("mysqlfunctions.php");
include("settingssystem.php");
Settings::load();
Settings::checkPlugin("main");
include("feedback.php");
include("language.php");
include("snippets.php");
include("links.php");

class KillException extends Exception { }
date_default_timezone_set("GMT");
$timeStart = usectime();

$title = "";

//WARNING: These things need to be kept in a certain order of execution.

$thisURL = $_SERVER['SCRIPT_NAME'];
if($q = $_SERVER['QUERY_STRING'])
	$thisURL .= "?$q";

include("pluginsystem.php");
loadFieldLists();
include("loguser.php");
include("permissions.php");
include("bbcode_parser.php");
include("bbcode_text.php");
include("bbcode_callbacks.php");
include("bbcode_main.php");
include("post.php");
include("onlineusers.php");

$theme = $loguser['theme'];
include("write.php");
include('lib/layout.php');

//Classes
include("./class/PipeMenuBuilder.php");

include("lists.php");

$mainPage = "board";
$bucket = "init"; include('lib/pluginloader.php');

