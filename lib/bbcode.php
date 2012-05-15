<?php



function parseText($text)
{
	global $parseStatus;
	
	//Parse smilies and such
	if($parseStatus <= 1)
		$text = htmlspecialchars($text);
	if($parseStatus == 0)
	{
		$text = nl2br($text);
		//Smilies here
	}
	
	return $text;
}

$tagParseStatus = array(
	"ul" => 1,
	"ol" => 1,
	"li" => 0,

	"table" => 1,
	"td" => 0,
	"th" => 0,

	"img" => 2,
	"code" => 2,
	"source" => 2,
	"pre" => 2,
);

$heavyTags = array(
	"code", "source", "pre"
);

$singleTags = array(
	"code", "source", "pre"
);


function tokenValidTag($tagname, $bbcode)
{
	global $badTags;
	
	if(!$bbcode && in_array(trim(strtolower($tagname)), $badTags))
		return false;
	
	return 
		false === strpos($tagname, ">") &&
		false === strpos($tagname, "<") &&
		false === strpos($tagname, "[") &&
		false === strpos($tagname, "]");
}

function parseToken($token)
{
	if(substr($token, 0, 2) == "[/" && substr($token, strlen($token)-1, 1) == "]")
	{
		$tagname = substr($token, 2, strlen($token)-3);

		if(!tokenValidTag($tagname, true))
			return array("type" => 0, "text" => $token);

		return array(
			"type" => 2,
			"tag" => $tagname,
			"text" => $token
		);		
	}
	if(substr($token, 0, 1) == "[" && substr($token, strlen($token)-1, 1) == "]")
	{
		$tagname = substr($token, 1, strlen($token)-2);

		if(!tokenValidTag($tagname, true))
			return array("type" => 0, "text" => $token);

		$arg = "";
		$ind = strpos($tagname, "=");
		if($ind)
		{
			$arg = substr($tagname, $ind+1);
			$tagname = substr($tagname, 0, $ind);
		}
		
		return array(
			"type" => 1,
			"tag" => $tagname,
			"text" => $token,
			"attributes" => $arg
		);
	}
	if(substr($token, 0, 2) == "</" && substr($token, strlen($token)-1, 1) == ">")
	{
		$tagname = substr($token, 2, strlen($token)-3);
		if(!tokenValidTag($tagname, false))
			return array("type" => 0, "text" => $token);
		return array(
			"type" => 4,
			"tag" => $tagname,
			"text" => $token
		);		
	}
	if(substr($token, 0, 1) == "<" && substr($token, strlen($token)-1, 1) == ">")
	{
		$tagname = substr($token, 1, strlen($token)-2);
		if(!tokenValidTag($tagname, false))
			return array("type" => 0, "text" => $token);
		$arg = "";
		$ind = strpos($tagname, " ");
		if($ind)
		{
			$arg = substr($tagname, $ind+1);
			$tagname = substr($tagname, 0, $ind);
		}
		
		return array(
			"type" => 3,
			"tag" => $tagname,
			"text" => $token,
			"attributes" => $arg
		);
	}
	return array(
		"type" => 0,
		"text" => $token
	);
}

function parse($parenttoken)
{
	global $tokens, $tokenPtr, $heavyTags, $tagParseStatus, $parseStatus, $bbcodeCallbacks;
	
	$contents = "";
	$finished = false;
	
	$textContents = "";
	
	$thistag = $parenttoken["tag"];
	
	//Heavy tags just put everything as text until lol.
	$heavyTag = $parenttoken != 0 && in_array($thistag, $heavyTags);
	
	//Backup parse status
	$oldParseStatus = $parseStatus;
	
	//Force parse status if tag wants to.
	if($parenttoken != 0)
		if(array_key_exists($thistag, $tagParseStatus))
		{
			$parseStatus = $tagParseStatus[$thistag];
//			$contents .= "[".$parenttoken["tag"]." ".$parseStatus."]";
		}
	
	while($tokenPtr < count($tokens) && !$finished)
	{
		$token = $tokens[$tokenPtr++];
		
		$printAsText = false;
		$result = "";
		switch($token["type"])
		{
			case 0: //Text
				$printAsText = true;
				break;
			case 1: //BBCode open
				if(!$heavyTag)
					$result .= parse($token);
				break;
			case 3: //HTML open
				if(!$heavyTag)
					$result .= parse($token);
				break;
			case 2: //BBCode close
			case 4: //HTML close
				if($parenttoken != 0 && $token["tag"] == $parenttoken["tag"] && $token["type"] == $parenttoken["type"]+1)
					$finished = true;
				else
					$printAsText = true;
			//TODO HTML
		}
		
		if($heavyTag && !$finished)
			$printAsText = true;
		
		if($printAsText)
			$textcontents .= $token["text"];
		else
		{
			$contents .= parseText($textcontents);
			$textcontents = "";
			$contents .= $result;
		}
	}

	$contents .= parseText($textcontents);
	$textcontents = "";

	//Restore saved parse status.
	$parseStatus = $oldParseStatus;
	
	if($parenttoken == 0)
		return $contents;
	else if($parenttoken["type"] == 1) //BBCode
	{
		$func = $bbcodeCallbacks[$thistag];
		if($func)
			return $func($contents, $parenttoken["attributes"]);
		else
			return $contents;
	}
	else if($parenttoken["type"] == 3) //HTML
	{
		$attrs = "";
		if($parenttoken["attributes"] != "")
			$attrs = " ".$parenttoken["attributes"];
		//Now we gotta do something with the tag in here.
		return "<".$thistag.$attrs.">".$contents."</".$thistag.">";
	}
	else return "WTF?";
}

/* 
$parsestatus: 
0 - HTML Entites, Smilies. nl2br
1 - HTML Entites
2 - nothing.
*/

function parseBBCode($text)
{
	global $tokens, $tokenPtr, $parseStatus;
	
	$parseStatus = 0;
	
	$tokens = preg_split("/(\[[^\[\]<>]+\]|<[^\[\]<>]+>)/", $text, 0, PREG_SPLIT_DELIM_CAPTURE);
	$tokenPtr = 0;
	$tokens = array_map("parseToken", $tokens);
	return parse(0);
}



//===================

//Not recursive version below.
//I don'tlike it though. It's way less flexible. And uses an array as stack...
//So screw it.

/*
$stack = array();
$tagcount = array();

function openTag($token)
{
	global $tagcount;
	$tagcount[$token["tag"]]++;
}
function closeTag($token)
{
	global $tagcount;
	$tagcount[$token["tag"]]--;
}

foreach($tokens as $ind => $token)
{
	$good = true;
	switch($token["type"])
	{
		case 0:
			print $token["text"];
			break;
		case 1:
			array_push($stack, $token["tag"]);
			openTag($token);
			break;
		case 2:
			if(count($stack) == 0)
				break;
			
			$top = $stack[count($stack)-1];
			if($top != $token["tag"])
				break;

			closeTag($token);
			array_pop($stack);
			break;
	}
	
}

while(count($stack) != 0)
{
	$tagname = array_pop($stack);
	
	closeTag(array(
		"type" => 2,
		"tag" => $tagname,
		"text" => "[/".$tagname."]";
	));
}
*/
