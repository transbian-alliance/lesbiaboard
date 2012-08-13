<?php
//  AcmlmBoard XD - User account registration page
//  Access: any, but meant for guests.

$haveSecurimage = is_file("securimage/securimage.php");
if($haveSecurimage)
	session_start();


$title = __("Register");

$backtomain = "<br /><a href=\"./\">".__("Back to main")."</a> &bull; <a href=\"".actionLink("register")."\">".__("Try again")."</a>";
$sexes = array(__("Male"), __("Female"), __("N/A"));

if(!isset($_POST['action']))
{
	write(
"
	<form action=\"".actionLink("register")."\" method=\"post\">
		<table class=\"outline margin width50\">
			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Register")."
				</th>
			</tr>
			<tr>
				<td class=\"cell2\">
					<label for=\"un\">".__("User name")."</label>
				</td>
				<td class=\"cell0\">
					<input type=\"text\" id=\"un\" name=\"name\" maxlength=\"20\" style=\"width: 98%;\"  class=\"required\" />
				</td>
			</tr>
			<tr>
				<td class=\"cell2\">
					<label for=\"pw\">".__("Password")."</label>
				</td>
				<td class=\"cell1\">
					<input type=\"password\" id=\"pw\" name=\"pass\" size=\"13\" maxlength=\"32\" class=\"required\" /> / ".__("Repeat:")." <input type=\"password\" id=\"pw2\" name=\"pass2\" size=\"13\" maxlength=\"32\" class=\"required\" />
				</td>
			</tr>
			<tr>
				<td class=\"cell2\">
					<label for=\"email\">".__("Email address")."</label>
				</td>
				<td class=\"cell0\">
					<input type=\"email\" id=\"email\" name=\"email\" value=\"\" style=\"width: 98%;\" maxlength=\"60\" />
				</td>
			</tr>
			<tr>
				<td class=\"cell2\">
					".__("Sex")."
				</td>
				<td class=\"cell1\">
					{0}
				</td>
			</tr>
			<tr>
				<td class=\"cell2\"></td>
				<td class=\"cell0\">
					<label>
						<input type=\"checkbox\" name=\"readFaq\" />
						".format(__("I have read the {0}FAQ{1}"), "<a href=\"".actionLink("faq")."\">", "</a>")."
					</label>
				</td>
			</tr>
", MakeOptions("sex",2,$sexes));

	if(Settings::get("registrationWord") != "")
	{
		write(
"
			<tr>
				<td class=\"cell2\">
					<label for=\"tw\">".__("The word")."</label>
				</td>
				<td class=\"cell1\">
					<input type=\"text\" id=\"tw\" name=\"theWord\" maxlength=\"100\" style=\"width: 80%;\"  class=\"required\" />
					<img src=\"img/icons/icon5.png\" title=\"".__("It's in the FAQ. Read it carefully and you'll find out what the word is.")."\" alt=\"[?]\" />
				</td>
			</tr>
");
	}

	if($haveSecurimage)
	{
		write(
"
			<tr>
				<td class=\"cell2\">
					".__("Security")."
				</td>
				<td class=\"cell1\">
					<img id=\"captcha\" src=\"captcha.php\" alt=\"CAPTCHA Image\" />
					<button onclick=\"document.getElementById('captcha').src = 'captcha.php?' + Math.random(); return false;\">".__("New")."</button><br />
					<input type=\"text\" name=\"captcha_code\" size=\"10\" maxlength=\"6\" class=\"required\" />
				</td>
			</tr>
");
	}

	write(
"
			<tr class=\"cell2\">
				<td></td>
				<td>
					<input type=\"submit\" name=\"action\" value=\"".__("Register")."\"/>
					<label>
						<input type=\"checkbox\" checked=\"checked\" name=\"autologin\" />
						".__("Log in afterwards")."
					</label>
				</td>
			</tr>
			<tr>
				<td colspan=\"2\" class=\"cell0 smallFonts\">
					".__("Specifying an email address is not exactly a hard requirement, but it will allow you to reset your password should you forget it. By default, your email is not shown.")."
				</td>
			</tr>
		</table>
	</form>
");
}
elseif($_POST['action'] == __("Register"))
{
	$name = $_POST['name'];
	$cname = trim(str_replace(" ","", strtolower($name)));

	$rUsers = Query("select name, displayname from {users}");
	while($user = Fetch($rUsers))
	{
		$uname = trim(str_replace(" ", "", strtolower($user['name'])));
		if($uname == $cname)
			break;
		$uname = trim(str_replace(" ", "", strtolower($user['displayname'])));
		if($uname == $cname)
			break;
	}

	$ipKnown = FetchResult("select COUNT(*) from {users} where lastip={0}", $_SERVER['REMOTE_ADDR']);

	if($uname == $cname)
		$err = __("This user name is already taken. Please choose another.").$backtomain;
	else if($name == "" || $cname == "")
		$err = __("The user name must not be empty. Please choose one.").$backtomain;
	else if(strpos($name, ";") !== false)
		$err = __("The user name cannot contain semicolons.").$backtomain;
	elseif($ipKnown >= 3)
		$err = __("Another user is already using this IP address.").$backtomain;
	else if(!$_POST['readFaq'])
		$err = format(__("You really should {0}read the FAQ{1}&hellip;"), "<a href=\"".actionLink("faq")."\">", "</a>").$backtomain;
	else if(Settings::get("registrationWord") != "" && strcasecmp($_POST['theWord'], Settings::get("registrationWord")))
		$err = format(__("That's not the right word. Are you sure you really {0}read the FAQ{1}?"), "<a href=\"".actionLink("faq")."\">", "</a>").$backtomain;
	else if(strlen($_POST['pass']) < 4)
		$err = __("Your password must be at least four characters long.").$backtomain;
	else if ($_POST['pass'] !== $_POST['pass2'])
		$err = __("The passwords you entered don't match.").$backtomain;

	if($haveSecurimage)
	{
		include("securimage/securimage.php");
		$securimage = new Securimage();
		if($securimage->check($_POST['captcha_code']) == false)
			$err = __("You got the CAPTCHA wrong.").$backtomain;
	}

	if($err)
	{
		Kill($err);
	}

	$newsalt = Shake();
	$sha = doHash($_POST['pass'].$salt.$newsalt);
	$uid = FetchResult("SELECT id+1 FROM {users} WHERE (SELECT COUNT(*) FROM {users} u2 WHERE u2.id={users}.id+1)=0 ORDER BY id ASC LIMIT 1");
	if($uid < 1) $uid = 1;

	$rUsers = Query("insert into {users} (id, name, password, pss, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {4}, {5}, {6}, {7}, {8})", $uid, $_POST['name'], $sha, $newsalt, time(), $_SERVER['REMOTE_ADDR'], $_POST['email'], (int)$_POST['sex'], Settings::get("defaultTheme"));

	if($uid == 1)
		Query("update {users} set powerlevel = 4 where id = 1");

	Report("New user: [b]".$_POST['name']."[/] (#".$uid.") -> [g]#HERE#?uid=".$uid);
	
	$user = Fetch(Query("select * from {users} where id={0}", $uid));
	$user["rawpass"] = $_POST["pass"];
	
	$bucket = "newuser"; include("lib/pluginloader.php");
	

	if($_POST['autologin'])
	{
		$sessionID = Shake();
		setcookie("logsession", $sessionID, 0, "", "", false, true);
		Query("INSERT INTO {sessions} (id, user, autoexpire) VALUES ({0}, {1}, {2})", doHash($sessionID.$salt), $user["id"], 0);
		die(header("Location: ."));
	}
	else
		die(header("Location: ".actionLink("login")));
}

function MakeOptions($fieldName, $checkedIndex, $choicesList)
{
	$checks[$checkedIndex] = " checked=\"checked\"";
	foreach($choicesList as $key=>$val)
		$result .= format("
					<label>
						<input type=\"radio\" name=\"{1}\" value=\"{0}\"{2} />
						{3}
					</label>", $key, $fieldName, $checks[$key], $val);
	return $result;
}
?>
