<?php

if($user['powerlevel'] >= 0 && $user["color"])
{
	$color = $user["color"];
	if ($color[0] !== "#")
		$color = "#$color";
	$classing = " style='color: $color'";
}
