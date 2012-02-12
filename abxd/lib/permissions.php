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
	$loguser['permissions'] = array_merge($groups[$loguser['group']], $permissions); //$permissions overrides the group permissions here.	
}

//Returns false for guests no matter what. Returns if the user is allowed to do something otherwise.
function checkAllowed($p) {
	global $loguser, $loguserid;
	if (!$loguserid) return false;
	elseif (strpos('.', $p)) {
		$nodes = explode(".", $p);
		$r = $loguser['permissions'];
		foreach ($nodes as $n) {
			$r = $r[$node];
		return $r;
	}
	else return $loguser['permissions'][$p];
}
