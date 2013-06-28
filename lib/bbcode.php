<?php
$bbcode = array(
	'b' => array(
		'callback' => 'bbcodeBold',
	),
	'i' => array(
		'callback' => 'bbcodeItalic',
	),
	'u' => array(
		'callback' => 'bbcodeUnderline',
	),
	's' => array(
		'callback' => 'bbcodeStrikethrough',
	),

	'url' => array(
		'callback' => 'bbcodeURL',
		'pre' => 'bbcodeURLPRE',
	),
	'img' => array(
		'callback' => 'bbcodeImage',
		'pre' => true,
	),
	'imgs' => array(
        'callback' => 'bbcodeImageScale',
        'pre' => true,
	),

	'user' => array(
		'callback' => 'bbcodeUser',
		'void' => true,
	),

	'thread' => array(
		'callback' => 'bbcodeThread',
		'void' => true,
	),

	'forum' => array(
		'callback' => 'bbcodeForum',
		'void' => true,
	),

	'quote' => array(
		'callback' => 'bbcodeQuote',
	),
	'reply' => array(
		'callback' => 'bbcodeReply',
	),

	'spoiler' => array(
		'callback' => 'bbcodeSpoiler',
	),
);

function bbcodeAppend($dom, $nodes) {
	foreach (iterator_to_array($nodes) as $node)
		$dom->appendChild($node);
}

function domToString($dom) {
	if ($dom instanceof DOMNodeList)
	{
		$result = "";
		foreach ($dom as $elem)
			$result .= domToString($elem);
		return $result;
	}
	return $dom->ownerDocument->saveHTML($dom);
}

function markupToMarkup($dom, $markup) {
	$markup_dom = new DOMDocument;
	$markup_dom->loadHTML($markup);
	$nodes = $markup_dom->getElementsByTagName('body')->item(0)->childNodes;
	$result = array();
	foreach ($nodes as $node)
		$result[] = $dom->importNode($node, true);

	return $result;
}

function bbcodeBold($dom, $nodes) {
	$b = $dom->createElement('b');
	bbcodeAppend($b, $nodes);
	return $b;
}

function bbcodeItalic($dom, $nodes) {
	$i = $dom->createElement('i');
	bbcodeAppend($i, $nodes);
	return $i;
}

function bbcodeUnderline($dom, $nodes) {
	$u = $dom->createElement('u');
	bbcodeAppend($u, $nodes);
	return $u;
}

function bbcodeStrikethrough($dom, $nodes) {
	$s = $dom->createElement('s');
	bbcodeAppend($s, $nodes);
	return $s;
}

function bbcodeURL($dom, $nodes, $arg) {
	$a = $dom->createElement('a');
	if ($arg === NULL)
	{
		$a->setAttribute('href', $nodes);
		$a->appendChild($dom->createTextNode($nodes));
	}
	else
	{
		$a->setAttribute('href', $arg);
		bbcodeAppend($a, $nodes);
	}
	return $a;
}

function bbcodeURLPRE($attr) {
	return $attr === NULL;
}

function bbcodeImage($dom, $nodes, $title) {
	$img = $dom->createElement('img');
	$img->setAttribute('src', $nodes);
	$img->setAttribute('title', $title);
	$img->setAttribute('class', 'imgtag');
	return $img;
}

function bbcodeImageScale($dom, $nodes, $title) {
	$a = $dom->createElement('a');
	$a->setAttribute('href', $nodes);
	$img = $dom->createElement('img');
	$img->setAttribute('src', $nodes);
	$img->setAttribute('title', $title);
	$img->setAttribute('class', 'imgtag');
	$img->setAttribute('style', 'max-width:300px; max-height:300px');
	$a->appendChild($img);
	return $a;
}

function bbcodeUser($dom, $nothing, $id) {
	return markupToMarkup($dom, UserLinkById((int) $id));
}

function bbcodeThread($dom, $nothing, $arg)
{
	global $threadLinkCache, $loguser;
	$id = (int)$arg;
	if(!isset($threadLinkCache[$id]))
	{
		$rThread = Query("SELECT
							t.id, t.title
						FROM {threads} t
						LEFT JOIN {forums} f ON t.forum = f.id
						WHERE t.id={0} AND f.minpower <= {1} ", $id, $loguser["powerlevel"]);
		if(NumRows($rThread))
		{
			$thread = Fetch($rThread);
			$threadLinkCache[$id] = makeThreadLink($thread);
		}
		else
			$threadLinkCache[$id] = "&lt;invalid thread ID&gt;";
	}
	return markupToMarkup($dom, $threadLinkCache[$id]);
}

function bbcodeForum($dom, $nothing, $arg)
{
	global $forumLinkCache, $loguser;
	$id = (int)$arg;
	if(!isset($forumLinkCache[$id]))
	{
		$rForum = Query("SELECT
							id, title
						FROM {forums}
						WHERE id={0} and minpower <= {1}", $id, $loguser["powerlevel"]);
		if(NumRows($rForum))
		{
			$forum = Fetch($rForum);
			$forumLinkCache[$id] = actionLinkTag($forum['title'], "forum", $forum['id']);
		}
		else
			$forumLinkCache[$id] = "&lt;invalid forum ID&gt;";
	}
	return markupToMarkup($dom, $forumLinkCache[$id]);
}

function bbcodeQuote($dom, $nodes, $arg)
{
	return bbcodeQuoteGeneric($dom, $nodes, $arg, __("Posted by"));
}

function bbcodeReply($dom, $nodes, $arg)
{
	return bbcodeQuoteGeneric($dom, $nodes, $arg, __("Sent by"));
}

function bbcodeQuoteGeneric($dom, $nodes, $arg, $text)
{
	// TODO: Implement id="" from old BBCode implementation
	$div = $dom->createElement('div');
	$div->setAttribute('class', 'quote');
	if ($arg !== NULL)
	{
		$header = $dom->createElement('div');
		$header->setAttribute('class', 'quoteheader');
		$header->appendChild($dom->createTextNode("$text $arg"));
		$div->appendChild($header);
	}
	$content = $dom->createElement('div');
	$content->setAttribute('class', 'quotecontent');
	bbcodeAppend($content, $nodes);
	$div->appendChild($content);
	return $div;
}

function bbcodeSpoiler($dom, $nodes, $arg) {
	$spoiler = $dom->createElement('div');
	$spoiler->setAttribute('class', 'spoiler');
	$button = $dom->createElement('button');
	if ($arg === NULL)
	{
		$button->setAttribute('class', 'spoilerbutton');
		$button->appendChild($dom->createTextNode('Show spoiler'));
	}
	else
	{
		$button->setAttribute('class', 'spoilerbutton named');
		$button->appendChild($dom->createTextNode($arg));
	}
	$spoiler->appendChild($button);
	$contents = $dom->createElement('div');
	$contents->setAttribute('class', 'spoiled hidden');
	bbcodeAppend($contents, $nodes);
	$spoiler->appendChild($contents);
	return $spoiler;
}
