<?php

/*
	Setting system info:
	
	We have to store 4 types of settings:
	- Board global settings
	- Plugin global settings
	- Board per-user settings
	- Plugin per-user settings
	
	Both types of global settings are stored in the "settings" table.
	"plugin" field is the plugin name, or "main" if it's a board setting.
	"name" field is the name of the setting.
	"value" field is the value of the setting.
	
	TODO: Per-user settings (?)
*/

//Loads ALL the settings.

function loadSettings()
{
	global $pluginsettings, $globalsettings;
	
	$pluginsettings = array();
	$globalsettings = array();
	$rSettings = Query("select * from settings");
	while $setting
}
?>
