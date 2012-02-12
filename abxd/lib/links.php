<?php

function actionLink($action, $id=0, $args="")
{
	global $boardroot;
	
	$res = "$boardroot?page=$action";
	
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
function actionLinkTagItem($text, $action, $id=0, $args="")
{
	return '<li><a href="'.actionLink($action, $id, $args).'">'.$text.'</a></li>';
}

function actionLinkTagConfirm($text, $prompt, $action, $id=0, $args="")
{
	return '<a onclick="if(!confirm(\''.$prompt.'\')) return false; " href="'.actionLink($action, $id, $args).'">'.$text.'</a>';
}
function actionLinkTagItemConfirm($text, $prompt, $action, $id=0, $args="")
{
	return '<li><a onclick="if(!confirm(\''.$prompt.'\')) return false; " href="'.actionLink($action, $id, $args).'">'.$text.'</a></li>';
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


