<?php
include("lib/common.php");

if($loguser['powerlevel'] < 3)
	Kill(__("You're not admin. There is nothing for you here."));
	
system("git pull");

?>
