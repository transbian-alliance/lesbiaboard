<?php
//Layout functions, by Nikolaj

function MakeCrumbs($path, $links)
{
	global $layout_crumbs;

	if(count($path) != 0)
	{
		$pathPrefix = array(Settings::get("breadcrumbsMainName") => actionLink("index"));
		$pathPostfix = array(); //Not sure how this could be used, but...
	
		$bucket = "breadcrumbs"; include("lib/pluginloader.php");

		$path = $pathPrefix + $path + $pathPostfix;
	}
	
	$first = true;
	
	foreach($path as $text=>$link)
	{
		if(is_array($link))
		{
			$dalink = $text;
			$tags = $link[1];
			$text = $link[0];
			$link = $dalink;
		}
		else
			$tags = "";

		$link = str_replace("&","&amp;",$link);
		
		if(!$first)
			$crumbs .= " &raquo; ";
		$first = false;
		
		if(!$tags)
			$crumbs .= "<a href=\"".$link."\">".$text."</a>";
		else if (Settings::get("tagsDirection") === 'Left')
			$crumbs .= $tags." <a href=\"".$link."\">".$text."</a>";
		else
			$crumbs .= "<a href=\"".$link."\">".$text."</a> ".$tags." &raquo; ";
	}

	if($links)
		$links = "<ul class=\"pipemenu smallFonts\">
			$links
		</ul>";
	
	$layout_crumbs = "
<div class=\"margin\">
	<div style=\"float: right;\">
		$links
	</div>
	$crumbs&nbsp;
</div>";
}

function makeThreadLink($thread)
{
	$tags = ParseThreadTags($thread["title"]);
	
	$link = actionLinkTag($tags[0], "thread", $thread["id"]);
	$tags = $tags[1];
	
	if (Settings::get("tagsDirection") === 'Left')
		return $tags." ".$link;
	else
		return $link." ".$tags;
	
}
?>
