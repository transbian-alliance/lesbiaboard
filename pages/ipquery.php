<?php

if($loguser['powerlevel'] < 3)
	Kill(__("You're not an administrator. There is nothing for you here."));

$ip = $_GET["id"];
if(!filter_var($ip, FILTER_VALIDATE_IP))
	Kill("Invalid IP");

echo "<strong>Showing IP ", $ip, "</strong><br /><br />";

echo "<form action=\"".actionLink('ipbans')."\" method=\"post\">$res
	<input type=\"hidden\" name=\"ip\" value=\"$ip\">
	<input type=\"hidden\" name=\"reason\" value=\"\">
	<input type=\"hidden\" name=\"days\" value=\"0\">
	<input type=\"hidden\" name=\"action\" value=\"".__('Add')."\">
	<input type=\"submit\" value=\"Ban\">
</form>";

echo "<br>";
echo "<a href=\"http://dnsquery.org/ipwhois/$ip\" target=\"_blank\">Whois Query</a>";
echo "<br>TODO: Show users and posts with this IP. Check if the IP is banned and offer to unban. etc etc.";

