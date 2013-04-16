<?php

if(!function_exists("HandleUsernameColor"))
{
	function handleUsernameColor($field, $item)
	{
		global $user, $loguser;

		if ($loguser['powerlevel'] > 1)
		{
			$unc = $_POST['color'];
			if($unc[0] !== "#")
				$unc = "#$unc";
			if($unc != "")
				$unc = filterPollColors(str_pad($unc, 7, '0'));

			Query("UPDATE {users} SET color={0s} WHERE id={1}", $unc, $user["id"]);
		}
		return true;
	}
}

if ($loguser['powerlevel'] > 1)
{
	$general['appearance']['items']['color'] = array(
		"caption" => "Name color",
		"type" => "color",
//		"callback" => "handleUsernameColor",
	);
	$general['appearance']['items']['hascolor'] = array(
		"caption" => "Enable color",
		"type" => "checkbox",
//		"callback" => "handleUsernameColor",
	);
}


