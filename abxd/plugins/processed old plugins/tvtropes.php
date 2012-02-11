<?php
/* [trope] BBCode
 * By Kawa
 *
 * Requires ABXD 2.1.x for BBCode bucket.
 */

registerPlugin("[trope] BBCode");

function MakeTrope($matches)
{
	$tropeTitle = $matches[1];
	$tropeText = $matches[2];
	return format("<a href=\"http://tvtropes.org/pmwiki/pmwiki.php/Main/{0}\">{1}</a>", $tropeTitle, $tropeText);
}

function MakeShortTrope($matches)
{
	$tropeTitle = $matches[1];
	$tropeText = "";
	for($i = 0; $i < strlen($tropeTitle); $i++)
	{
		if(ctype_upper($tropeTitle[$i]))
			$tropeText .= " ";
		$tropeText .= $tropeTitle[$i];
	}
	$tropeText = trim($tropeText);
	return format("<a href=\"http://tvtropes.org/pmwiki/pmwiki.php/Main/{0}\">{1}</a>", $tropeTitle, $tropeText);
}

function TVTropes_Code()
{
	global $text;
	$text = preg_replace_callback("'\[trope=(.*?)\](.*?)\[/trope\]'si", "MakeTrope", $text);
	$text = preg_replace_callback("'\[trope\](.*?)\[/trope\]'si", "MakeShortTrope", $text);

}

function TVTropes_Help($tag)
{
	if($tag == "Links")
		write("
[trope]&hellip;[/trope] &mdash; link to trope <br />
[trope=&hellip;]&hellip;[/trope] <br/>
");
}

register("bbcodes", "TVTropes_Code");
register("postHelp", "TVTropes_Help", 1);

?>
