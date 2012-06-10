<?php

if(!function_exists("HandleUsernameColor"))
{
	function HandleUsernameColor($field, $item)
	{
		global $user, $uncolors, $loguser;

		if ($uncolors[$user['id']]['hascolor'] || $loguser['powerlevel'] > 1)
		{
			$unc = filterPollColors($_POST['unc']);
			$uncolors[$user['id']]['color'] = $unc;
			if (strlen($unc) < 3)
				unset($uncolors[$user['id']]['color']);
			$uncolors2 = serialize($uncolors);
			$here = preg_split("#[/\\\]#", dirname(__FILE__));
			$here = $here[count($here) - 1];
			file_put_contents("./plugins/".$here."/colors", $uncolors2);
		}
		return true;
	}
}

if ($uncolors[$userid]['color'] || $loguser['powerlevel'] > 1)
{
	Write("<script type=\"text/javascript\" src=\"lib/jscolor/jscolor.js\"></script>");
	$general['appearance']['items']['unc'] = array(
		"caption" => "Name color",
		"type" => "text",
		"before" => "#",
		"value" => $uncolors[$userid]['color'],
		"length" => 6,
		"more" => "class=\"color {hash:false,required:false,pickerFaceColor:'black',pickerFace:3,pickerBorder:0,pickerInsetColor:'black',pickerPosition:'left',pickerMode:'HVS'}\"",
		"callback" => "HandleUsernameColor",
	);
}


