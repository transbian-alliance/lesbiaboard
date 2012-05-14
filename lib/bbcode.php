<?php



function parseToken($token)
{
	if(substr($token, 0, 2) == "[/" && substr($token, strlen($token)-1, 1) == "]")
	{
		$tagname = substr($token, 2, strlen($token)-3);
		return array(
			"type" => 2,
			"tag" => $tagname,
			"text" => $token
		);		
	}
	if(substr($token, 0, 1) == "[" && substr($token, strlen($token)-1, 1) == "]")
	{
		$tagname = substr($token, 1, strlen($token)-2);
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
			"arg" => $arg
		);
	}

	return array(
		"type" => 0,
		"text" => $token
	);
}

function parseText($text)
{
	//Parse smilies and such
	return $text;
}

function parse($parenttoken)
{
	global $tokens, $tokenPtr;
	
	$contents = "";
	$finished = false;
	while($tokenPtr < count($tokens) && !$finished)
	{
		$token = $tokens[$tokenPtr++];
		
		switch($token["type"])
		{
			case 0: //Text
				$contents .= parseText($token["text"]);
				break;
			case 1: //BBCode open
				$contents .= parse($token);
				break;
			case 2: //BBCode close
				if($parenttoken != 0 && $token["tag"] == $parenttoken["tag"])
					$finished = true;
					
			//TODO HTML
		}
	}

	if($parenttoken == 0)
		return $contents;
	
	//Now we gotta do something with the tag in here.
	return "<".$parenttoken["tag"].">".$contents."</".$parenttoken["tag"].">";
}

function parseBBCode($text)
{
	global $tokens, $tokenPtr;
	
	$tokens = preg_split("/(\[[^\[^\]]+\])/", $text, 0, PREG_SPLIT_DELIM_CAPTURE);
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
