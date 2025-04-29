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
		'pre' => 'bbcodeUrlPre',
	),
	'img' => array(
		'callback' => 'bbcodeImage',
		'pre' => true,
	),
	'video' => array(
		'callback' => 'bbcodeVideo',
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
		'br'       => true,
	),
	'reply' => array(
		'callback' => 'bbcodeReply',
		'br'       => true,
	),

	'spoiler' => array(
		'callback' => 'bbcodeSpoiler',
		'br'       => true,
	),

	'table' => array(
		'callback'  => 'bbcodeTable',
		'stopclose' => true,
	),
	'tr' => array(
		'callback'  => 'bbcodeTableRow',
		'selfclose' => 'tr',
		'require' => array('table'),
	),
	'trh' => array(
		'callback'  => 'bbcodeTableRowHeader',
		'selfclose' => 'tr',
		'require' => array('table'),
	),
	'td' => array(
		'callback'  => 'bbcodeTableCell',
		'selfclose' => 'td',
		'require' => array('tr', 'trh'),
	),
  
	'code' => array(
		'callback' => 'bbcodeCode',
		'br'       => false,
    'pre'      => true,
	),
);

//Allow plugins to register their own callbacks (new bbcode tags)
$bucket = "bbcode"; include("pluginloader.php");

function bbcodeAppend($dom, $nodes)
{
	foreach (iterator_to_array($nodes) as $node)
		$dom->appendChild($node);
}

function domToString($dom)
{
	if ($dom instanceof DOMNodeList)
	{
		$result = "";
		foreach ($dom as $elem)
			$result .= domToString($elem);
		return $result;
	}
	return $dom->ownerDocument->saveHTML($dom);
}

function markupToMarkup($dom, $markup)
{
	$markup_dom = new DOMDocument;
	$markup_dom->encoding = 'UTF-8';
	$markup_dom->preserveWhiteSpace = true;
	$markup_dom->substituteEntities = true;
	$markup_dom->strictErrorChecking = false;
	$markup_dom->loadHTML("<?xml encoding='UTF-8'>$markup");

	$nodes = $markup_dom->getElementsByTagName('body')->item(0)->childNodes;
	$result = array();
	foreach ($nodes as $node)
		$result[] = $dom->importNode($node, true);

	return $result;
}

// DOM is stupid enough to not let change the name of the element.
function renameTag(DOMElement $oldTag, $newTagName)
{
    $document = $oldTag->ownerDocument;

    $newTag = $document->createElement($newTagName);
    $oldTag->parentNode->replaceChild($newTag, $oldTag);

    foreach ($oldTag->attributes as $attribute)
        $newTag->setAttribute($attribute->name, $attribute->value);

    foreach (iterator_to_array($oldTag->childNodes) as $child)
        $newTag->appendChild($oldTag->removeChild($child));

    return $newTag;
}

function parseQuoteLike($quote, $i = 0, $full = false)
{
	if ($quote[$i] === '"')
	{
		$pos = strpos($quote, '"', $i + 1);
		if ($pos)
			return array(
				'pos'    => $pos + 1,
				'substr' => substr($quote, $i + 1, $pos - $i - 1),
			);
	}
	elseif ($quote[$i] === "'")
	{
		$pos = strpos($quote, "'", $i + 1);
		if ($pos)
			return array(
				'pos'    => $pos + 1,
				'substr' => substr($quote, $i + 1, $pos - $i - 1),
			);
	}
	// If not, then it's unquoted
	if ($full)
		return array(
			'pos'    => strlen($quote),
			'substr' => substr($quote, $i),
		);
	else
	{
		$pos = strpos($quote, ' ', $i);
		if (!$pos) $pos = strlen($quote);
		return array(
			'pos'    => $pos,
			'substr' => substr($quote, $i, $pos - $i),
		);
	}
}

function bbcodeNullIfArg($arg) {
    return $arg !== NULL;
}

function bbcodeUrlPre($arg) {
	return $arg === NULL;
}

function bbcodeBold($dom, $nodes)
{
	$b = $dom->createElement('b');
	bbcodeAppend($b, $nodes);
	return $b;
}

function bbcodeItalic($dom, $nodes)
{
	$i = $dom->createElement('i');
	bbcodeAppend($i, $nodes);
	return $i;
}

function bbcodeUnderline($dom, $nodes)
{
	$u = $dom->createElement('u');
	bbcodeAppend($u, $nodes);
	return $u;
}

function bbcodeStrikethrough($dom, $nodes)
{
	$s = $dom->createElement('s');
	bbcodeAppend($s, $nodes);
	return $s;
}

function bbcodeURL($dom, $nodes, $arg)
{
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
	$a->setAttribute('target', '_blank');
	return $a;
}

function bbcodeImage($dom, $nodes, $arg)
{
	if ($arg === NULL) { // img with url inside tag
		$url = $nodes;
	} else { // img with url in arg and description inside tag
		$url = $arg;
		$desc = $nodes;
	}
	$a = $dom->createElement('a');
	$a->setAttribute('target', '_blank');
	$a->setAttribute('href', $url);
	$a->setAttribute('title', $desc);
	$img = $dom->createElement('img');
	$img->setAttribute('src', $url);
	$img->setAttribute('alt', $desc);
	$a->setAttribute('class', 'imgtag');
	$a->appendChild($img);
	return $a;
}

function bbcodeVideo($dom, $nodes, $arg)
{
	// match youtube
	$yt_match = '/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
	$matches = array();
	preg_match($yt_match, $nodes, $matches);
	if(!empty($matches)) {
		$iframe = $dom->createElement('iframe');
		$iframe->setAttribute('width', '640');
		$iframe->setAttribute('height', '360');
		$iframe->setAttribute('frameborder', '0');
		$iframe->setAttribute('src', 'https://www.youtube.com/embed/'.$matches[1]);
		return $iframe;
	}

	// match streamable
	$streamable_match = '/(?:streamable\.com\/)([a-z0-9]+)$/';
	$matches = array();
	preg_match($streamable_match, $nodes, $matches);
	if(!empty($matches)) {
		$iframe = $dom->createElement('iframe');
		$iframe->setAttribute('width', '640');
		$iframe->setAttribute('height', '360');
		$iframe->setAttribute('frameborder', '0');
		$iframe->setAttribute('src', 'https://streamable.com/e/'.$matches[1]);
		return $iframe;
	}
	
	// not a known video -- try to embed with <video>
	$video = $dom->createElement('video');
	$video->setAttribute('controls', 'true');
	$video->setAttribute('muted', 'true');
	$video->setAttribute('preload', 'metadata');
	$video->setAttribute('src', $nodes);
	return $video;
}

function bbcodeUser($dom, $nothing, $id)
{
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
						WHERE t.id={0} AND ".forumAccessControlSql(), $id);
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

function bbcodeQuote($dom, $nodes, $arg, $attrs)
{
	return bbcodeQuoteGeneric($dom, $nodes, $arg, $attrs, __("Posted by"));
}

function bbcodeReply($dom, $nodes, $arg, $attrs)
{
	return bbcodeQuoteGeneric($dom, $nodes, $arg, $attrs, __("Sent by"));
}

function bbcodeQuoteGeneric($dom, $nodes, $arg, $attrs, $text)
{
	$div = $dom->createElement('div');
	$div->setAttribute('class', 'quote');
	if ($arg !== NULL)
	{
		$header = $dom->createElement('div');
		$header->setAttribute('class', 'quoteheader');

		$user_name = $dom->createTextNode($arg);
		if ($attrs['borked'])
		{
			$quote = parseQuoteLike($arg);
			$continue = $quote['pos'];
			$name = $quote['substr'];
			if (preg_match('/^\s*id\s*=\s*/', substr($arg, $continue), $matches))
			{
				$quote = parseQuoteLike($arg, strlen($matches[0]) + $continue, true);
				$id = (int) $quote['substr'];
				$user_name = $dom->createElement('a');
				$user_name->setAttribute('href', actionLink("post", $id));
				$user_name->appendChild($dom->createTextNode($name));
				$arg = $name;
			}
		}
		$header->appendChild($dom->createTextNode("$text "));
		$header->appendChild($user_name);
		$div->appendChild($header);
	}
	$content = $dom->createElement('div');
	$content->setAttribute('class', 'quotecontent');
	bbcodeAppend($content, $nodes);
	$div->appendChild($content);
	return $div;
}

function bbcodeSpoiler($dom, $nodes, $arg)
{
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

function bbcodeTable($dom, $nodes)
{
	$table = $dom->createElement('table');
	$table->setAttribute('class', 'outline margin');
	bbcodeAppend($table, $nodes);
	return $table;
}

function bbcodeTableRow($dom, $nodes)
{
	static $i = 0;
	$i = ($i + 1) % 2;
	$tr = $dom->createElement('tr');
	$tr->setAttribute('class', "cell$i");
	bbcodeAppend($tr, $nodes);
	return $tr;
}

function bbcodeTableRowHeader($dom, $nodes)
{
	$tr = $dom->createElement('tr');
	$tr->setAttribute('class', 'header0');
	bbcodeAppend($tr, $nodes);
	foreach (iterator_to_array($tr->childNodes) as $node)
		if ($node->tagName === 'td')
			renameTag($node, 'th');
	return $tr;
}

function bbcodeTableCell($dom, $nodes)
{
	$td = $dom->createElement('td');
	bbcodeAppend($td, $nodes);
	return $td;
}

function bbcodeCode($dom, $nodes)
{
  // code part in <pre>
	$pre = $dom->createElement('pre');
	$pre->appendChild($dom->createTextNode($nodes));
  
  // label
  $span = $dom->createElement('span');
  $span->appendChild($dom->createTextNode(__("Code:")));
  
  // div wrapper for prettiness
  $div = $dom->createElement('div');
	$div->setAttribute('class', "codeblock");
  $div->appendChild($span);
  $div->appendChild($pre);
  
	return $div;
}
