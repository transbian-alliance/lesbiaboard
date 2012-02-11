<?php
$numberOfFields = $plugins['profilefields']['amount'];
for($i = 0; $i < $numberOfFields; $i++)
{
	registerSetting("profileExt".$i."t", "Custom profile field #".($i+1)." title");
	registerSetting("profileExt".$i."v", "Custom profile field #".($i+1)." value");
}
?>