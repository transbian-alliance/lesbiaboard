<?php
/* [wiki] BBCode
 * By Kawa
 *
 * Requires ABXD 2.1.x for BBCode bucket.
 */

registerPlugin("[wiki] BBCode");

function MakeWikipedia($matches)
{
	$wikiTitle = $matches[2];
	$escaped = str_replace(" ", "_", $matches[1]);
	return format("<a href=\"http://en.wikipedia.org/wiki/{0}\">{1}</a>", $escaped, $wikiTitle);
}

function MakeShortWikipedia($matches)
{
	$wikiTitle = $matches[1];
	$escaped = str_replace(" ", "_", $wikiTitle);
	return format("<a href=\"http://en.wikipedia.org/wiki/{0}\">{1}</a>", $escaped, $wikiTitle);
}

function Wikipedia_Code()
{
	global $text;
	$text = preg_replace_callback("'\[wiki=(.*?)\](.*?)\[/wiki\]'si", "MakeWikipedia", $text);
	$text = preg_replace_callback("'\[wiki\](.*?)\[/wiki\]'si", "MakeShortWikipedia", $text);

}

function Wikipedia_Help($tag)
{
	if($tag == "Links")
		write("
				[wiki]&hellip;[/wiki] &mdash; link to Wikipedia <br/>
				[wiki=&hellip;]&hellip;[/wiki] <br/>
");
}

register("bbcodes", "Wikipedia_Code");
register("postHelp", "Wikipedia_Help", 1);

?>
