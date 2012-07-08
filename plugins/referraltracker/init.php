<?php

$referral = $_SERVER['HTTP_REFERER'];
$startwith = 'http://'.$_SERVER['SERVER_NAME'].'/';
if ($referral && substr($referral, 0, strlen($startwith)) != $startwith)
	Query("INSERT INTO {$dbpref}referrals (ref_hash,referral,count) VALUES ('".md5($referral)."','".justEscape($referral)."',1) ON DUPLICATE KEY UPDATE count=count+1");

?>