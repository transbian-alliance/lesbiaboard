<?php

if(!function_exists("MakeFlash"))
{
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
}

$s = preg_replace_callback("'\[swf ([0-9]+) ([0-9]+)\]([^\"]+?)\[/swf\]'si", "MakeFlash", $s);

$svgin="'<?xml version=\"1.0\"?>"
	  ."<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" "
  ."\"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">"
  ."<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"\\1\" height=\"\\2\" onload=\"InitSMIL(evt)\">'";
$svgout="'</svg>'";
$svglist1 = array("\\\"","\\\\","\\'");
$svglist2 = array("\"","\\","\'");
$s=preg_replace("'\[svg ([0-9]+) ([0-9]+)\](.*?)\[/svg\]'sie",
   '\''."<embed src=\"data:image/svg+xml;base64,"
  .'\''.".base64_encode($svgin.".'str_replace($svglist1,$svglist2,\'\\3\')'.".$svgout).".'"'
  ."\\\" type=\\\"image/svg+xml\\\" width=".'\'\\1\' height=\'\\2\''." />\"",$s);

$s = preg_replace("'\[youtube\]([\-0-9_a-zA-Z]*?)\[/youtube\]'si","<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1&amp;hl=en&amp;fs=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"never\"></param><embed src=\"http://www.youtube.com/v/\\1&amp;hl=en&amp;fs=1\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"never\" allowfullscreen=\"false\" width=\"425\" height=\"344\"></embed></object>", $s);
$s = preg_replace("'\[youtube/loop\]([\-0-9_a-zA-Z]*?)\[/youtube\]'si","<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1&amp;hl=en&amp;fs=1&amp;loop=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"never\"></param><embed src=\"http://www.youtube.com/v/\\1&amp;hl=en&amp;fs=1&amp;loop=1\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"never\" allowfullscreen=\"false\" width=\"425\" height=\"344\"></embed></object>", $s);

$s = preg_replace("'\[video\](.*?)\[/video\]'si","<video src=\"\\1\" width=\"425\" height=\"344\"  controls=\"controls\">Video not supported &mdash; <a href=\"\\1\">download</a></video>", $s);

$s = preg_replace("'\[tindeck\](.*?)\[/tindeck\]'si", "<a href=\"http://tindeck.com/listen/\\1\"><img src=\"http://tindeck.com/image/\\1/stats.png\" alt=\"Tindeck\" /></a>", $s);

?>