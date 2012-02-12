<?php

function actionLink($action, $id=0, $args="")
{
	global $boardroot;
	
	$res = "$boardroot?action=$action";
	
	if($id)
		$res .= "&id=$id";
	if($args)
		$res .= "&$args";

	return $res;
	
//Possible URL Rewriting :D
//	return "$boardroot/$action/$id?$args";
	
}

function actionLinkTag($text, $action, $id=0, $args="")
{
	return '<a href="'.actionLink($action, $id, $args).'">'.$text.'</a>';
}

function resourceLink($what)
{
	global $boardroot;
	return "$boardroot$what";
}

function themeResourceLink($what)
{
	global $theme, $boardroot;
	return $boardroot."themes/$theme/$what";
}


