<?php
include("lib/common.php");

if($loguser['powerlevel'] < 3)
	Kill(__("You're not admin. There is nothing for you here."));
	
$output = array();
exec("git pull", $output);
echo '<div style="width: 50%; margin-left: auto; margin-right: auto; background: black; border: 1px solid #0f0; color: #0f0; font-family: \'Consolas\', \'Lucida Console\', \'Courier New\', monospace;">';

if (empty($output)) echo '<em>(no output)</em>';
else
	foreach ($output as $line) echo htmlspecialchars($line).'<br>';

echo '</div>';

?>
