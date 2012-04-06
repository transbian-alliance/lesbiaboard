<?php

/*
	I really don't like the idea of settings.php. Self-modifying code is bad, and 
	it's like that just for historical reasons.
	
	We should now do one settings system that stores everything in the MySQL DB, and works
	for all possible use cases.
	
	This is how the new Settings System should work:
	
	We have to store 4 types of settings:
	- Board global settings
	- Plugin global settings
	- Board per-user settings
	- Plugin per-user settings
	
	Global settings (both types) are stored in the "settings" table.
	"plugin" field is the plugin name, or "main" if it's a board setting.
	"name" field is the name of the setting.
	"value" field is the value of the setting.
	
	TODO: specify how are per-user settings stored and implement them. 
	
	======
	
	Types of settings:
	
	- integer
	- text			Creates a text field
	- textbig		Creates a text box with no controls
	- textbbcode	Creates a text box with BBCode post help
	- texthtml		Creates a text box with HTML post help (if it's ever implemented)

	- theme			Creates a theme selection drop-down. Stores the theme name.
	- user 			Creates a user selection drop-down. Stores the UID
	- forum			Creates a forum selection drop-down. Stores the FID
	
	Additionally settings can have a default value.	
	Also there should be some validation of the setting values.
	Specially for per-user settings, which can be modified at will by users.

*/

//Loads ALL the settings.

function loadSettings()
{
	global $pluginsettings, $globalsettings;
	
	$pluginsettings = array();
	$globalsettings = array();
	$rSettings = Query("select * from settings");
	while($setting = Fetch($rSettings))
	{
		if($setting["plugin"] == "main")
			$globalsettings[$setting["name"]] = $setting["value"];
		else
			$pluginsettings[$setting["plugin"]][$setting["name"]] = $setting["value"];
	}
}

//TODO: Functions to change settings.
//TODO: Setting Description Files
//TODO: Having the board actually use these settings.
?>
