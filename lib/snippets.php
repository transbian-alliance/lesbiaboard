<?php
//  AcmlmBoard XD support - Handy snippets

function endsWith($a, $b){
	return substr($a, strlen($a) - strlen($b)) == $b;
}

function endsWithIns($a, $b){
	return endsWith(strtolower($a), strtolower($b));
}

function startsWith($a, $b){
	return substr($a, 0, strlen($b)) == $b;
}

function startsWithIns($a, $b){
	return startsWith(strtolower($a), strtolower($b));
}

function GetRainbowColor()
{
	$stime = gettimeofday();
	$h = (($stime[usec] / 5) % 600);
	if($h < 100)
	{
		$r = 255;
		$g = 155 + $h;
		$b = 155;
	}
	else if($h < 200)
	{
		$r = 255 - $h + 100;
		$g = 255;
		$b = 155;
	}
	else if($h < 300)
	{
		$r = 155;
		$g = 255;
		$b = 155 + $h - 200;
	}
	else if($h < 400)
	{
		$r = 155;
		$g = 255 - $h + 300;
		$b = 255;
	}
	else if($h < 500)
	{
		$r = 155 + $h - 400;
		$g = 155;
		$b = 255;
	}
	else
	{
		$r = 255;
		$g = 155;
		$b = 255 - $h + 500;
	}
	return substr(dechex($r * 65536 + $g * 256 + $b), -6);
}



function TimeUnits($sec)
{
	if($sec <    60) return "$sec sec.";
	if($sec <  3600) return floor($sec/60)." min.";
	if($sec < 86400) return floor($sec/3600)." hour".($sec >= 7200 ? "s" : "");
	return floor($sec/86400)." day".($sec >= 172800 ? "s" : "");
}

function DoPrivateMessageBar()
{
	global $loguserid, $loguser;

	if($loguserid)
	{
		$unread = FetchResult("select count(*) from {pmsgs} where userto = {0} and msgread=0 and drafting=0", $loguserid);
		$content = "";
		if($unread)
		{
			$pmNotice = $loguser['usebanners'] ? "id=\"pmNotice\" " : "";
			$rLast = Query("select * from {pmsgs} where userto = {0} and msgread=0 order by date desc limit 0,1", $loguserid);
			$last = Fetch($rLast);
			$rUser = Query("select * from {users} where id = {0}", $last['userfrom']);
			$user = Fetch($rUser);
			$content .= format(
"
		".__("You have {0}{1}. {2}Last message{1} from {3} on {4}."),
			Plural($unread, format(__("new {0}private message"), "<a href=\"".actionLink("private")."\">")),
			"</a>",
			"<a href=\"".actionLink("showprivate", $last['id'])."\">",
			UserLink($user), formatdate($last['date']));
		}

		if($loguser['newcomments'])
		{
			$content .= format(
"
		".__("You {0} have new comments in your {1}profile{2}."),
			$content != "" ? "also" : "",
			"<a href=\"".actionLink("profile", $loguserid)."\">",
			"</a>");
		}

		if($content)
			write(
"
	<div {0} class=\"outline margin header0 cell0 smallFonts\">
		{1}
	</div>
", $pmNotice, $content);
	}
}

function DoSmileyBar($taname = "text")
{
	global $smiliesOrdered;
	$expandAt = 100;
	LoadSmiliesOrdered();
	print '<table class="message margin">
		<tr class="header0"><th>'.__("Smilies").'</th></tr>
		<tr class="cell0"><td id="smiliesContainer">';

	if(count($smiliesOrdered) > $expandAt)
		write("<button class=\"expander\" id=\"smiliesExpand\" onclick=\"expandSmilies();\">&#x25BC;</button>");
	print "<div class=\"smilies\" id=\"commonSet\">";
	
	$i = 0;
	foreach($smiliesOrdered as $s)
	{
		if($i == $expandAt)
			print "</div><div class=\"smilies\" id=\"expandedSet\">";
		print "<img src=\"".resourceLink("img/smilies/".$s['image'])."\" alt=\"".htmlentities($s['code'])."\" title=\"".htmlentities($s['code'])."\" onclick=\"insertSmiley(' ".str_replace("'", "\'", $s['code'])." ');\" />";
		$i++;
	}

	print '</div></td></tr></table>';
}

function DoPostHelp()
{
	write("
	<table class=\"message margin\">
		<tr class=\"header0\"><th>".__("Post help")."</th></tr>
		<tr class=\"cell0\"><td>
			<button class=\"expander\" id=\"postHelpExpand\" onclick=\"expandPostHelp();\">&#x25BC;</button>
			<div id=\"commonHelp\" class=\"left\">
				<h4>".__("Presentation")."</h4>
				[b]&hellip;[/b] &mdash; <strong>".__("bold type")."</strong> <br />
				[i]&hellip;[/i] &mdash; <em>".__("italic")."</em> <br />
				[u]&hellip;[/u] &mdash; <span class=\"underline\">".__("underlined")."</span> <br />
				[s]&hellip;[/s] &mdash; <del>".__("strikethrough")."</del><br />
			</div>
			<div id=\"expandedHelp\" class=\"left\">
				[code]&hellip;[/code] &mdash; <code>".__("code block")."</code> <br />
				[spoiler]&hellip;[/spoiler] &mdash; ".__("spoiler block")." <br />
				[spoiler=&hellip;]&hellip;[/spoiler] <br />
	");
	$bucket = "postHelpPresentation"; include("./lib/pluginloader.php");
	write("
				<br />
				<h4>".__("Links")."</h4>
				[url]http://&hellip;[/url], ".__("or")." <br />
				[url=http://&hellip;]&hellip;[/url] &mdash; ".__("insert external link")." <br />
				>>&hellip; &mdash; ".__("link to post by ID")." <br />
				[user=##] &mdash; ".__("link to user's profile by ID")." <br />
	");
	$bucket = "postHelpLinks"; include("./lib/pluginloader.php");
	write("
				<br />
				<h4>".__("Quotations")."</h4>
				[quote]&hellip;[/quote] &mdash; ".__("untitled quote")."<br />
				[quote=&hellip;]&hellip;[/quote] &mdash; ".__("\"Posted by &hellip;\"")." <br />
				[quote=\"&hellip;\" id=\"&hellip;\"]&hellip;[/quote] &mdash; \"".__("\"Post by &hellip;\" with link by post ID")." <br />
	");
	$bucket = "postHelpQuotations"; include("./lib/pluginloader.php");
	write("
				<br />
				<h4>".__("Embeds")."</h4>
				[img]http://&hellip;[/img] &mdash; ".__("insert image")." <br />
				[img=http://&hellip;]image description&hellip;[/img] &mdash; ".__("insert image with description")." <br />
				[video]http://&hellip;[/video] &mdash; ".__("insert video (youtube, streamable or mp4)")." <br />
	");
	$bucket = "postHelpEmbeds"; include("./lib/pluginloader.php");
	write("
			</div>
			<br />
			".__("Most plain HTML also allowed.")."
		</td></tr>
	</table>
	");
}



function recalculateKarma($uid)
{
	$karma = 100;
	$karmaWeights = array(5, 10, 10, 15, 15);
	$rKarma = Query("select powerlevel, up from {uservotes} left join {users} on id=voter where uid={0} and powerlevel > -1", $uid);
	while($k = Fetch($rKarma))
	{
		if($k['up'])
			$karma += $karmaWeights[$k['powerlevel']];
		else
			$karma -= $karmaWeights[$k['powerlevel']];
	}
	Query("update {users} set karma={0} where id={1}", $karma, $uid);
	return $karma;
}


function cdate($format, $date = 0)
{
	global $loguser;
	if($date == 0)
		$date = time();
	$hours = (int)($loguser['timezone']/3600);
	$minutes = floor(abs($loguser['timezone']/60)%60);
	$plusOrMinus = $hours < 0 ? "" : "+";
	$timeOffset = $plusOrMinus.$hours." hours, ".$minutes." minutes";
	return gmdate($format, strtotime($timeOffset, $date));
}

function Report($stuff, $hidden = 0, $severity = 0)
{
	//legacy function that should be removed.
}


//TODO: This is used for notifications. We should replace this with the coming-soon notifications system ~Dirbaio
function SendSystemPM($to, $message, $title)
{
	global $systemUser;

	//Don't send system PMs if no System user was set
	if($systemUser == 0)
		return;

	$rPM = Query("insert into {pmsgs} (userto, userfrom, date, ip, msgread) values ({0}, {1}, {2}, '127.0.0.1', 0)", $to, $systemUser, time());
	$pid = InsertId();
	$rPM = Query("insert into {pmsgs_text} (pid, text, title) values ({0}, {1}, {2})", $pid, $message, $title);

	//print "PM sent.";
}

function Shake()
{
	$cset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$salt = "";
	$chct = strlen($cset) - 1;
	while (strlen($salt) < 16)
		$salt .= $cset[mt_rand(0, $chct)];
	return $salt;
}

function IniValToBytes($val)
{
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last)
	{
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return $val;
}

function BytesToSize($size, $retstring = '%01.2f&nbsp;%s')
{
	$sizes = array('B', 'KiB', 'MiB');
	$lastsizestring = end($sizes);
	foreach($sizes as $sizestring)
	{
		if($size < 1024)
			break;
		if($sizestring != $lastsizestring)
			$size /= 1024;
	}
	if($sizestring == $sizes[0])
		$retstring = '%01d %s'; // Bytes aren't normally fractional
	return sprintf($retstring, $size, $sizestring);
}

function makeThemeArrays()
{
	global $themes, $themefiles;
	$themes = array();
	$themefiles = array();
	$dir = @opendir("themes");
	while ($file = readdir($dir))
	{
		if ($file != "." && $file != "..")
		{
			$themefiles[] = $file;
			$name = explode("\n", @file_get_contents("./themes/".$file."/themeinfo.txt"));
			$themes[] = trim($name[0]);
		}
	}
	closedir($dir);
}

function getdateformat()
{
	global $loguserid, $loguser;

	if($loguserid)
		return $loguser['dateformat'].", ".$loguser['timeformat'];
	else
		return Settings::get("dateformat");
}

function formatdate($date)
{
	return cdate(getdateformat(), $date);
}
function formatdatenow()
{
	return cdate(getdateformat());
}

function formatBirthday($b)
{
	return format("{0} ({1} old)", cdate("F j, Y", $b), Plural(floor((time() - $b) / 86400 / 365.2425), "year"));
}
function getPowerlevelName($pl) {
	$powerlevels = array(
		-1 => __("Banned"),
		0 => __("Normal"),
		1 => __("Local mod"),
		2 => __("Full mod"),
		3 => __("Admin"),
		4 => __("Root"),
		5 => __("System")
	);
	return $powerlevels[$pl];
}

//TODO Add caching if it's too slow.
function formatIP($ip)
{
	global $loguser;

	$res = $ip;
	$res .=  " " . IP2C($ip);
	if($loguser["powerlevel"] >= 3)
		return actionLinkTag($res, "ipquery", $ip);
	else
		return $res;
}

function ip2long_better($ip)
{ 
	$v = explode('.', $ip); 
	return ($v[0]*16777216)+($v[1]*65536)+($v[2]*256)+$v[3];
}

//TODO: Optimize it so that it can be made with a join in online.php and other places.
function IP2C($ip)
{
	global $dblink;
	//This nonsense is because ips can be greater than 2^31, which will be interpreted as negative numbers by PHP.
	$ipl = ip2long($ip);
	$r = Fetch(Query("SELECT * 
				 FROM {ip2c}
				 WHERE ip_from <= {0s} 
				 ORDER BY ip_from DESC
				 LIMIT 1", 
				 sprintf("%u", $ipl)));

	if($r && $r["ip_to"] >= ip2long_better($ip))
		return " <img src=\"".resourceLink("img/flags/".strtolower($r['cc']).".png")."\" alt=\"".$r['cc']."\" title=\"".$r['cc']."\" />";
	else
		return "";
}

function getBirthdaysText()
{
	$rBirthdays = Query("select u.birthday, u.(_userfields) from {users} u where birthday > 0 and powerlevel >= 0 order by name");
	$birthdays = array();
	while($user = Fetch($rBirthdays))
	{
		$b = $user['birthday'];
		if(gmdate("m-d", $b) == gmdate("m-d"))
		{
			$y = gmdate("Y") - gmdate("Y", $b);
			$birthdays[] = UserLink(getDataPrefix($user, "u_"))." (".$y.")";
		}
	}
	if(count($birthdays))
		$birthdaysToday = implode(", ", $birthdays);
	if($birthdaysToday)
		return "<br>".__("Birthdays today:")." ".$birthdaysToday;
	else
		return "";
}

function HandlePicture($field, $type, $errorname, $mood_id = NULL)
{
	global $userid, $dataDir;
	if($type == 0) // main avatar
	{
		if (isset($mood_id)) {
			$targetFile = $dataDir."avatars/".$userid."_".$mood_id;
		} else {		
			$targetFile = $dataDir."avatars/".$userid;
		}
		$extensions = array("png","jpg","jpeg","gif");
		$maxDim = Settings::get("avatarMaxDim");
		$maxSize = 2 * 1024 * 1024; // 2MB
	}
	else if($type == 1) // minipic
	{
		$targetFile = $dataDir."minipics/".$userid;
		$extensions = array("png", "gif");
		$maxDim = 16;
		$maxSize = 100 * 1024; // 100KB
	}

	$fileName = $_FILES[$field]['name'];
	$fileSize = $_FILES[$field]['size'];
	$tempFile = $_FILES[$field]['tmp_name'];
	list($width, $height, $fileType) = getimagesize($tempFile);
	
	$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	if(!in_array($extension, $extensions))
		return format(__("Invalid extension {0} used for {1}. Allowed: {2}"), $fileName, $errorname, join(", ", $extensions));

	if($fileSize > $maxSize)
		return format(__("File size for {0} is too high. The limit is {1} bytes, the uploaded image is {2} bytes."), $errorname, $maxSize, $fileSize); //."</li>"; <-- huh?

	if($width <= $maxDim && $height <= $maxDim) {
		echo "sdaif";
		copy($tempFile, $targetFile);
	} else {
		if (!Settings::get("avatarAllowAboveMax") || $type == 1) // i don't want to resize minipics
			return format(__("Dimensions of {0} must be at most {1} by {1} pixels."), $errorname, $maxDim);

		if($fileType == 1) $img1 = imagecreatefromgif ($tempFile);
		if($fileType == 2) $img1 = imagecreatefromjpeg($tempFile);
		if($fileType == 3) $img1 = imagecreatefrompng ($tempFile);
		if($fileType > 3) return format(__("Invalid image format detected."));

		$r = imagesx($img1) / imagesy($img1);
		echo $r;
		if($r > 1) {
			$img2=imagecreatetruecolor($maxDim,floor($maxDim / $r));
			imagecopyresampled($img2,$img1,0,0,0,0,$maxDim,$maxDim/$r,imagesx($img1),imagesy($img1));
		} else {
			$img2=imagecreatetruecolor(floor($maxDim * $r), $maxDim);
			imagecopyresampled($img2,$img1,0,0,0,0,$maxDim*$r,$maxDim,imagesx($img1),imagesy($img1));
		}
		imagepng($img2,$targetFile);
	}
  
	return true;
}

?>
