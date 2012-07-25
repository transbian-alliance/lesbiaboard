<?php
global $uncolors;
if(!isset($uncolors))
{
	if (!file_exists("./plugins/".$self['dir']."/colors"))
	{
		$f = fopen("./plugins/".$self['dir']."/colors", 'w');
		fclose($f);
	}
	$uncolors = file_get_contents("./plugins/".$plugins[$plugin]['dir']."/colors");
	$uncolors = unserialize($uncolors);
}
if($user['powerlevel'] >= 0 && $uncolors[$user["id"]] && $unc_not_loaded == false)
	$classing = " style=\"color: #".$uncolors[$user["id"]]['color']."\"";
