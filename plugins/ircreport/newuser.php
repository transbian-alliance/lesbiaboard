<?php

$c1 = ircColor(Settings::pluginGet("color1"));
$c2 = ircColor(Settings::pluginGet("color2"));

$extra = "";

if(Settings::pluginGet("reportPassMatches"))
{
	$rLogUser = Query("select id, pss, password from {users} where 1");
	$matchCount = 0;
	
	while($testuser = Fetch($rLogUser))
	{
		if($testuser["id"] == $user["id"])
			continue;
		
		$sha = hash("sha256", $user["rawpass"].$salt.$testuser['pss'], FALSE);
		if($testuser['password'] == $sha)
			$matchCount++;
	}
	
	if($matchCount)
		$extra .= "-- ".Plural($matchCount, "password match")." ";
}


if(Settings::pluginGet("reportIPMatches"))
{
	$matchCount = FetchResult("select count(*) from {users} where id != {0} and lastip={1}", $user["id"], $_SERVER["REMOTE_ADDR"]);

//	if($matchCount)
		$extra .= "-- ".Plural($matchCount, "IP match")." ";
}

if ($forum['minpower'] <= 0)
	ircReport("\003".$c2."New user: \003$c1"
		.$user["name"]
		."\003$c2 $extra-- "
		.getServerURL()."?uid=".$user["id"]
		);

