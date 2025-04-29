<?php

$abxd_version = 302; // schema version

$lb_ver_major = 1;
$lb_ver_minor = 4;
$lb_ver_patch = NULL;

function doBoardVersionFooter()
{
  $verStr = "$lb_ver_major.$lb_ver_sub";
  if (isset($lb_ver_subsub))
    $verStr = "$lb_ver_major.$lb_ver_sub.$lb_ver_subsub";
  return $verStr;
}