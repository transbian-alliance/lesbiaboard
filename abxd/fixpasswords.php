<?php

include("lib/common.php");

$password = hash("sha256", "cooldude".$salt, FALSE);
Kill($password);

$users = Query("select id, name, password from users");
while($user = Fetch($users))
{
	if(strlen($user['password']) == 32 || strlen($user['password']) == 64)
		continue;
	
	print "#".$user['id']." ".$user['name']." &mdash; ";
	$raw = base64_decode($user['password']);
	$hex = "";
	for($i = 0; $i < strlen($raw); $i++)
	{
		$h = ord($raw[$i]);
		$hex .= sprintf("%02X", $h);
	}
	print $hex."<br />\n";
	
	Query("update users set password = '".$hex."' where id = ".$user['id']);
}

?>