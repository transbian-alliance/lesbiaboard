<?php
if ($uncolors[$user['id']]['hascolor'] || $user['powerlevel'] > 1) {
	$unc = filterPollColors($_POST['unc']);
	$uncolors[$user['id']]['color'] = $unc;
	if (strlen($unc) < 3) unset($uncolors[$user['id']]['color']);
	$uncolors = serialize($uncolors);
	file_put_contents("./plugins/".$plugins[$plugin]['dir']."/colors", $uncolors);
}