<?php

$referral = $_SERVER['HTTP_REFERER'];
$startwith = 'http://'.$_SERVER['SERVER_NAME'].'/';
if ($referral && substr($referral, 0, strlen($startwith)) != $startwith)
{
	Query("INSERT INTO {referrals} (ref_hash,referral,count) VALUES ({0}, {1}, 1) ON DUPLICATE KEY UPDATE count=count+1", 
		md5($referral), $referral);
}

?>
