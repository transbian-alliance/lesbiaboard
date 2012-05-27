<?php

$bbcodeCallbacks["youtube"] = "bbcodeYoutube";
$bbcodeCallbacks["swf"] = "bbcodeFlash";
$bbcodeCallbacks["video"] = "bbcodeVideo";
$bbcodeCallbacks["tindeck"] = "bbcodeTindeck";
$bbcodeCallbacks["svg"] = "bbcodeSvg";

function bbcodeYoutube($contents, $arg)
{
	$contents = trim($contents);
	if(!preg_match("/^[\-0-9_a-zA-Z]+$/", $contents))
		return "[Invalid youtube video ID]";
	
	$args = "";

	if($arg == "loop")
		$args .= "&amp;loop=1";

	return "<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/$contents&amp;hl=en&amp;fs=1$args\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"never\"></param><embed src=\"http://www.youtube.com/v/$contents&amp;hl=en&amp;fs=1$args\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"never\" allowfullscreen=\"false\" width=\"425\" height=\"344\"></embed></object>";
}

function bbcodeVideo($contents, $arg)
{
	return "<video src=\"$contents\" width=\"425\" height=\"344\"  controls=\"controls\">Video not supported &mdash; <a href=\"$contents\">download</a></video>";
}

function bbcodeTindeck($contents, $arg)
{
	return "<a href=\"http://tindeck.com/listen/$contents\"><img src=\"http://tindeck.com/image/$contents/stats.png\" alt=\"Tindeck\" /></a>";
}

function bbcodeSvg($contents, $arg)
{
	$svgin="'<?xml version=\"1.0\"?>"
		  ."<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" "
	  ."\"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">"
	  ."<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"\\1\" height=\"\\2\" onload=\"InitSMIL(evt)\">'";
	$svgout="'</svg>'";
	
	$svglist1 = array("\\\"","\\\\","\\'");
	$svglist2 = array("\"","\\","\'");

	//I don't even understand what this code is even doing. 
	//I wouldn't be surprised if it doesnt work ~Dirbaio
	
	return '\''."<embed src=\"data:image/svg+xml;base64,"
  .'\''.".base64_encode($svgin.".'str_replace($svglist1,$svglist2,\'\\3\')'.".$svgout).".'"'
  ."\\\" type=\\\"image/svg+xml\\\" width=".'\''.$width.'\' height=\''.$height.'\''." />\"";

}

function bbcodeFlash($contents, $arg)
{
	global $flashloops;
	$flashloops++;
	
	$width = 400;
	$height = 300;
	
	$args = explode(" ", $arg);
	if(count($args) == 2)
	{
		$width = $args[0];
		$height = $args[0];
	}
	
	if(strlen($contents) < 4)
		return "[user tried to use an URL too short to be a valid .SWF file.]";

	if(strtolower(substr($contents, -4)) !== ".swf")
		$contents .= ".swf";

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
", $width + 4, $width, $height, $contents, $flashloops);
}
?>
