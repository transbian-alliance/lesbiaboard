<?php

if(!function_exists("HandleUsernameColor"))
{
	function HandleUsernameColor($field, $item)
	{
		global $user, $loguser;

		if ($loguser['powerlevel'] > 1)
		{
			$unc = filterPollColors($_POST['unc']);
			if (strlen($unc) < 3)
				$unc = "";

			Query("UPDATE {users} SET color={0} WHERE id={1}", $unc, $user["id"]);
		}
		return true;
	}
}

if ($loguser['powerlevel'] > 1)
{
	Write("<script type=\"text/javascript\" src=\"js/jscolor/jscolor.js\"></script>");
	$general['appearance']['items']['unc'] = array(
		"caption" => "Name color",
		"type" => "text",
		"before" => "#",
		"value" => $user['color'],
		"length" => 6,
		"more" => "class=\"color {hash:false,required:false,pickerFaceColor:'black',pickerFace:3,pickerBorder:0,pickerInsetColor:'black',pickerPosition:'left',pickerMode:'HVS'}\"",
		"callback" => "HandleUsernameColor",
	);
}


