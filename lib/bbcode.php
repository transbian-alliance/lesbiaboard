<?php

function parseText($text)
{
	global $parseStatus, $postNoSmilies, $postNoBr, $postPoster;
	
	if($parseStatus <= 1)
	{
		$text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');
		$text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
	}

	if($parseStatus == 0)
	{
		if(!$postNoBr)
			$text = nl2br($text);
		
		$text = postDoReplaceText($text);
	}
	
	return $text;
}

$tagParseStatus = array(
	'ul' => 1,
	'ol' => 1,
	'li' => 0,

	'table' => 1,
	'td' => 0,
	'th' => 0,

	'img' => 2,
	'imgs' => 2,
	'url' => 2,
	'code' => 2,
	'source' => 2,
	'pre' => 2,
	'style' => 2,
);

$autocloseTags = array(
	'li' => array('li', 'ul', 'ol'),
	'td' => array('td', 'tr', 'trh', 'table'),
	'tr' => array('tr', 'trh', 'table'),
	'trh' => array('tr', 'trh', 'table'),
);

$heavyTags = array(
	'code', 'source', 'pre'
);

$singleTags = array(
	'user', 'forum', 'thread',
);
$singleHtmlTags = array(
	'p', 'br', 'img', 'link',
);

$goodHtmlTags = array(
	'a', 'b', 'big', 'br', 'button', 'center', 'code', 'dd', 'del', 'div', 'dl', 'dt', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'img', 'input', 'kbd', 'li', 'ol', 'p', 'pre', 's', 'small', 'span', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'tr', 'u', 'ul', 'link'
);


function tokenValidTag($tagname, $bbcode)
{
	global $badTags, $bbcodeCallbacks, $goodHtmlTags;
	
	if(!$bbcode && in_array(trim($tagname), $badTags))
		return false;
	
	if($bbcode && !array_key_exists($tagname, $bbcodeCallbacks))
			return false;

	if(!$bbcode && !in_array($tagname, $goodHtmlTags))
		return false;
	
	return 
		false === strpos($tagname, '>') &&
		false === strpos($tagname, '<') &&
		false === strpos($tagname, '[') &&
		false === strpos($tagname, ']');
}

function parseToken($token)
{
	$type = 0;
	$match = array();
	$inregex = "(\w+)=?(.*)";
		
	if(preg_match('@^\\[/'.$inregex.'\]$@', $token, $match))
		$type = 2;
	else if(preg_match('@^\\['.$inregex.'\\]$@', $token, $match))
		$type = 1;
	else if(preg_match("@^</$inregex>$@", $token, $match))
		$type = 4;
	else if(preg_match("@^<$inregex>$@", $token, $match))
		$type = 3;
		
	if($type == 0)
		return array(
			'type' => 0,
			'text' => $token
		);
	
	$tagname = strtolower($match[1]);
	$attrs = trim($match[2]);

	if(!tokenValidTag($tagname, $type < 3))
		return array(
			'type' => 0,
			'text' => $token
		);

	return array(
		'type' => $type,
		'tag' => $tagname,
		'text' => $token,
		'attributes' => $attrs
	);
}

function parse($parentToken)
{
	global $tokens, $tokenPtr, $heavyTags, $singleTags, $singleHtmlTags, $tagParseStatus, $parseStatus, $bbcodeCallbacks, $allowTables, $autocloseTags, $bbcodeIsTableHeader;
	
	$parentTag = $parentToken['tag'];

	//Single tags just can't/aren't supposed to be closed, like [user=xx]	
	if($parentToken['type'] == 1)
		$singleTag = in_array($parentTag, $singleTags);
	else
		$singleTag = in_array($parentTag, $singleHtmlTags);

	$finished = $singleTag;
	
	//Heavy tags just put everything as text until close tag.
	$heavyTag = $parentToken != 0 && in_array($parentTag, $heavyTags);
	
	//Backup parse status
	$oldParseStatus = $parseStatus;
	$oldAllowTables = $allowTables;
	
	//Force parse status if tag wants to.
	if($parentToken != 0)
		if(array_key_exists($parentTag, $tagParseStatus))
			$parseStatus = $tagParseStatus[$parentTag];

	if(($parentToken['type'] == 3 || $parentToken['type'] == 1) && $parentTag == 'table')
		$allowTables = true;
	
	if($parentTag == 'trh')
		$bbcodeIsTableHeader = true;

	while($tokenPtr < count($tokens) && !$finished)
	{
		$token = $tokens[$tokenPtr++];
		
		$printAsText = false;
		$result = '';
		switch($token['type'])
		{
			case 0: //Text
				$printAsText = true;
				break;
			case 1: //BBCode open
			case 3: //HTML open
				if($parentToken['type'] == $token['type']
						&& isset($autocloseTags[$parentTag]) 
						&& in_array($token['tag'], $autocloseTags[$parentTag]))
				{
//					$result .= "[AUTO]";
					$finished = true;
					$tokenPtr--;
				}
				else if(!$allowTables && ($token['tag'] == 'td' || $token['tag'] == 'tr' || $token['tag'] == 'th'))
					$printAsText = true;
				else
					if(!$heavyTag)
						$result .= parse($token);
				break;
				
			case 2: //BBCode close
			case 4: //HTML close
				if($parentToken != 0 && $parentToken['type']+1 == $token['type'] && $token['tag'] == $parentTag)
					$finished = true;
				else if($parentToken != 0 
						&& $parentToken['type']+1 == $token['type']
						&& isset($autocloseTags[$parentTag])
						&& in_array($token['tag'], $autocloseTags[$parentTag]))
				{
//					$result .= "[AUTO]";
					$finished = true;
					$tokenPtr--;
				}
				else
					$printAsText = true;
				break;
		}
		
		if($heavyTag && !$finished)
			$printAsText = true;
		
		if($printAsText)
			$textcontents .= $token['text'];
		else
		{
			if($textcontents)
				$contents .= parseText($textcontents);
			$textcontents = '';
			$contents .= $result;
		}
	}

	if($parentTag == 'trh')
		$bbcodeIsTableHeader = false;

	if($textcontents)
		$contents .= parseText($textcontents);

	//Restore saved parse status.
	$parseStatus = $oldParseStatus;
	$allowTables = $oldAllowTables;
	
	if($parentToken == 0)
		return $contents;
	
	if($parentToken['type'] == 1) //BBCode
	{
		$func = $bbcodeCallbacks[$parentTag];
		if($func)
			return $func($contents, $parentToken['attributes']);
		else
			return $contents;
	}
	else if($parentToken['type'] == 3) //HTML
	{
		if($singleTag)
			return '<'.$parentTag.' '.$parentToken['attributes'].'>';
		else
			return '<'.$parentTag.' '.$parentToken['attributes'].'>'.$contents.'</'.$parentTag.'>';
	}
	else return 'WTF?';
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
	
	$tokens = preg_split('/(\[(?:\w+(?:=".*?"|=[^]]*)?|\/\w+)\]|<[^\[\]<>]+>)/S', $text, 0, PREG_SPLIT_DELIM_CAPTURE);
	$tokenPtr = 0;
	$tokens = array_map('parseToken', $tokens);
	return parse(0);
}

