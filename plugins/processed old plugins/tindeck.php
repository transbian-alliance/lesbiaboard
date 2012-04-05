<?php
/* [tindeck] BBCode
 * By Kawa
 *
 * Requires ABXD 2.1.x for BBCode bucket.
 */

registerPlugin("[tindeck] BBCode");

function Tindeck_Code()
{
	global $text;
	$text = preg_replace("'\[tindeck\](.*?)\[/tindeck\]'si", "<a href=\"http://tindeck.com/listen/\\1\"><img src=\"http://tindeck.com/image/\\1/stats.png\" alt=\"Tindeck\" /></a>", $text);

}

function Tindeck_Help($tag)
{
	if($tag == "Embeds")
		print "[tindeck]&hellip;[/tindeck] &mdash; link to a song on <a href=\"http://tindeck.com\">Tindeck</a>, song ID only <br />";
}

register("bbcodes", "Tindeck_Code");
register("postHelp", "Tindeck_Help", 1);

?>
