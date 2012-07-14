<?php
//Plugin loader -- By Nikolaj
global $pluginbuckets, $plugins;

$oldplugin = $plugin;
$oldself = $self;

if ($pluginbuckets[$bucket])
{
	foreach ($pluginbuckets[$bucket] as $plugin)
	{
		if (isset($plugins[$plugin]))
		{
			$self = $plugins[$plugin];
			include("./plugins/".$plugins[$plugin]['dir']."/".$bucket.".php");
			unset($self);
		}
	}
}

$self = $oldself;
$plugin = $oldplugin;
?>
