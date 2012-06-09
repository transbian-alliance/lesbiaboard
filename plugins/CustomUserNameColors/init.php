<?php
if (file_exists("./plugins/".$plugins[$plugin]['dir']."/colors")) {
	$uncolors = file_get_contents("./plugins/".$plugins[$plugin]['dir']."/colors");
	$uncolors = unserialize($uncolors);
}
else {
	file_put_contents("./plugins/".$plugins[$plugin]['dir']."/colors", serialize(array()));
	$uncolors = array();
}
?>