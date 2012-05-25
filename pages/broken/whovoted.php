<?php
include("lib/common.php");

$users = array();
$rUsers = Query("select * from users");
while($user = Fetch($rUsers))
	$users[$user['id']] = $user;

print "<ul>";
foreach($users as $id => $user)
{
	print "<li>".UserLink($user)." (".$user['karma'].")";
	$rVotes = Query("select * from uservotes where uid=".$id);
	if(NumRows($rVotes))
	{
		print "<ul>";
		while($vote = Fetch($rVotes))
		{
			print "<li>".UserLink($users[$vote['voter']])." - ".($vote['up'] ? "&#x25B2;" : "&#x25BC;")."</li>"; 
		}		
		print "</ul>";
	}
	print "</li>";
}
print "</ul>";

?>
