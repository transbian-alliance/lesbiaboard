<?php

$abxd_version = 302; // Keeping this here purely for consistency - this should be updated only if the schema is updated tbh. --Pululut.

$lb_ver_major = 1;
$lb_ver_sub = 4;
$lb_ver_subsub = NULL; // If we're making a subversion of a subversion, don't bother changing this, refer to doBoardVersion() --Pululut
$interim_build = TRUE; // Only change this when making a stable release of the software, otherwise leave it alone. --Pululut

function doBoardVersionFooter()
{
  $verStr = "$lb_ver_major.$lb_ver_sub";
  if (isset($lb_ver_subsub))
    $verStr = "$lb_ver_major.$lb_ver_sub.$lb_ver_subsub";
  return $verStr;
}

