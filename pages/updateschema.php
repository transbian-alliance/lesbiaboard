<?php

if($loguser['powerlevel'] < 3)
	Kill(__("You're not an administrator. There is nothing for you here."));

MakeCrumbs(array(__("Admin") => actionLink("admin"), __("Update table structure") => actionLink("updateschema")), "");

Upgrade();

?>

