<?php

if(isset($_POST['google']))
{
	$full = GetFullURL();
	$here = substr($full, 0, strrpos($full, "/"));
	header("Location: http://www.google.com/search?q=".urlencode($_POST['google']." site:".$here));
}

AssertForbidden("search");

write("
<div style=\"float: left; width: 70%;\">

	<form action=\"".actionLink("search")."\" method=\"post\">
		<div class=\"outline PoRT margin width25\" style=\"margin: 16px; width: 100%; float: none;\">
			<div class=\"errort\">
				<strong>".__("Google search")."</strong>
			</div>
			<div class=\"errorc left cell0\" style=\"padding: 8px; font-size: 150%\">
				<input type=\"text\" maxlength=\"1024\" name=\"google\" style=\"width: 80%;\" />
				&nbsp;
				<input type=\"submit\" value=\"".__("Search")."\" />
			</div>
		</div>
	</form>
");

if($loguser['powerlevel'] < 1)
{
	echo("</div>");
	throw new KillException();
}

write("
	<form action=\"".actionLink("search")."\" method=\"post\">
		<div class=\"outline PoRT margin\" style=\"margin: 16px; width: 100%; float: none;\">
			<div class=\"errort\">
				<strong>".__("Internal search")."</strong>
			</div>
			<div class=\"errorc left cell0\" style=\"padding: 8px; font-size: 150%\">
				<input type=\"text\" maxlength=\"1024\" name=\"q\" style=\"width: 80%;\" value=\"".htmlspecialchars($_GET['q'])."\">
				&nbsp;
				<input type=\"submit\" value=\"".__("Search")."\">
			</div>
		</div>
	</form>
</div>

<div class=\"PoRT margin width25\">
	<div class=\"errort\"><strong>".__("Search help")."</strong></div>
	<div class=\"errorc left cell0\" style=\"padding: 8px 8px;\">
		".__("Internal search checks both thread titles and post text, returning results from both.")."
		<dl>
			<dt><samp>foo bar</samp></dt>
			<dd>".__("Find entries with either term")."</dd>
			<dt><samp>\"foo bar\"</samp></dt>
			<dd>".__("Find entries with full phrase")."</dd>
			<dt><samp>+foo -bar</samp></dt>
			<dd>".__("Find entries with <var>foo</var> but not <var>bar</var>")."</dd>
		</dl>
	</div>
</div>

<hr style=\"clear: both; visibility: hidden;\" />

");

if(isset($_POST['q']))
{
	$searchQuery = $_POST["q"];
	$totalResults = 0;
	$bool = htmlspecialchars($searchQuery);
	$t = explode(" ", $bool);
	$terms = array();
	foreach($t as $term)
	{
		if($term[0] == "-")
			continue;
		if($term[0] == "+" || $term[0] == "\"")
			$terms[] = substr($term, 1);
		else if($term[strlen($term)-1] == "*" || $term[strlen($term)-1] == "\"")
			$terms[] = substr($term, 0, strlen($term) - 1);
		else if($term != "")
			$terms[] = $term;
	}
	$final = "";

	$search = Query("
		SELECT 
			t.id, t.title, t.user, 
			u.(_userfields)
		FROM {threads} t
			LEFT JOIN {users} u ON u.id=t.user
		WHERE MATCH(t.title) AGAINST({0} IN BOOLEAN MODE)
		ORDER BY t.lastpostdate DESC
		LIMIT 0,100", $bool);

	if(NumRows($search))
	{
		$results = "";
		while($result = Fetch($search))
		{
			$snippet = MakeSnippet($result['title'], $terms, true);
			$userlink = UserLink(getDataPrefix($result, "u_"));
			$url = actionLink("thread", $result["id"]);
			if($snippet != "")
			{
				$totalResults++;
				$results .= "
	<tr class=\"cell0\">
		<td class=\"smallFonts\">
			$userlink
		</td>
		<td>
			<a href=\"$url\">$snippet</a>
		</td>
	</tr>";
			}
		}
		
		if($results != "")
			$final .= "
<table class=\"outline margin\">
	<tr class=\"header0\">
		<th colspan=\"4\">Thread title results</th>
	</tr>
	<tr class=\"header1\">
		<th style=\"width:15%\">User</th>
		<th>Thread</th>
	</tr>
	$results
</table>";
	}

	$search = Query("
		SELECT 
			pt.text, pt.pid, 
			t.title, t.id,
			u.(_userfields) 
		FROM {posts_text} pt
			LEFT JOIN {posts} p ON pt.pid = p.id 
			LEFT JOIN {threads} t ON t.id = p.thread 
			LEFT JOIN {users} u ON u.id = p.user 
		WHERE pt.revision = p.currentrevision AND MATCH(pt.text) AGAINST({0} IN BOOLEAN MODE) 
		ORDER BY p.date DESC 
		LIMIT 0,100", $bool);

	if(NumRows($search))
	{
		$results = "";
		while($result = Fetch($search))
		{
//			$result['text'] = str_replace("<!--", "~#~", str_replace("-->", "~#~", $result['text']));
			$snippet = MakeSnippet($result['text'], $terms);
			$userlink = UserLink(getDataPrefix($result, "u_"));
			$url = actionLink("thread", $result["id"]);
			$posturl = actionLink("thread", "", "pid=".$result['pid']."#".$result['pid']);

			if($snippet != "")
			{
				$totalResults++;
				$results .= "
	<tr class=\"cell0\">
		<td class=\"smallFonts\">
			$userlink
		</td>
		<td>
			$snippet
		</td>
		<td class=\"smallFonts\">
			<a href=\"$url\">{$result['title']}</a>
		</td>
		<td class=\"smallFonts\">
			&raquo;&nbsp;<a href=\"$posturl\">{$result['pid']}</a>
		</td>
	</tr>";
			}
		}

		if($results != "")
		{
			$final .= "
<table class=\"outline margin\">
	<tr class=\"header0\">
		<th colspan=\"4\">Text results</th>
	</tr>
	<tr class=\"header1\">
		<th>User</th>
		<th>Text</th>
		<th>Thread</th>
		<th>ID</th>
	</tr>
	$results
</table>";
		}
	}

	if($totalResults == 0)
		Alert(Format("No results for \"{0}\".", htmlspecialchars($searchQuery)), "Search");
	else
		Write("
<div class=\"outline header2 cell2 margin\" style=\"text-align: center; font-size: 130%;\">
	{0}
</div>
{1}
", Plural($totalResults, "result"), $final);
}



function MakeSnippet($text, $terms, $title = false)
{
	$text = strip_tags($text);
	if(!$title)
		$text = preg_replace("/(\[\/?)(\w+)([^\]]*\])/i", "", $text);
	
	$lines = explode("\n", $text);
	$terms = implode("|", $terms);
	$contextlines = 3;
	$max = 50;
	$pat1 = "/(.*)(".$terms.")(.{0,".$max."})/i";
	$lineno = 0;
	$extract = "";
	foreach($lines as $line)
	{
		if($contextlines == 0)
			break;
		$lineno++;
		
		if($title)
			$line = htmlspecialchars($line);
		else
		{
			$m = array();
			if(!preg_match($pat1, $line, $m))
				continue;
			$contextlines--;

			$pre = substr($m[1], -$max);
			if(count($m) < 3)
				$post = "";
			else
				$post = $m[3];

			$found = $m[2];

			$line = htmlspecialchars($pre.$found.$post);
		}
		$line = trim($line);
		if($line == "")
			continue;
		$pat2 = "/(".$terms.")/i";
		$line = preg_replace($pat2, "<strong>\\1</strong>", $line);
		$line = preg_replace("/\~#\~(.*?)\~#\~/", "<span style=\"color: #6f6;\">&lt;!--\\1--&gt;</span>", $line);
		if(!$title)
			$extract .= "&bull; ".$line."<br />";
		else
			$extract .= $line;
	}

	return $extract;
}
