<?php
// AcmlmBoard XD support - MySQL database wrapper functions

include("database.php");

$queries = 0;

$dblink = new mysqli($dbserv, $dbuser, $dbpass, $dbname);
unset($dbpass);


function justEscape($text)
{
	global $dblink;
	return $dblink->real_escape_string($text);
}

function CheckQuery($query)
{
	$check = preg_replace("@'.*?[^\\\\]'@si", 'lolstring', $query);
	$check = preg_replace("@\".*?[^\\\\]\"@si", 'lolstring', $check);
	if (preg_match("@UPDATE\s+?users\s+?SET\s+?.*?`?(powerlevel|tempbanpl)`?\s*?=\s*?[\"']?\d+?[\"']?@si", $check))
		Report("Unauthorized user powerlevel change (".$query.")", 1, 2);
}

function Query($query)
{
	global $queries, $querytext, $loguser, $dblink;
	if ($loguser['powerlevel'] < 3) CheckQuery($query);
	$res = @$dblink->query($query) or die($dblink->error."<br />Query was: <code>".$query."</code><br />This could have been caused by a database layout change in a recent git revision. Try running the installer again to fix it. <form action=\"install/doinstall.php\" method=\"POST\"><br />
	<input type=\"hidden\" name=\"action\" value=\"Install\" />
	<input type=\"hidden\" name=\"existingSettings\" value=\"true\" />
	<input type=\"submit\" value=\"Click here to re-run the installation sript\" /></form>");
	$queries++;
	$querytext .= str_replace("\n", "", $query)."\n";
	return $res;
}

function Fetch($result)
{
	return $result->fetch_array();
}

function FetchRow($result)
{
	return $result->fetch_row();
}

function FetchResult($query, $row = 0, $field = 0)
{
	$res = Query($query);
	if($res->num_rows == 0) return -1;
	return Result($res, $row, $field);
}

// based on http://stackoverflow.com/a/3779460/736054
function Result($res, $row = 0, $field = 0) {
	$res->data_seek($row);
	$ceva = array_values($res->fetch_assoc());
	$rasp = $ceva[$field];
	return $rasp;
}

function NumRows($result)
{
	return $result->num_rows;
}

function InsertId()
{
	global $dblink;
	return $dblink->insert_id;
}

?>
