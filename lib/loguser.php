<?php
//  AcmlmBoard XD support - Login support

$bots = array(
	"Microsoft URL Control",
	"Yahoo! Slurp",
	"Mediapartners-Google",
	"Twiceler",
	"facebook",
	"bot","spider", //catch-all
);

$isBot = 0;
if(str_replace($bots,"x",$_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT']) // stristr()/stripos()?
	$isBot = 1;

include("browsers.php");

//Check the amount of users right now for the records
$rMisc = Query("select * from {misc}");
$misc = Fetch($rMisc);

$rOnlineUsers = Query("select id, powerlevel, sex, name from {users} where lastactivity > {0} or lastposttime > {0} order by name", (time()-300));

$_qRecords = "";
$onlineUsers = "";
$onlineUserCt = 0;
while($onlineUser = Fetch($rOnlineUsers))
{
	$onlineUsers .= ":".$onlineUser["id"];
	$onlineUserCt++;
}

if($onlineUserCt > $misc['maxusers'])
{
	$_qRecords = "maxusers = {0}, maxusersdate = {1}, maxuserstext = {2}";
}
//Check the amount of posts for the record
$newToday = FetchResult("select count(*) from {posts} where date > {0}", (time() - 86400));
$newLastHour = FetchResult("select count(*) from {posts} where date > {0}", (time() - 3600));
if($newToday > $misc['maxpostsday'])
{
	if($_qRecords) $_qRecords .= ", ";
	$_qRecords .= "maxpostsday = {3}, maxpostsdaydate = {1}";
}
if($newLastHour > $misc['maxpostshour'])
{
	if($_qRecords) $_qRecords .= ", ";
	$_qRecords .= "maxpostshour = {4}, maxpostshourdate = {1}";
}
if($_qRecords)
{
	$_qRecords = "update {misc} set ".$_qRecords;
	$rRecords = Query($_qRecords, $onlineUserCt, time(), $onlineUsers, $newToday, $newLastHour);
}

//Delete oldies visitor from the guest list. We may re-add him/her later.
$rGuests = Query("delete from {guests} where date < {0}", (time()-300));

//Lift dated Tempbans
$rTempban = Query("update {users} set powerlevel = tempbanpl, tempbantime = 0 where tempbantime != 0 and tempbantime < {0}", time());

//Lift dated IP Bans
$rIPBan = Query("delete from {ipbans} where date != 0 and date < {0}", time());


function isIPBanned($ip)
{
	$ip = trim($ip);
	$rIPBan = Query("select * from {ipbans} where instr({0}, ip)=1", $ip);
	return NumRows($rIPBan) != 0;
}

$rIPBan = Query("select * from {ipbans} where instr({0}, ip)=1", $_SERVER['REMOTE_ADDR']);

if(isIPBanned($_SERVER['REMOTE_ADDR']))
{
	$ipban = Fetch($rIPBan);
	print "You have been ".($ipban['date'] ? "" : "<strong>permanently</strong> ")."IP-banned from this board".($ipban['date'] ? " until ".gmdate("M jS Y, G:i:s",$ipban['date'])." (GMT). That's ".TimeUnits($ipban['date']-time())." left" : "").". Attempting to get around this in any way will result in worse things.";
	$bucket = "ipbanned"; include('lib/pluginloader.php');

	exit();
}

if(FetchResult("select count(*) from {proxybans} where instr({0}, ip)=1", $_SERVER['REMOTE_ADDR']))
	die("No.");


$logdata = unserialize(base64_decode($_COOKIE['logdata']));
$loguserid = (int)$logdata['loguserid'];
$loguserbull = $logdata['bull'];

$wantGuest = TRUE;

if($loguserid) //Are we logged in?
{
	$rLogUser = Query("select * from {users} where id={0}", (int)$loguserid);
	if(NumRows($rLogUser)) //We have at least one result.
	{
		$loguser = Fetch($rLogUser);
		
		//Bullcheck
		$ourbull = hash('sha256', $loguser['id'].$loguser['password'].$salt.$loguser['pss'], FALSE);
		if($loguserbull == $ourbull)
		{
			// Given that tokens are to be included in URLs, they really shouldn't be as long as a SHA256 hash
			// SHA1 with a sufficiently long salt should be enough.
			$loguser['token'] = hash('sha1', "{$loguserid},{$loguser['pss']},{$salt},dr567hgdf546guol89ty896rd7y56gvers9t");
			
			$wantGuest = FALSE;
		}
	}
}

if($wantGuest)
{
	$loguser = array("name"=>"", "powerlevel"=>0, "threadsperpage"=>50, "postsperpage"=>20, "theme"=>Settings::get("defaultTheme"), 
		"dateformat"=>"m-d-y", "timeformat"=>"h:i A", "fontsize"=>80, "timezone"=>0, "blocklayouts"=>!Settings::get("guestLayouts"),
		'token'=>hash('sha1', rand()));
	$loguserid = 0;
}

if($hacks['forcetheme'] != "")
	$loguser['theme'] = $hacks['forcetheme'];

if ($loguserid)
	$loguserNotifications = getNotifications($loguserid);
else
	$loguserNotifications = array();

$loguserLogin = 1;

function setLastActivity()
{
	global $loguserid, $isBot, $lastKnownBrowser;
	
	Query("delete from {guests} where ip = {0}", $_SERVER['REMOTE_ADDR']);

	if($loguserid == 0)
	{
		Query("insert into {guests} (date, ip, lasturl, useragent, bot) values ({0}, {1}, {2}, {3}, {4})",
			time(), $_SERVER['REMOTE_ADDR'], getRequestedURL(), $_SERVER['HTTP_USER_AGENT'], $isBot);
	}
	else
	{
		Query("update {users} set lastactivity={0}, lastip={1}, lasturl={2}, lastknownbrowser={3}, loggedin=1 where id={4}",
			time(), $_SERVER['REMOTE_ADDR'], getRequestedURL(), $lastKnownBrowser, $loguserid);
	}
}

?>
