<?php
//  AcmlmBoard XD - User editing page
//  Access: administrators only

include("lib/common.php");

$title = __("Edit user");

AssertForbidden("editUser");

if($loguser['powerlevel'] < 3)
	Kill(__("You're not allowed to edit other users' profiles."));
		
$key = hash('sha256', "{$loguserid},{$loguser['pss']},{$salt}");
if (isset($_POST['action']) && $key != $_POST['key'])
	Kill(__("No."));

if(isset($_POST['id']) && !isset($_GET['id']))
	$_GET['id'] = $_POST['id'];

if(!isset($_GET['id']))
	$_GET['id'] = $loguserid;
//	Kill("User ID unspecified.");

$id = (int)$_GET['id'];
AssertForbidden("editUser", $id);

/*
if($loguser['powerlevel'] < 3)
{
	if(isset($_POST['name']) || isset($_POST['level']) || isset($_POST['globalblock']))
		Kill("Admin-only changes detected.");
}
*/

$qUser = "select * from users where id=".$id;
$rUser = Query($qUser);
if(NumRows($rUser))
	$user = Fetch($rUser);
else
	Kill(__("Unknown user ID."));

if($user['powerlevel'] == 4 && isset($_POST['level']) && (int)$_POST['level'] != 4)
	Kill(__("Root cannot be taken away."));
if($loguser['powerlevel'] != 4 && isset($_POST['level']) && (int)$_POST['level'] == 4)
	Kill(__("Only Root can set Root."));

if(isset($_POST['displayname']))
{
	if(!IsReallyEmpty($_POST['displayname']) || $_POST['displayname'] == $user['name'])
	{
		$_POST['displayname'] = "";
	}
	else
	{
		//$_POST['displayname'] = htmlspecialchars($_POST['displayname']);
		$dispCheck = FetchResult("select count(*) from users where id != ".$user['id']." and (name = '".justEscape($_POST['displayname'])."' or displayname = '".justEscape($_POST['displayname'])."')", 0, 0);
		if($dispCheck > 0)
		{
			Alert(__("The display name you entered is already taken."));
			$_POST['displayname'] = "";
			$user['displayname'] = "";
			$_POST['action'] = "";
		}
	}
}

$editVerb = __("Edit user");
if($id == $loguserid)
	$editVerb = __("Edit profile");

if($_POST['newPW'] != "" && $_POST['repeatPW'] != "" && $_POST['repeatPW'] != $_POST['newPW'])
{
	Alert(__("To change your password, you must type it twice without error."));
	$_POST['newPW'] = "";
	$_POST['action'] = "Not yet.";
}
else if($_POST['repeatPW'] == "")
	$_POST['newPW'] = "";

if($_POST['action'] == __("Tempban"))
{
	if($user['powerlevel'] == 4)
		Kill(__("Trying to ban a root user?"));
	$timeStamp = strtotime($_POST['until']);
	if($timeStamp === FALSE)
	{
		Alert(__("Invalid time given. Try again."));
	}
	else
	{
		SendSystemPM($id, format(__("You have been temporarily banned until {0} GMT. If you don't know why this happened, feel free to ask the one most likely to have done this. Calmly, if possible."), gmdate("M jS Y, G:[b][/b]i:[b][/b]s", $timeStamp)), __("You have been temporarily banned."));
	
		Query("update users set tempbanpl = ".$user['powerlevel'].", tempbantime = ".$timeStamp.", powerlevel = -1 where id = ".$id);
		Redirect(format(__("User has been banned for {0}."), TimeUnits($timeStamp - time())), "profile.php?id=".$id, __("that user's profile"));
	}
}

if($_POST['action'] == $editVerb)
{
	if($user['powerlevel'] == 4 && $loguserid != $id)
		Kill(__("You cannot edit a Root user."));

	//Lots of stolen avatar bullcrap
	if($_POST['removepic'])
	{
		$usepic = "";
		if(substr($user['picture'],0,12) == "img/avatars/")
			@unlink($user['picture']);
	} if($fname = $_FILES['picture']['name'])
	{
		$fext = strtolower(substr($fname,-4));
		$error = "";

		$exts = array(".png",".jpg",".gif");
		$dimx = 100;
		$dimy = 100;
		$dimxs = 60;
		$dimys = 60;
		$size = 30720;

		$validext = false;
		$extlist = "";
		foreach($exts as $ext)
		{
			if($fext == $ext)
			$validext = true;
			$extlist .= ($extlist ? ", " : "").$ext;
		}
		if(!$validext)
			$error.="<li>".__("Invalid file type, must be one of:")." ".$extlist."</li>";

		if(!$error)
		{
			$tmpfile = $_FILES['picture']['tmp_name'];
			$file = "img/avatars/".$id;

			if($loguser['powerlevel'])	//Are we at least a local mod?
				copy($tmpfile,$file);	//Then ignore the 100x100 rule.
			else
			{
				list($width, $height, $type) = getimagesize($tmpfile);

				if($type == 1) $img1 = imagecreatefromgif ($tmpfile);
				if($type == 2) $img1 = imagecreatefromjpeg($tmpfile);
				if($type == 3) $img1 = imagecreatefrompng ($tmpfile);

				if($width <= $dimx && $height <= $dimy && $type<=3)
					copy($tmpfile,$file);
				elseif($type <= 3)
				{
					$r=imagesx($img1)/imagesy($img1);
					if($r > 1)
					{
						$img2=imagecreatetruecolor($dimx,floor($dimy / $r));
						imagecopyresampled($img2,$img1,0,0,0,0,$dimx,$dimy/$r,imagesx($img1),imagesy($img1));
					} else
					{
						$img2=imagecreatetruecolor(floor($dimx * $r), $dimy);
						imagecopyresampled($img2,$img1,0,0,0,0,$dimx*$r,$dimy,imagesx($img1),imagesy($img1));
					}
					imagepng($img2,$file);
				} else
					$error.="<li>".__("Invalid format.")."</li>";
			}
			$usepic = $file;
		} else
			Kill(__("Could not update your avatar for the following reason(s):")."<ul>".$error."</ul>");
	}

	//DNE - consider the minipic support an optional feature for later. -- Kawa
	if($_POST['removempic'])
	{
		$usempic = "";
		if(substr($user['minipic'],0,12) == "img/avatars/")
			@unlink($user['minipic']);
	}
	if($fname = $_FILES['minipic']['name'])
	{
		$fext = strtolower(substr($fname,-4));
		$error = "";

		$dimx = 16;
		$dimy = 16;
		$size = 200000;

		$validext = false;
		if($fext == ".png")
			$validext = true;
		if(!$validext)
			$error.="<li>".__("Invalid file type, must be .png")."</li>";

		if(($fsize=$_FILES['picture']['size']) > $size)
			$error.="<li>".format(__("File size is too high, limit is {0} bytes."), $size)."</li>";

		if(!$error)
		{
			$tmpfile = $_FILES['minipic']['tmp_name'];
			$file = "img/minipics/".$id.".png";

			list($width, $height, $type) = getimagesize($tmpfile);

			if($type != 3)
				$error.="<li>".__("Image must be in PNG format.")."</li>";

			if($width <= $dimx && $height <= $dimy && !$error)
				copy($tmpfile,$file);
			else
				$error.="<li>".format(__("Image must be at most {0} by {1} pixels."), $dimx, $dimy)."</li>";

			$usempic = $file;
		} else
			Kill(__("Could not update your minipic for the following reason(s):")."<ul>".$error."</ul>");
	}

	$tpp = (int)$_POST['tpp'];
	$ppp = (int)$_POST['ppp'];
	$fontsize = (int)$_POST['fontsize'];

	if($tpp > 99)
		$tpp = 99;
	if($tpp < 1)
		$tpp = 50;
	if($ppp > 99)
		$ppp = 99;
	if($ppp < 1)
		$ppp = 20;
	if($fontsize > 200)
		$fontsize = 200;
	if($fontsize < 20)
		$fontsize = 20;
	
	$globalblock = (int)($_POST['globalblock'] == "on");
	$blocklayouts = (int)($_POST['blocklayouts'] == "on");
	$usebanners = (int)($_POST['usebanners'] == "on");
	$showemail = (int)($_POST['showemail'] == "on");
	$signsep = (int)($_POST['signsep'] != "on");

    $timezone = ((int)$_POST['timezoneH'] * 3600) + ((int)$_POST['timezoneM'] * 60) * ((int)$_POST['timezoneH'] < 0 ? -1 : 1);

	if($_POST['presetdate'] != "dummy")
		$_POST['dateformat'] = $_POST['presetdate'];
	if($_POST['presettime'] != "dummy")
		$_POST['timeformat'] = $_POST['presettime'];

	if($_POST['birthday'])
		$bday = strtotime($_POST['birthday'].", 12:00 PM");
	else
		$bday = 0;

	if($_POST['newPW'] != "")
	{
		$newsalt = Shake();
		$sha = hash("sha256", $_POST['newPW'].$salt.$newsalt, FALSE);
	}

	$plugSets = array();
	foreach($pluginSettings as $setName => $setItem)
		$plugSets[$setName] = urlencode($_POST[$setName]);
	//print_r($plugSets);
	$plugSets = serialize($plugSets);

	$qUser = "update users set ";
	$qUser.= "name = '".justEscape($_POST['name'])."', ";
	$qUser.= "displayname = '".justEscape($_POST['displayname'])."', ";
	if($sha)
		$qUser.= "password='".$sha."', ";
	$qUser.= "rankset = ".(int)$_POST['rankset'].", ";
	if(TitleCheck())
		$qUser.= "title = '".justEscape(CleanUpPost($_POST['title'], "", true))."', ";
	if(isset($usepic))
		$qUser.= "picture='".$usepic."', ";
	if(isset($usempic))
		$qUser.= "minipic='".$usempic."', ";
	if(isset($_POST['level']))
		$qUser.= "powerlevel = ".(int)$_POST['level'].", ";
	$qUser.= "globalblock = ".$globalblock.", ";
	$qUser.= "sex = ".(int)$_POST['sex'].", ";
	$qUser.= "realname = '".justEscape(CleanUpPost($_POST['realname']))."', ";
	$qUser.= "location = '".justEscape(CleanUpPost($_POST['location']))."', ";
	$qUser.= "birthday = ".(int)$bday.", ";
	$qUser.= "bio = '".justEscape($_POST['bio'])."', ";
	$qUser.= "postheader = '".justEscape($_POST['postheader'])."', ";
	$qUser.= "signature = '".justEscape($_POST['signature'])."', ";
	$qUser.= "email = '".justEscape($_POST['email'])."', ";
	$qUser.= "homepageurl = '".justEscape($_POST['hpurl'])."', ";
	$qUser.= "homepagename = '".justEscape($_POST['hpname'])."', ";
	$qUser.= "theme = '".justEscape($_POST['theme'])."', ";
	$qUser.= "threadsperpage = ".$tpp.", ";
	$qUser.= "postsperpage = ".$ppp.", ";
	$qUser.= "timezone = ".$timezone.", ";
	$qUser.= "dateformat = '".justEscape($_POST['dateformat'])."', ";
	$qUser.= "timeformat = '".justEscape($_POST['timeformat'])."', ";
	$qUser.= "fontsize = ".$fontsize.", ";
	$qUser.= "signsep = '".$signsep."', ";
	$qUser.= "blocklayouts = '".$blocklayouts."', ";
	$qUser.= "usebanners = '".$usebanners."', ";
	$qUser.= "showemail = '".$showemail."', ";
	$qUser.= "pluginsettings = '".justEscape($plugSets)."' ";
	$qUser.= "where id=".$id." limit 1";
	//Kill($qUser);
	$rUser = Query($qUser);

	if($user['powerlevel'] != (int)$_POST['level'] && $id != $loguserid)
	{
		$votes = Query("select uid from uservotes where voter=".$id);
		if(NumRows($votes))
			while($karmaChameleon = Fetch($votes))
				RecalculateKarma($karmaChameleon['uid']);
		
		$newPL = (int)$_POST['level'];
		$oldPL = $user['powerlevel'];
		if($newPL == 5)
			; //Do nothing -- System won't pick up the phone.
		else if($newPL == -1)
		{
			SendSystemPM($id, __("If you don't know why this happened, feel free to ask the one most likely to have done this. Calmly, if possible."), __("You have been banned."));			
		}
		else if($newPL == 0)
		{
			if($oldPL == -1)
				SendSystemPM($id, __("Try not to repeat whatever you did that got you banned."), __("You have been unbanned."));
			else if($oldPL > 0)
				SendSystemPM($id, __("Try not to take it personally."), __("You have been brought down to normal."));
		}
		else if($newPL == 4)
		{
			SendSystemPM($id, __("Your profile is now untouchable to anybody but you. You can give root status to anybody else, and can access the RAW UNFILTERED POWERRR of sql.php. Do not abuse this. Your root status can only be removed through sql.php."), __("You are now a root user."));
		}
		else
		{
			if($oldPL == -1)
				; //Do nothing.
			else if($oldPL > $newPL)
				SendSystemPM($id, __("Try not to take it personally."), __("You have been demoted."));
			else if($oldPL < $newPL)
				SendSystemPM($id, __("Congratulations. Don't forget to review the rules regarding your newfound powers."), __("You have been promoted."));
		}
	}
	
	Report("[b]".$loguser['name']."[/] edited [b]".$user['name']."[/]'s profile. -> [g]#HERE#?uid=".$id, 1);
	Redirect(__("Profile updated."), "profile.php?id=".$id,($id == $loguserid ? __("your profile") : __("that user's profile")));
}

$sexes = array(__("Male"), __("Female"), __("N/A"));
$levels = array(-1 => __("-1 - Banned"), 0 => __("0 - Normal user"), 1 => __("1 - Local Mod"), 2 => __("2 - Full Mod"), 3 => __("3 - Admin"));
if($loguser['powerlevel'] == 4 || $user['powerlevel'] == 4)
	$levels[4] = __("4 - Root");
$levels[5] = __("5 - System");

$qRanksets = "select name from ranksets";
$rRanksets = Query($qRanksets);
$ranksets[] = __("None");
while($rankset = Fetch($rRanksets))
	$ranksets[] = $rankset['name'];

//Iterate through the themes, appending the number of users with that theme...
foreach($themes as $themeKey => $themeName)
{
	$qCount = "select count(*) from users where theme='".$themeKey."'";
	$c = FetchResult($qCount);
	$themes[$themeKey] .= " (".$c.")";
}

$datelist['dummy'] = __("[select]");
$timelist['dummy'] = __("[select]");

$dateformats=array('','m-d-y','d-m-y','y-m-d','Y-m-d','m/d/Y','d.m.y','M j Y','D jS M Y');
$timeformats=array('','h:i A','h:i:s A','H:i','H:i:s');

foreach($dateformats as $format)
	$datelist[$format] = ($format ? $format.' ('.cdate($format).')':'');
foreach($timeformats as $format)
	$timelist[$format] = ($format ? $format.' ('.cdate($format).')':'');

if($user['birthday'] == 0)
	$bday = "";
else
	$bday = gmdate("F j, Y", $user['birthday']);

$plugsets = "";
$userPluginSettings = unserialize($user['pluginsettings']);
if(!is_array($userPluginSettings))
	$userPluginSettings = array();
if(array_key_exists($settingname, $userPluginSettings))
	return $userPluginSettings[$settingname];

foreach($pluginSettings as $setName => $setItem)
{
	$c = ($c + 1) % 2;
	if($setItem['check'])
	{
		$checked = $userPluginSettings[$setName] ? " checked=\"checked\"" : "";
		$plugsets .= format(
"
			<tr class=\"cell{0}\">
				<td>
					&nbsp;
				</td>
				<td>
					<label>
						<input type=\"checkbox\" name=\"{2}\" {3}/>
						{1}
					</label>
				</td>
			</tr>
",	$c, $setItem['label'], $setName, $checked);
	}
	else
	{
		$plugsets .= format(
"
			<tr class=\"cell{0}\">
				<td>
					{1}
				</td>
				<td>
					<input type=\"text\" name=\"{2}\" value=\"{3}\" style=\"width: 98%;\" maxlength=\"200\" />
				</td>
			</tr>
",	$c, $setItem['label'], $setName, htmlval($userPluginSettings[$setName]));
	}
}
if($plugsets == "")
	$plugsets = "<tr><td colspan=\"2\" class=\"cell0\">No active plugins with settings.</td></tr>";

if($user['powerlevel'] != 4)
	write(
"
	<form action=\"edituser.php\" method=\"post\">
		<table class=\"outline margin width25\" style=\"float: right;\">
			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Quick-E Ban&trade;")."
				</th>
			</tr>
			<tr>
				<td class=\"cell2\">
					<label for=\"until\">".__("Target time")."</label>
				</td>
				<td class=\"cell0\">
					<input id=\"until\" name=\"until\" type=\"text\" />
				</td>
			</tr>
			<tr>
				<td class=\"cell1\" colspan=\"2\">
					<input type=\"submit\" name=\"action\" value=\"".__("Tempban")."\" />
					<input type=\"hidden\" name=\"id\" value=\"{0}\" />
					<input type=\"hidden\" name=\"key\" value=\"{1}\" />
				</td>
			</tr>
		</table>
	</form>
", $id, $key);

write(
"
	<form action=\"edituser.php\" method=\"post\" enctype=\"multipart/form-data\">
		<table class=\"outline margin width50\">

			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Login information")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"un\">".__("User name")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"un\" name=\"name\" value=\"{0}\" style=\"width: 98%;\" maxlength=\"20\" />
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"pw\">".__("Password")."</label>
				</td>
				<td>
					<input type=\"password\" id=\"pw\" name=\"newPW\" size=\"13\" maxlength=\"32\" />
					".__("Repeat:")."
					<input type=\"password\" name=\"repeatPW\" size=\"13\" maxlength=\"32\" />
				</td>
			</tr>

			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Appearance")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"displayname\">".__("Display name")."</label>
					<img src=\"img/icons/icon5.png\" title=\"".__("Leave empty to use login name.")."\" alt=\"[?]\" />
				</td>
				<td>
					<input type=\"text\" id=\"displayname\" name=\"displayname\" value=\"{1}\" style=\"width: 98%;\" maxlength=\"20\" />
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"rankset\">".__("Rank set")."</label>
				</td>
				<td>
					{2}
				</td>
			</tr>
",	htmlval($user['name']), $user['displayname'], MakeSelect("rankset",$user['rankset'],$ranksets));

write(
"
			<tr class=\"cell0\">
				<td>
					<label for=\"tit\">".__("Title")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"tit\" name=\"title\" value=\"{0}\" style=\"width: 98%;\" maxlength=\"255\" />
				</td>
			</tr>
", htmlval($user['title']));

write(
"
			<tr class=\"cell1\">
				<td>
					<label for=\"pic\">".__("Picture")."</label>
				</td>
				<td>
					<input type=\"file\" id=\"pic\" name=\"picture\" style=\"width: 98%;\" />
					<label>
						<input type=\"checkbox\" name=\"removepic\" />
						".__("Remove")."
					</label>
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"mpic\">".__("Minipic")."</label>
				</td>
				<td>
					<input type=\"file\" id=\"mpic\" name=\"minipic\" style=\"width: 98%;\" />
					<label>
						<input type=\"checkbox\" name=\"removempic\" />
						".__("Remove")."
					</label>
				</td>
			</tr>

			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Administrative stuff")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"level\">".__("Power level")."</label>
				</td>
				<td>
					{0}
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					".__("Goggles")."
				</td>
				<td>
					<label>
						<input type=\"checkbox\" name=\"globalblock\" {1} />
						".__("Globally block layout")."
					</label>
				</td>
			</tr>

			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Personal information")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					".__("Sex")."
				</td>
				<td>
					{2}
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"rn\">".__("Real name")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"rn\" name=\"realname\" value=\"{3}\" style=\"width: 98%;\" maxlength=\"60\" />
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"loc\">".__("Location")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"loc\" name=\"location\" value=\"{4}\" style=\"width: 98%;\" maxlength=\"60\" />
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"bd\">".__("Birthday")."</label>
				</td>
				<td class=\"smallFonts\">
					<input type=\"text\" id=\"bd\" name=\"birthday\" value=\"{5}\" style=\"width: 98%;\" maxlength=\"60\" />
					".format(__("(example: June 26, 1983. {0}More{1})"), "<a href=\"http://nl2.php.net/manual/en/function.strtotime.php\">","</a>")."
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"bio\">".__("Bio")."</label>
				</td>
				<td>
					<textarea id=\"bio\" name=\"bio\" rows=\"8\" style=\"width: 98%;\">{6}</textarea>
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					".__("Timezone offset")."
				</td>
				<td>
					<input type=\"text\" name=\"timezoneH\" size=\"2\" maxlength=\"3\" value=\"{23}\" />
					:
					<input type=\"text\" name=\"timezoneM\" size=\"2\" maxlength=\"3\" value=\"{24}\" />
				</td>
			</tr>

			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Post layout")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"hd\">".__("Header")."</label>
				</td>
				<td>
					<textarea id=\"hd\" name=\"postheader\" rows=\"16\" style=\"width: 98%;\">{7}</textarea>
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"sig\">".__("Footer")."</label>
				</td>
				<td>
					<textarea id=\"sig\" name=\"signature\" rows=\"16\" style=\"width: 98%;\">{8}</textarea>
				</td>
			</tr>

			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Contact information")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"email\">".__("Email address")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"email\" name=\"email\" value=\"{9}\" style=\"width: 98%;\" maxlength=\"60\" />
					<label>
						<input type=\"checkbox\" name=\"showemail\" {27}/>
						".__("Public")."
					</label>
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"hpurl\">".__("Homepage URL")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"hpurl\" name=\"hpurl\" value=\"{10}\" style=\"width: 98%;\" maxlength=\"200\" />
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"hpname\">".__("Homepage name")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"hpname\" name=\"hpname\" value=\"{11}\" style=\"width: 98%;\" maxlength=\"60\" />
				</td>
			</tr>

			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Presentation")."
				</th>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"theme\">".__("Theme")."</label>
				</td>
				<td>
					{12}
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"tpp\">".__("Threads per page")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"tpp\" name=\"tpp\" value=\"{13}\" />
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"ppp\">".__("Posts per page")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"ppp\" name=\"ppp\" value=\"{14}\" />
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"df\">".__("Date format")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"df\" name=\"dateformat\" value=\"{15}\" />
					".__("or preset")." {16}
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>
					<label for=\"tf\">".__("Time format")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"tf\" name=\"timeformat\" value=\"{17}\" />
					".__("or preset")." {18}
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>
					<label for=\"fontsize\">".__("Font scale")."</label>
				</td>
				<td>
					<input type=\"text\" id=\"fontsize\" name=\"fontsize\" value=\"{19}\" />
				</td>
			</tr>
			<tr class=\"cell0\">
				<td>
					".__("Extras")."
				</td>
				<td>
					<label>
						<input type=\"checkbox\" id=\"bl\" name=\"blocklayouts\" {20} />
						".__("Block all layouts")."
					</label><br />
					<label>
						<input type=\"checkbox\" id=\"ub\" name=\"usebanners\" {21} />
						".__("Use nice notification banners")."
					</label><br />
					<label>
						<input type=\"checkbox\" name=\"signsep\" {25} />
						".__("Show signature separator")."
					</label>
				</td>
			</tr>
			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Plugins")."
				</th>
			</tr>
			".$plugsets."
		</table>
		<div class=\"margin right width50\" id=\"button\">
			<input type=\"submit\" name=\"action\" value=\"{22}\" />
			<input type=\"hidden\" name=\"id\" value=\"{26}\" />
			<input type=\"hidden\" name=\"key\" value=\"{28}\" />
		</div>
	</form>
",	MakeSelect("level",$user['powerlevel'],$levels), ($user['globalblock'] ? " checked=\"checked\"" : ""),
	MakeOptions("sex",$user['sex'],$sexes), htmlval($user['realname']), htmlval($user['location']),
	$bday, htmlval($user['bio']), htmlval($user['postheader']), htmlval($user['signature']),
	$user['email'], $user['homepageurl'], htmlval($user['homepagename']),
	MakeSelect("theme",$user['theme'],$themes),
	$user['threadsperpage'], $user['postsperpage'], $user['dateformat'],
	MakeSelect("presetdate", -1, $datelist), $user['timeformat'],
	MakeSelect("presettime", -1, $timelist), $user['fontsize'],
	($user['blocklayouts'] ? "checked=\"checked\"" : ""),
	($user['usebanners'] ? "checked=\"checked\"" : ""),
	$editVerb, (int)($user['timezone']/3600), floor(abs($user['timezone']/60)%60),
	($user['signsep'] ? "" : "checked=\"checked\""), $id,
	($user['showemail'] ? "checked=\"checked\"" : ""), $key
);

function MakeOptions($fieldName, $checkedIndex, $choicesList)
{
	$checks[$checkedIndex] = " checked=\"checked\"";
	foreach($choicesList as $key=>$val)
		$result .= "<label><input type=\"radio\" name=\"".$fieldName."\" value=\"".$key."\"".$checks[$key]." />&nbsp;".$val."</label> ";
	return $result;
}

function MakeSelect($fieldName, $checkedIndex, $choicesList)
{
	global $user;
	if($user['powerlevel'] == 4 && $fieldName == "level")
		$root = " disabled=\"disabled\"";

	$checks[$checkedIndex] = " selected=\"selected\"";
	$result = "<select id=\"".$fieldname."\" name=\"".$fieldName."\" size=\"1\"".$root.">";
	foreach($choicesList as $key=>$val)
		$result .= "<option value=\"".$key."\"".$checks[$key].">".$val."</option>";
	$result .= "</select>";
	return $result;
}

function TitleCheck()
{
	global $user;
	if($user['title'] || $user['posts'] >= $customTitleThreshold || $user['powerlevel'] > 0) return 1;
	return 0;
}

function IsReallyEmpty($subject)
{
	$trimmed = trim(preg_replace("/&.*;/", "", $subject));
	return strlen($trimmed) != 0;
}

?>
