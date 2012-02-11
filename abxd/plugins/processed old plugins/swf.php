<?php
/* [swf] BBCode
 * By Kawa
 *
 * Requires ABXD 2.1.x for BBCode bucket.
 * External requirements:
 *   swf.js
 * All in /plugins.
 */

registerPlugin("[swf] BBCode");

function MakeFlash($match)
{
	global $flashloops;
	$flashloops++;
	if(strlen($match[3]) < 4)
		return "[user tried to use an URL too short to be a valid .SWF file.]";
	if(strtolower(substr($match[3], -4)) !== ".swf")
		$match[3] .= ".swf";
	return format(
"
	<div class=\"swf\" style=\"width: {0}px;\">
		<div class=\"swfmain\" id=\"swf{4}main\" style=\"width: {1}px; height: {2}px;\">
		</div>
		<div class=\"swfcontrol\">
			<span class=\"swfbuttonoff\" id=\"swf{4}play\" onclick=\"startFlash({4}); return false;\">
				&#x25BA;
			</span>
			<span class=\"swfbuttonon\" id=\"swf{4}stop\" onclick=\"stopFlash({4}); return false;\">
				&#x25A0;
			</span>
			<span class=\"swfurl\" id=\"swf{4}url\">
				{3}
			</span>
		</div>
	</div>
", $match[1] + 4, $match[1], $match[2], $match[3], $flashloops);
}

function SWF_Code()
{
	global $text;
	$text = preg_replace_callback("'\[swf ([0-9]+) ([0-9]+)\](.*?)\[/swf\]'si", "MakeFlash", $text);
}

function SWF_JS()
{
		write("
	<script type=\"text/javascript\" src=\"plugins/swf.js\"></script>
");
}

function SWF_Help($tag)
{
	if($tag == "Embeds")
		write("
				[swf ## ##]&hellip;[/swf] &mdash; insert SWF clip<br />
");
}

register("bbcodes", "SWF_Code");
register("pageHeader", "SWF_JS");
register("postHelp", "SWF_Help", 1);

?>