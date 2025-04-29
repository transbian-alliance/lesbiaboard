<?php

$abxd_version = 302; // schema version

$lb_ver_major = 1;
$lb_ver_minor = 4;
$lb_ver_patch = NULL;

function doBoardVersionFooter() {
	global $lb_ver_major, $lb_ver_minor, $lb_ver_patch;
	$verStr = "$lb_ver_major.$lb_ver_minor";
	if (isset($lb_ver_patch))
		$verStr = "$lb_ver_major.$lb_ver_minor.$lb_ver_patch";
	return $verStr;
}