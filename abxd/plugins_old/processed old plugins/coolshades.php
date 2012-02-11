<?php

registerPlugin("Battle system's coolshades");

function BottleSystem_Goggles()
{
	global $goggles, $loguserid, $loguser, $postText;
	if(!isset($goggles))
	{
		/*
		$gogR = Query("select item6 from users_rpg where id = ".$loguserid);
		if(NumRows($gogR))
		{
			$gog = Fetch($gogR);
			$goggles = (strpos($gog['item6'], "\"Cool shades\"") !== FALSE);
		}
		*/
		if($loguser['powerlevel'] > 0)
			$goggles = 1;
	}
	if($goggles)
	{
		$postText = str_replace("<!--", "<span style=\"color: #66ff66;\">&lt;!--", $postText);
		$postText = str_replace("-->", "--&gt;</span>", $postText);
	}
}

register("postManglers", "BottleSystem_Goggles");
	
?>