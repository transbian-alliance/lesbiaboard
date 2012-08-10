<?php

function formatPlusOnes($plusones)
{
	$style = "";

	if($plusones >= 1)
		$style .= "color:#3F0;";
	if($plusones >= 5)
		$style .= "font-weight:bold;";
	if($plusones >= 20)
		$style .= "font-size:14px;";
	
	return "<span style=\"$style\">+$plusones</span>";
}
