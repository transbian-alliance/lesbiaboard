<?php
//Improved permissions system ~Nina
$groups = array();
$rGroups = query("SELECT * FROM usergroups");
while ($group = fetch($rGroups)) {
	$groups[] = $group;
	$groups[$grup['id']]['permissions'] = unserialize($group['permissions']);
}

//Do nothing for guests.
if ($loguserid) {
	$rPermissions = query("SELECT * FROM userpermissions WHERE uid=".$loguserid);
	$permissions = fetch($rPermissions);
	$permissions['permissions'] = unserialize($permissions['permissions']);
	if (is_array($groups[$loguser['group']]['permissions']))
		$loguser['permissions'] = array_merge($groups[$loguser['group']]['permissions'], $permissions); //$permissions overrides the group permissions here.	
	if ($loguser['powerlevel'] == 4) $loguser['group'] == "root"; //Just in case.
}

//Returns false for guests no matter what. Returns if the user is allowed to do something otherwise.
//Additionally always returns true if the user's powerlevel is root.
function checkAllowed($p) {
	global $loguser, $loguserid;
	if (!$loguserid) return false;
	elseif ($loguser['group'] == "root" || $loguser['powerlevel'] == 4) return true;
	elseif (strpos('.', $p)) {
		$nodes = explode(".", $p);
		$r = $loguser['permissions'];
		foreach ($nodes as $n)
			$r = $r[$node];
		return $r;
	}
	else return $loguser['permissions'][$p];
}
