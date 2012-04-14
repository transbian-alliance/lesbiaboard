<?php

$title = "Plugin Manager";

AssertForbidden("managePlugins");

if($loguser['powerlevel'] < 3)
	Kill(__("You must be an administrator to manage plugins."));


if($_GET["action"] == "enable")
{
	if($_GET["key"] != getUserKey())
		Kill("No.");
	
	Query("insert into enabledplugins values ('".justEscape($_GET["id"])."')");
	die(header("location: ".actionLink("pluginmanager")));
}
if($_GET["action"] == "disable")
{
	if($_GET["key"] != getUserKey())
		Kill("No.");
	
	Query("delete from enabledplugins where plugin='".justEscape($_GET["id"])."'");
	die(header("location: ".actionLink("pluginmanager")));
}

print '<table class="outline margin width50"><tr class="header0"><th style="width:20px; text-align:center;"></th><th>Plugin</th><th colspan="3"></th></tr>';

$cell = 0;
$pluginsDir = @opendir("plugins");

if($pluginsDir !== FALSE)
{
	while(($plugin = readdir($pluginsDir)) !== FALSE)
	{
		if($plugin == "." || $plugin == "..") continue;
		if(is_dir("./plugins/".$plugin))
		{
			try
			{
				$plugindata = getPluginData($plugin, false);
			}
			catch(BadPluginException $e)
			{
				continue;
			}
			
			listPlugin($plugin, $plugindata);
		}
	}
}

print '</table>';

function listPlugin($plugin, $plugindata)
{
	global $cell, $plugins;
	
	print '<tr class="cell'.$cell.'"><td>';
	print "X";
	print '</td><td>';
	print "<b>".$plugindata["name"]."</b><br>";
	print $plugindata["description"];
	print '</td><td>';
	
	if(isset($plugins[$plugin]))
		print "Enabled";
	print '</td><td>';

	$text = "Enable";
	$act = "enable";
	if(isset($plugins[$plugin]))
	{
		$text = "Disable";
		$act = "disable";
	}
	print actionLinkTag($text, "pluginmanager", $plugin, "action=".$act."&key=".getUserKey());
	
	print '</td><td>';
	if(in_array("settingsfile", $plugindata["buckets"]))
	{
		if(isset($plugins[$plugin]))
			print actionLinkTag("Edit settings", "editsettings", "", "plugin=".$plugin);
		else
			print "Edit settings";
	}
			
	print '</td></tr>';
	
	$cell++;
	$cell %= 2;
}
?>
