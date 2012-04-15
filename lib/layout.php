<?php
//Layout functions, by Nikolaj

function MakeCrumbs($path, $links)
{
	global $layout_crumbs;

	$pathPrefix = array(Settings::get("breadcrumbsMainName") => actionLink("index"));
	$pathPostfix = array(); //Not sure how this could be used, but...
	
	$bucket = "breadcrumbs"; include("lib/pluginloader.php");

	$path = $pathPrefix + $path + $pathPostfix;
	
	foreach($path as $text=>$link)
	{
		$link = str_replace("&","&amp;",$link);
		if($link)
		{
			$sep = strpos($text, '<TAGS>');
			if ($sep === FALSE)
			{
				$title = $text;
				$tags = '';
			}
			else
			{
				$title = substr($text, 0, $sep);
				$tags = ' '.substr($text, $sep+6);
			}
			$crumbs .= "<a href=\"".$link."\">".$title."</a>".$tags." &raquo; ";
		}
		else
			$crumbs .= str_replace('<TAGS>', '', $text). " &raquo; ";
	}
	$crumbs = substr($crumbs, 0, strlen($crumbs) - 8);
	
	$layout_crumbs = "
<div class=\"margin\">
	<div style=\"float: right;\">
		<ul class=\"pipemenu smallFonts\">
			$links
		</ul>
	</div>
	$crumbs
</div>";
}
?>
