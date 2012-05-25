<?php

if ($uncolors[$user['id']]['hascolor'] || $user['powerlevel'] > 1) {
	Write("<script type=\"text/javascript\" src=\"lib/jscolor/jscolor.js\"></script>");
	row("Name color", array(
		"type"=>"text",
		"name"=>"unc",
		"id"=>"unc",
		"value"=>$uncolors[$user['id']]['color'],
		"maxlength"=>6,
		"ext"=>"size=\"6\" class=\"color {hash:false,required:false,pickerFaceColor:'black',pickerFace:3,pickerBorder:0,pickerInsetColor:'black',pickerPosition:'left',pickerMode:'HVS'}\""
	), array("prepend"=>"#"));
}