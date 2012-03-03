<?php

function foonly()
{
	global $sideBarStuff, $sideBarData;
	$sideBarStuff .= "UID: ".$sideBarData['uid']."<br />";
}
register("sideBar", "foonly");

?>