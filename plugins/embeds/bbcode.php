<?php

$bbcodeCallbacks["youtube"] = "bbcodeYoutube";
$bbcodeCallbacks["swf"] = "bbcodeFlash";
$bbcodeCallbacks["video"] = "bbcodeVideo";
$bbcodeCallbacks["tindeck"] = "bbcodeTindeck";
$bbcodeCallbacks["svg"] = "bbcodeSvg";
$tagParseStatus["swf"] = 2;
$tagParseStatus["youtube"] = 2;
$tagParseStatus["video"] = 2;
$tagParseStatus["tindeck"] = 2;
$tagParseStatus["svg"] = 2;



function getYoutubeIdFromUrl($url) {
    $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}

function bbcodeYoutube($contents, $arg)
{
	$contents = trim($contents);
	$id = getYoutubeIdFromUrl($contents);
	if($id)
		$contents = $id;
		
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
		$height = $args[1];
	}

	return "
	<div class=\"swf\" style=\"width: ".($width + 4)."px;\">
		<div class=\"swfmain\" id=\"swf{$flashloops}main\" style=\"width: {$width}px; height: {$height}px;\">
		</div>
		<div class=\"swfcontrol\">
			<button type=\"button\" style=\"height:25px\" class=\"startFlash\" id=\"swfa$flashloops\">&#x25BA;</button>
			<button type=\"button\" style=\"height:25px\" class=\"stopFlash\" id=\"swfb$flashloops\">&#x25A0;</button>
			<span style=\"display:none;\" id=\"swf{$flashloops}url\">".htmlspecialchars($contents)."</span>
		</div>
	</div>";
}
?>
