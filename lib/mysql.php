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

function Query($query)
{
	global $queries, $querytext, $loguser, $dblink, $debugMode;

//	if($debugMode)
//		$queryStart = usectime();

	$res = @$dblink->query($query);

	if(!$res)
	{
		if($debugMode)
			die(nl2br(backTrace())."<br>".$dblink->error."<br />Query was: <code>".$query."</code><br />This could have been caused by a database layout change in a recent git revision. Try running the installer again to fix it. <form action=\"install/doinstall.php\" method=\"POST\"><br />
			<input type=\"hidden\" name=\"action\" value=\"Install\" />
			<input type=\"hidden\" name=\"existingSettings\" value=\"true\" />
			<input type=\"submit\" value=\"Click here to re-run the installation sript\" /></form>");
		else
			die("MySQL Error.");
	}
	
	$queries++;
	
	if($debugMode)
	{
		$querytext .= "<tr class=\"cell0\">";
		$querytext .= "<td>".nl2br(htmlspecialchars($query))."</td>";
		
//derp, timing queries this way doesn't return accurate results since it's async
//		$querytext .= "<td>".sprintf("%1.3f",usectime()-$queryStart)."</td>";
		$querytext .= "<td>".nl2br(backTrace())."</td>";

		$querytext .= "</tr>";
	}
	
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
function Result($res, $row = 0, $field = 0)
{
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
