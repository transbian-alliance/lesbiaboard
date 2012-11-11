<?php


if($loguser['powerlevel'] < 3)
	Kill(__("You're not an administrator. There is nothing for you here."));

$badVotes = Query("SELECT * FROM  {pollvotes} pv
				   WHERE choiceid = 0");

echo "Fixing ", numRows($badVotes), " bad votes... ";

while($vote = Fetch($badVotes))
{
	$lol = Fetch(Query("SELECT id FROM {poll_choices} WHERE poll={0} LIMIT {1}, 1", 
						$vote["poll"], $vote["choice"]));
	$lol = $lol["id"];
	Query("UPDATE {pollvotes} SET choiceid={0} WHERE poll={1} AND choice={2}", $lol, $vote["poll"], $vote["choice"]);
}

echo "Done!";


