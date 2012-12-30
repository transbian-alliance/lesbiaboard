<?php

// Misc things that get replaced in text.

function loadSmilies($byOrder = FALSE)
{
	global $smilies, $smiliesOrdered, $smiliesReplaceOrig, $smiliesReplaceNew;

	if(isset($smilies))
		return;

	$rSmilies = Query("select * from {smilies} order by length(code) desc");
	$smilies = array();

	while($smiley = Fetch($rSmilies))
		$smilies[] = $smiley;

	$smiliesReplaceOrig = $smiliesReplaceNew = array();
	for ($i = 0; $i < count($smilies); $i++)
	{
		$smiliesReplaceOrig[] = "/(?<!\w)".preg_quote(htmlspecialchars($smilies[$i]['code']), "/")."(?!\w)/";
		$smiliesReplaceNew[] = "<img class=\"smiley\" alt=\"\" src=\"".resourceLink("img/smilies/".$smilies[$i]['image'])."\" />";
	}
}

function makeUserAtLink($matches)
{
	global $members;
	$username = $matches[1];
	foreach($members as $id => $data)
	{
		if($data['name'] == $username)
		{
			return UserLink($members[$data['id']]);
		}
	}
	//Didn't find it in the cache.
	$rUser = Query("select u.(_userfields) from {users} u where name={0} or displayname={0}", $username);
	if(NumRows($rUser))
	{
		$hit = getDataPrefix(Fetch($rUser), "u_");
		$members[$hit['id']] = $hit;
		return UserLink($hit);
	}
	else
		return $username; //Return the actual name attempted.
}

//Main post text replacing.
function postDoReplaceText($s)
{
	global $postNoSmilies, $postNoBr, $postPoster, $smiliesReplaceOrig, $smiliesReplaceNew;

//	$s = preg_replace_callback("'@\"([\w ]+)\"'si", "MakeUserAtLink", $s);
//	$s = preg_replace("'>>([0-9]+)'si",">>".actionLinkTag("\\1", "thread", "", "pid=\\1#\\1"), $s);
	if($postPoster)
		$s = preg_replace("'/me '","<b>* ".$postPoster."</b> ", $s);

	LoadSmilies();

	//Smilies
	if(!$postNoSmilies)
		$s = preg_replace($smiliesReplaceOrig, $smiliesReplaceNew, $s);

//Macros system WILL be replaced by smilies.
/*	include("macros.php");
	foreach($macros as $macro => $img)
		$s = str_replace(":".$macro.":", "<img src=\"img/macros/".$img."\" alt=\":".$macro.":\" />", $s);
*/
	$s = preg_replace_callback('((?:(?:view-source:)?(?:[Hh]t|[Ff])tps?://(?:(?:[^:&@/]*:[^:@/]*)@)?|\bwww\.)[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*(?::[0-9]+)?(?:/(?:->(?=\S)|&amp;|[\w\-/%?=+#~:\'@*^$!]|[.,;\'|](?=\S)|(?:(\()|(\[)|\{)(?:->(?=\S)|[\w\-/%&?=+;#~:\'@*^$!.,;]|(?:(\()|(\[)|\{)(?:->(?=\S)|l[\w\-/%&?=+;#~:\'@*^$!.,;])*(?(3)\)|(?(4)\]|\})))*(?(1)\)|(?(2)\]|\})))*)?)', 'bbcodeURLAuto', $s);

	$bucket = "postMangler"; include("./lib/pluginloader.php");

	return $s;
}

