<?php
	$cell = ($cell + 1) % 2;
	write("
		<tr class=\"cell{0}\">
			<td style=\"vertical-align: top\">
				Active plugins
			</td>
			<td>
", $cell);

	foreach($plugins as $p)
	{
		$d = $p['description'];
		if($p['author'])
			$d = "By ".$p['author'].". ".$d; 
		write("
				&bull; <span title=\"{1}\">{0}</span><br />
", $p['name'], $d);
	}
	
	write("
			</td>
		</tr>
");
/*
	$thelist = "";
	foreach($pluginList as $plugin)
		$thelist .= Format(
"
					<li>{0}</li>
", $plugin);

	Write(
"
			<dt>The following plugins are installed:</dt>
			<dd>
				<ul style=\"margin: 0px; padding: 0px\">
					{0}
				</ul>
			</dd>
", $thelist);
*/

//register("admins", "PluginLister_Write");

?>
