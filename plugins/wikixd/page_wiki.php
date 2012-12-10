<?php

require 'wikilib.php';

$page = getWikiPage($_GET['id']);

$urltitle = urlencode($page['id']);
$nicetitle = htmlspecialchars(url2title($page['id']));
$title = 'Wiki &raquo; '.$nicetitle;

if ($page['istalk']) 
	$links .= actionLinkTagItem('Page', 'wiki', substr($urltitle,7)).'<li>Discuss</li>';
else
	$links .= '<li>Page</li>'.actionLinkTagItem('Discuss', 'wiki', 'Talk%3A'.$urltitle);

if ($page['canedit'])
	$links .= actionLinkTagItem('Edit', 'wikiedit', $urltitle);

if ($page['ismain'])
	MakeCrumbs(array('Wiki'=>actionLink('wiki')), $links);
else
	MakeCrumbs(array('Wiki'=>actionLink('wiki'), url2title($nicetitle)=>actionLink('wiki', $urltitle)), $links);

echo '
		<table class="outline margin">
			<tr class="cell1">
				<td style="padding:0px 1em 1em;">';
	
if ($page['flags'] & WIKI_PFLAG_DELETED)
{
	echo '<h1>'.$nicetitle.'</h1>This page has been deleted.';
}
else if ($page['new'])
{
	echo '<h1>'.$nicetitle.'</h1>This page does not exist.';
	if ($page['canedit']) echo '<br><br>'.actionLinkTag('Create it now', 'wikiedit', $urltitle);
}
else
{
	echo '<h1>'.$nicetitle.'</h1>'.wikiFilter($page['text'], $page['flags'] & WIKI_PFLAG_NOCONTBOX);
}

echo '
				</td>
			</tr>
		</table>';

?>