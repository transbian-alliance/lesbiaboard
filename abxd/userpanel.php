<?php

if($loguserid)
{
	print "Logged in as $loguserid<br>";
	print actionLinkTag("Logout", "logout");
}
else
{
	print "Guest!<br>";
	print actionLinkTag("Log in", "login");
	print actionLinkTag("Register", "register");
}

?>
