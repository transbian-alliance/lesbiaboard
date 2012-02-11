<?php
/* [color] BBCode
 * By Kawa
 *
 * Requires ABXD 2.1.x for BBCode bucket.
 */

registerPlugin("[color] BBCode");

function Color_Code()
{
	global $text;
	$text = preg_replace("'\[color=([A-Za-z#0-9]*?)\](.*?)\[/color\]'si","<span style=\"color: \\1\">\\2</span>", $text);
}

function Color_Help($tag)
{
	if($tag == "Presentation")
		print "[color=&hellip;]&hellip;[/color] &mdash; set text color<br />";
}

register("bbcodes", "Color_Code");
register("postHelp", "Color_Help", 1);

?>