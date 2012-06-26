<?php

$bbcodeCallbacks = array(
	"b" => "bbcodeBold",
	"i" => "bbcodeItalics",
	"u" => "bbcodeUnderline",
	"s" => "bbcodeStrikethrough",
	
	"url" => "bbcodeURL",
	"img" => "bbcodeImage",
	
	"user" => "bbcodeUser",
	"thread" => "bbcodeThread",
	"forum" => "bbcodeForum",
	
	"quote" => "bbcodeQuote",
	"reply" => "bbcodeReply",
	
	"spoiler" => "bbcodeSpoiler",
	"code" => "bbcodeCode",
	"source" => "bbcodeCode",
);

//Allow plugins to register their own callbacks (new bbcode tags)
$bucket = "bbcode"; include("pluginloader.php");

function bbcodeBold($contents){
	return "<strong>$contents</strong>";
}
function bbcodeItalics($contents){
	return "<em>$contents</em>";
}
function bbcodeUnderline($contents){
	return "<u>$contents</u>";
}
function bbcodeStrikethrough($contents){
	return "<del>$contents</del>";
}

function bbcodeURL($contents, $arg)
{
	$dest = $contents;
	$title = $contents;

	if($arg)
		$dest = htmlentities($arg);
	
	return '<a href="'.$dest.'">'.$title.'</a>';
}

function bbcodeImage($contents, $arg)
{
	$dest = $contents;
	$title = "";
	if($arg)
	{
		$title = $contents;
		$dest = $arg;
	}
	
	return '<img class="imgtag" src="'.htmlentities($dest).'" alt="'.$title.'"/>';
}


function bbcodeUser($contents, $arg)
{
	global $members;
	$id = (int)$arg;
	if(!isset($members[$id]))
	{
		$rUser = Query("select id, name, displayname, powerlevel, sex from users where id={0}", $id);
		if(NumRows($rUser))
			$members[$id] = Fetch($rUser);
		else
			return UserLink(array('id' => 0, 'name' => "Unknown User", 'sex' => 0, 'powerlevel' => -1));
	}
	return UserLink($members[$id]);
}

function bbcodeThread($contents, $arg)
{
	global $threadLinkCache;
	$id = (int)$arg;
	if(!isset($threadLinkCache[$id]))
	{
		$rThread = Query("select id, title from threads where id={0}", $id);
		if(NumRows($rThread))
		{
			$thread = Fetch($rThread);
			$threadLinkCache[$id] = actionLinkTag($thread['title'], "thread", $thread['id']);
		}
		else
			$threadLinkCache[$id] = "&lt;invalid thread ID&gt;";
	}
	return $threadLinkCache[$id];
}

function bbcodeForum($contents, $arg)
{
	global $forumLinkCache;
	$id = (int)$arg;
	if(!isset($forumLinkCache[$id]))
	{
		$rForum = Query("select id, title from forums where id={0}", $id);
		if(NumRows($rForum))
		{
			$forum = Fetch($rForum);
			$forumLinkCache[$id] = actionLinkTag($forum['title'], "forum", $forum['id']);
		}
		else
			$forumLinkCache[$id] = "&lt;invalid forum ID&gt;";
	}
	return $forumLinkCache[$id];
}

function bbcodeQuote($contents, $arg)
{
	return bbcodeQuoteGeneric($contents, $arg, __("Posted by"));
}

function bbcodeReply($contents, $arg)
{
	return bbcodeQuoteGeneric($contents, $arg, __("Sent by"));
}

function bbcodeQuoteGeneric($contents, $arg, $text)
{
	if(!$arg)
		return "<div class='quote'><div class='quotecontent'>$contents</div></div>";
	
	$arg = explode(" ", $arg);
	
	$who = $arg[0];
	$who = str_replace('"', '', $who);
	if(count($arg) == 2)
	{
		$id = $arg[1];
		$id = str_replace('"', '', $id);
		$id = substr($id, 3);
		$id = (int)$id;
		return "<div class='quote'><div class='quoteheader'>$text <a href=\"thread.php?pid=$id#$id\">$who</a></div><div class='quotecontent'>$contents</div></div>";
	}
	else
		return "<div class='quote'><div class='quoteheader'>$text $who</div><div class='quotecontent'>$contents</div></div>";
}

function bbcodeSpoiler($contents, $arg)
{
	if($arg)
		return "<div class=\"spoiler\"><button class=\"spoilerbutton named\">$arg</button><div class=\"spoiled hidden\">$contents</div></div>";
	else
		return "<div class=\"spoiler\"><button class=\"spoilerbutton\">Show spoiler</button><div class=\"spoiled hidden\">$contents</div></div>";
}

function bbcodeCode($contents, $arg)
{
	if(!$arg)
	{
		return '<div class="codeblock">'.htmlentities($contents).'</div>';
	}
	else
	{
		$language = $arg;
		$geshi = new GeSHi(trim($contents), $language, null);
		$geshi->set_header_type(GESHI_HEADER_NONE);
		$geshi->enable_classes();
		$geshi->enable_keyword_links(false);
		
		$code = str_replace("\n", "", $geshi->parse_code());
		$code = decodeCrapEntities($code);
		return "<div class=\"codeblock geshi\">$code</div>";
	}
}

//I hoped to be able to keep the new parser free from hax :(
//But it's not possible.
//Or is it? ~Dirbaio
function decodeCrapEntities($s)
{
	// parse entities
	$s = preg_replace_callback(
		"/&#(\\d+);/u",
		"_pcreEntityToUtf",
		$s
	);

	return $s;
}

function _pcreEntityToUtf($matches)
{
	$char = intval(is_array($matches) ? $matches[1] : $matches);

	if ($char < 0x80)
	{
		// to prevent insertion of control characters
		if ($char >= 0x20) return htmlspecialchars(chr($char));
		else return "&#$char;";
	}
	
	/*
	else if ($char < 0x8000)
	{
		return chr(0xc0 | (0x1f & ($char >> 6))) . chr(0x80 | (0x3f & $char));
	}
	else
	{
		return chr(0xe0 | (0x0f & ($char >> 12))) . chr(0x80 | (0x3f & ($char >> 6))). chr(0x80 | (0x3f & $char));
	}*/
}
