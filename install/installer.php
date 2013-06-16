<?php

//Random includes needed for the installer to work.

$debugMode = true;
error_reporting(-1 & ~E_NOTICE);

function cdate($format, $date = 0)
{
	global $loguser;
	if($date == 0)
		$date = gmmktime();
	$hours = (int)($loguser['timezone']/3600);
	$minutes = floor(abs($loguser['timezone']/60)%60);
	$plusOrMinus = $hours < 0 ? "" : "+";
	$timeOffset = $plusOrMinus.$hours." hours, ".$minutes." minutes";
	return gmdate($format, strtotime($timeOffset, $date));
}

include("lib/mysqlfunctions.php");
include("lib/version.php");
include("lib/debug.php");
include("lib/mysql.php");

function install()
{
	global $dblink, $dbserv, $dbuser, $dbpass, $dbname, $dbpref, $dberror;
	
	if(file_exists("config/database.php"))
	{
		//TODO: Check for errors when parsing this file (It may be corrupted or wrong or whatever.
		//If it fails, fail gracefully and instruct the user to fix or delete database.php
		include("config/database.php");
	}
	else
	{
		$dbserv = $_POST['dbserv'];
		$dbuser = $_POST['dbuser'];
		$dbpass = $_POST['dbpass'];
		$dbname = $_POST['dbname'];
		$dbpref = $_POST['dbpref'];
	}
	
	if(!sqlConnect())
		installationError("Could not connect to the database. Error was: ".$dberror);
	
	$currVersion = getInstalledVersion();
	
	if($currVersion == $abxd_version)
		installationError("The board is already installed and updated.");
	
	upgrade();
	
	if(!is_dir("config"))
		mkdir("config");
	
	
	if($currVersion == -1)
	{
		//Stuff to do on new installation (Not upgrade)
		Import("install/smilies.sql");
		Import("install/installDefaults.sql");
		if(!file_exists("config/salt.php"))
			writeConfigSalt();
	}
	
	if(!file_exists("config/database.php"))
		writeConfigDatabase();
}



//=============================================
// UTILITY FUNCTIONS

//Returns -1 if board is not installed.
//Returns the version installed if installed.
function getInstalledVersion()
{
	//If no misc table, not installed.
	if(numRows(query("SHOW TABLES LIKE 'misc'")) == 0)
		return -1;

	$row = query("SELECT * FROM misc");
	
	//If no row in misc table, not installed.
	if(numRows($row) == 0)
		return -1;

	//Otherwise return version.		
	$row = fetch($row);
	return $row["version"];
}

function installationError($message)
{
	echo $message;
	die();
}


function writeConfigDatabase()
{
	global $dbserv, $dbuser, $dbpass, $dbname, $dbpref;
	$dbcfg = @fopen("config/database.php", "w+") 
		or installationError(
			"Could not open the database configuration file (config/database.php) for writing.<br>
			 Make sure that PHP has access to this file.");

	fwrite($dbcfg, "<?php\n");
	fwrite($dbcfg, "//  AcmlmBoard XD support - Database settings\n\n");
	fwrite($dbcfg, '$dbserv = ' . var_export($dbserv, true) . ";\n");
	fwrite($dbcfg, '$dbuser = ' . var_export($dbuser, true) . ";\n");
	fwrite($dbcfg, '$dbpass = ' . var_export($dbpass, true) . ";\n");
	fwrite($dbcfg, '$dbname = ' . var_export($dbname, true) . ";\n");
	fwrite($dbcfg, '$dbpref = ' . var_export($dbpref, true) . ";\n");
	fwrite($dbcfg, "\n?>");
	fclose($dbcfg);
}

function writeConfigSalt()
{
	$cset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$salt = "";
	$chct = strlen($cset) - 1;
	while (strlen($salt) < 16)
		$salt .= $cset[mt_rand(0, $chct)];
		
	$sltf = @fopen("config/salt.php", "w+")
		or installationError(
			"Could not open \"config/salt.php\" for writing. <br>
			This has been checked for earlier, so if you see this error now, 
			something very strange is going on.");
			
	fwrite($sltf, "<?php \$salt = \"".$salt."\" ?>");
	fclose($sltf);
}

