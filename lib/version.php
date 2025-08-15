<?php

$abxd_version = 302; // schema version

$lb_ver_major = 1;
$lb_ver_minor = 4;
$lb_ver_patch = NULL;

function getCurrentGitCommitHash()
{
	$path = __DIR__ .'/../.git/';

	if (! file_exists($path)) {
		return null;
	}

	$head = trim(substr(file_get_contents($path . 'HEAD'), 4));
	$hash = trim(file_get_contents(sprintf($path . $head)));
	return $hash;
}

function doBoardVersionFooter() {
	global $lb_ver_major, $lb_ver_minor, $lb_ver_patch;
	$verStr = "$lb_ver_major.$lb_ver_minor";
	if (isset($lb_ver_patch))
		$verStr = "$lb_ver_major.$lb_ver_minor.$lb_ver_patch";
	$hash = getCurrentGitCommitHash();
	if ($hash)
		$verStr .= ' commit <a target="_blank" href="https://github.com/transbian-alliance/lesbiaboard/commit/'.$hash.'">'.substr($hash, 0, 8).'</a>';
	return $verStr;
}