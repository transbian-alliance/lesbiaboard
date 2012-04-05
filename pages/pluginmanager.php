<?php

$title = "Plugin Manager";
print '<table class="outline margin width50"><tr class="header0"><th>Plugin name</th><th>Description</th><th></th></tr>';

$cell = 0;
$pluginsDir = @opendir("plugins");

if($pluginsDir !== FALSE)
{
	while(($plugin = readdir($pluginsDir)) !== FALSE)
	{
		if($plugin == "." || $plugin == "..") continue;
		if(is_dir("./plugins/".$plugin))
		{
			print '<tr class="cell'.$cell.'"><td>'.$plugin.'</td><td>';
			try
			{
				$plugindata = getPluginData($plugin, false);
				print $plugindata["description"];
			}
			catch(BadPluginException $e)
			{
				print "<b>ERROR</b>: ".$e->getMessage();
			}
			
			print '</td><td>';
			
			if(isset($plugins[$plugin]))
				print "Enabled";
			
			print '</td></tr>';
			
			$cell++;
			$cell %= 2;
		}
	}
}

print '</table>';
?>
