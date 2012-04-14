<?php

function ircReport($stuff)
{
	global $pluginsettings;
	
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_connect($sock, $pluginsettings["host"], $pluginsettings["port"]);
	socket_write($sock, $stuff."\n");
	socket_close($sock);
}
