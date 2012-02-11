<?php
/* [svg] BBCode
 * By Kawa
 *
 * Requires ABXD 2.1.x for BBCode bucket.
 */

registerPlugin("[svg] BBCode");

function SVG_Code()
{
	global $text;
    $svgin="'<?xml version=\"1.0\"?>"
          ."<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" "
	  ."\"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">"
	  ."<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"\\1\" height=\"\\2\" onload=\"InitSMIL(evt)\">'";
    $svgout="'</svg>'";
    $svglist1 = array("\\\"","\\\\","\\'");
    $svglist2 = array("\"","\\","\'");
    $text=preg_replace("'\[svg ([0-9]+) ([0-9]+)\](.*?)\[/svg\]'sie",
       '\''."<embed src=\"data:image/svg+xml;base64,"
      .'\''.".base64_encode($svgin.".'str_replace($svglist1,$svglist2,\'\\3\')'.".$svgout).".'"'
      ."\\\" type=\\\"image/svg+xml\\\" width=".'\'\\1\' height=\'\\2\''." />\"",$text);

}

function SVG_Help($tag)
{
	if($tag == "Embeds")
		write("
				[svg ## ##]&hellip;[/svg] &mdash; insert SVG image<br />
");
}

register("bbcodes", "SVG_Code");
register("postHelp", "SVG_Help", 1);

?>