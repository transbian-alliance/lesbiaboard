<?php
//  AcmlmBoard XD - User account registration page
//  Access: any, but meant for guests.

$crumbs = new PipeMenu();
$crumbs->add(new PipeMenuLinkEntry(__("Register"), "register"));
makeBreadcrumbs($crumbs);

if(!isHttps()) {
	Kill("Please use the HTTPS version of Lesbiaboard to register a new user.");
}

$haveSecurimage = is_file("securimage/securimage.php");
if($haveSecurimage)
	session_start();

$title = __("Register");

if(isset($_POST['name']))
{
	$name = trim($_POST['name']);
	$cname = str_replace(" ","", strtolower($name));

	// this is only meant to keep non-sentient actors from registering accounts
	$test_answers = array(
		"lesbians",
		"lesbian",
		"lesbian people",
		"transbians",
		"transbian",
		"transbian people",
		"trans people",
		"trans",
		"transgender",
		"transgender people",
		"girls",
		"enbies",
		"lgbt",
	);
	$test_reply = trim(strtolower($_POST['test_question']));

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

	//This makes testing faster.
	if($_SERVER['REMOTE_ADDR'] == "127.0.0.1")
		$ipKnown = 0;
		
	if($uname == $cname)
		$err = __("This user name is already taken. Please choose another.");
	else if($name == "" || $cname == "")
		$err = __("The user name must not be empty. Please choose one.");
	else if(strpos($name, ";") !== false)
		$err = __("The user name cannot contain semicolons.");
	elseif($ipKnown >= 3)
		$err = __("Another user is already using this IP address.");
	else if ($_POST['pass'] !== $_POST['pass2'])
		$err = __("The passwords you entered don't match.");
	else if (!in_array($test_reply, $test_answers))
		$err = __("You did not pass the required security question to register an account.");
	else if($haveSecurimage)
	{
		include("securimage/securimage.php");
		$securimage = new Securimage();
		if($securimage->check($_POST['captcha_code']) == false)
			$err = __("You got the CAPTCHA wrong.");
	}

	if($err)
	{
		Alert($err);
	}
	else
	{
		$newsalt = Shake();
		$password = password_hash($_POST['pass'], PASSWORD_DEFAULT);

		$rUsers = Query("insert into {users} (name, password, pss, regdate, lastactivity, lastip, email, theme) values ({0}, {1}, {2}, {3}, {3}, {4}, {5}, {6})", $_POST['name'], $password, $newsalt, time(), $_SERVER['REMOTE_ADDR'], $_POST['email'], Settings::get("defaultTheme"));
		
		$uid = insertId();
		
		if($uid == 1)
			Query("update {users} set powerlevel = 4 where id = 1");

		recalculateKarma($uid);
		
		logAction('register', array('user' => $uid));

		$user = Fetch(Query("select * from {users} where id={0}", $uid));
		$user["rawpass"] = $_POST["pass"];

		$bucket = "newuser"; include("lib/pluginloader.php");

		$sessionID = Shake();
		setcookie("logsession", $sessionID, 0, $boardroot, "", false, true);
		Query("INSERT INTO {sessions} (id, user, autoexpire) VALUES ({0}, {1}, {2})", doHash($sessionID.$salt), $user["id"], 0);
		redirectAction("board");
	}
}

$name = "";
if(isset($_POST["name"]))
	$name = htmlspecialchars($_POST["name"]);
$email = "";
if(isset($_POST["email"]))
	$email = htmlspecialchars($_POST["email"]);

echo "
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
				<input type=\"text\" id=\"un\" name=\"name\" value=\"$name\" maxlength=\"20\" style=\"width: 98%;\"  class=\"required\" />
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
				<input type=\"email\" id=\"email\" name=\"email\" value=\"$email\" style=\"width: 98%;\" maxlength=\"60\" />
			</td>
		</tr>
		<tr>
			<td class=\"cell2\">
				<label for=\"test_question\">".__("Who is this forum aimed at? (there are a few correct answers)")."</label>
			</td>
			<td class=\"cell0\">
				<input type=\"text\" id=\"test_question\" name=\"test_question\" style=\"width: 98%;\" class=\"required\" />
			</td>
		</tr>";

if($haveSecurimage)
{
	echo "
		<tr>
			<td class=\"cell2\">
				".__("Security")."
			</td>
			<td class=\"cell1\">
				<img width=\"200\" height=\"80\" id=\"captcha\" src=\"".actionLink("captcha", shake())."\" alt=\"CAPTCHA Image\" />
				<button onclick=\"document.getElementById('captcha').src = '".actionLink("captcha", shake())."?' + Math.random(); return false;\">".__("New")."</button><br />
				<input type=\"text\" name=\"captcha_code\" size=\"10\" maxlength=\"6\" class=\"required\" />
			</td>
		</tr>";
}

echo "
		<tr class=\"cell2\">
			<td></td>
			<td>
				<input type=\"submit\" name=\"action\" value=\"".__("Register")."\"/>
			</td>
		</tr>
		<tr>
			<td colspan=\"2\" class=\"cell0 smallFonts\">
				".__("Specifying an email address is not exactly a hard requirement, but it will allow you to reset your password should you forget it. By default, your email is not shown.")."
			</td>
		</tr>
	</table>
</form>";

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

