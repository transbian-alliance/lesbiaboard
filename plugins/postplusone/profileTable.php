<?php

$val = $user["postplusones"];
if($user["postplusones"])
	$val .= " [".actionLinkTag("View...", "listplusones", $user["id"])."]";
	
$profileParts[__("General information")][__("Total +1s received")] = $val;
$profileParts[__("General information")][__("Total +1s given")] = $user["postplusonesgiven"];

$res = query("select count(*) as ct, u.(_userfields)
from postplusones l
left join posts p on l.post=p.id
left join users u on u.id = l.user
where p.user={0}
group by l.user
order by count(*) desc
limit 5", $user["id"]);

$plusoners = array();

while($row = fetch($res))
	$plusoners[] = userLink(getDataPrefix($row, "u_"))." (".$row["ct"].")";

$profileParts[__("General information")][__("Top +1ers")] = implode(", ", $plusoners);


