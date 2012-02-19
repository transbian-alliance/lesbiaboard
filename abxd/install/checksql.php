<?php
$sqlServ = $_POST['sqlServerAddress'];
if (!$sqlServ) die("No SQL server address was specified. Note that the default \"localhost\" is a plceholder.");
$sqlUser = $_POST['sqlUserName'];
$sqlPass = $_POST['sqlPassword'];
$sqlData = $_POST['sqlDbName'];

$sqlConnection = @mysql_connect($sqlServ, $sqlUser, $sqlPass) or die(mysql_error());

if (isset($_GET['attemptCreate'])) {
	mysql_query("CREATE DATABASE ".mysql_real_escape_string($sqlData)) or die(mysql_error);
	print "Successfully created the database. You should be good to go.";
	die();
}

mysql_select_db($sqlData) or die("The database was not found. <button onclick=\"checkSqlConnection(true);\">Attempt to create it</button>");
print "Connected successfully. Your settings are valid.";
