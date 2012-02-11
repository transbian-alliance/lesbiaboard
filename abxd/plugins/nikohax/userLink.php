<?php
if ($fname == "Nikolaj") {
	$classing  = substr($classing, 0, (strlen($classing)-1));
	if (!$uncolors[$user[$field]])
		$classing .= '" style="';
	else
		$classing .= '; ';
	$classing .= 'text-shadow: 0px 0px 2px #FFF;"';
}