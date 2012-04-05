<?php
$numberOfFields = $plugins['profilefields']['amount'];

for($i = 0; $i < $numberOfFields; $i++)
{
	if(getSetting("profileExt".$i."t", true) != "" && getSetting("profileExt".$i."v", true) != "")
	{
		$profileParts['Other stuff'][strip_tags(getSetting("profileExt".$i."t", true))] = CleanUpPost(getSetting("profileExt".$i."v", true));
	}
}

?>