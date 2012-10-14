<?php

// This file contains all useful stuff...

define('BB_NULL', 0);
define('BB_TEXT', 1);   // any letter - even HTML tags
define('BB_ID', 2);     // [a-zA-Z0-9_-] (Youtube videos for example)
define('BB_NUMBER', 3); // [0-9]
define('BB_COLOR', 5);  // #[0-9a-fA-F]{3}[0-9a-fA-F]{3}?

// category names
define('BB_NONE',0);
define('BB_PRESENTATION',1);
define('BB_LINKS',2);
define('BB_QUOTES',3);
define('BB_EMBED',4);

define('BB_FILE','plugins/custombb/bbcode.txt');

function bb_help($type) {
	static $bbcodes;
	if ($bbcodes === null) {
		$bbcodes = file_exists(BB_FILE) ? unserialize(file_get_contents(BB_FILE)) : array();
	}
	foreach($bbcodes as $bbcode){
		if ($bbcode['category'] == $type) {
			echo "[$bbcode[name]", $bbcode['value'] ? '=&hellip;' : "", ']',
				$bbcode['text'] ? "&hellip;[/$bbcode[name]]" : "",
				" &mdash; $bbcode[description]<br>";
		}
	}
}
