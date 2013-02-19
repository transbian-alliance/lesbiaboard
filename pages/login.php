<?php
//  AcmlmBoard XD - Login page
//  Access: guests

if($_POST['action'] == "logout")
{
		setcookie("logsession", "", 2147483647, $boardroot, "", false, true);
	Query("UPDATE {users} SET loggedin = 0 WHERE id={0}", $loguserid);
	Query("DELETE FROM {sessions} WHERE id={0}", doHash($_COOKIE['logsession'].$salt));

	logAction('logout', array());
	die(header("Location: $boardroot"));
}
elseif(isset($_POST['actionlogin']))
{
	$okay = false;
	$pass = $_POST['pass'];

	$user = Fetch(Query("select * from {users} where name={0}", $_POST['name']));
	if($user)
	{
		$sha = doHash($pass.$salt.$user['pss']);
		if($user['password'] == $sha)
		{
			print "badpass";
			$okay = true;
		}
		else
			logAction('loginfail', array('user2' => $user["id"]));
	}
	else
		logAction('loginfail2', array('text' => $_POST["name"]));

	if(!$okay)
		Alert(__("Invalid user name or password."));
	else
	{
		//TODO: Tie sessions to IPs if user has enabled it (or probably not)

		$sessionID = Shake();
		setcookie("logsession", $sessionID, 2147483647, $boardroot, "", false, true);
		Query("INSERT INTO {sessions} (id, user, autoexpire) VALUES ({0}, {1}, {2})", doHash($sessionID.$salt), $user["id"], $_POST["session"]?1:0);

		logAction('login', array('user' => $user["id"]));

		redirectAction("board");
	}
}

$forgotPass = "";

if(Settings::get("mailResetSender") != "")
	$forgotPass = "<button onclick=\"document.location = '".actionLink("lostpass")."'; return false;\">".__("Forgot password?")."</button>";

echo "
	<form name=\"loginform\" action=\"".actionLink("login")."\" method=\"post\">
		<table class=\"outline margin width50\">
			<tr class=\"header0\">
				<th colspan=\"2\">
					".__("Log in")."
				</th>
			</tr>
			<tr>
				<td class=\"cell2\">
					<label for=\"un\">".__("User name")."</label>
				</td>
				<td class=\"cell0\">
					<input type=\"text\" id=\"un\" name=\"name\" style=\"width: 98%;\" maxlength=\"25\" />
				</td>
			</tr>
			<tr>
				<td class=\"cell2\">
					<label for=\"pw\">".__("Password")."</label>
				</td>
				<td class=\"cell1\">
					<input type=\"password\" id=\"pw\" name=\"pass\" size=\"13\" maxlength=\"32\" />
				</td>
			</tr>
			<tr>
				<td class=\"cell2\"></td>
				<td class=\"cell1\">
					<label>
						<input type=\"checkbox\" name=\"session\" />
						".__("This session only")."
					</label>
				</td>
			</tr>
			<tr class=\"cell2\">
				<td></td>
				<td>
					<input type=\"submit\" name=\"actionlogin\" value=\"".__("Log in")."\" />
					$forgotPass
				</td>
			</tr>
		</table>
	</form>
	<script type=\"text/javascript\">
		document.loginform.name.focus();
	</script>
";

?>
