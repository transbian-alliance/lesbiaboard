<?php
/* Plugin Lister
 * By Kawa
 *
 * External requirements:
 *   None!
 */

registerPlugin("Plugin lister");

function PluginLister_Write()
{
	global $pluginList;
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
	
}

register("admins", "PluginLister_Write");

?>