<?php

	$groups = array();
	$qGroups = "select name from {groups} left join {groupaffiliations} on {groups}.id = {groupaffiliations}.gid where uid = ".$user['id']." and status = 0";
	$rGroups = Query($qGroups);
	while($group = Fetch($rGroups))
		$groups[] = $group['name'];
	$groups = implode(", ", $groups);
	
	if($groups)
		$profileParts['General information']['Groups'] = $groups;

?>