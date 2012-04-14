<?php

function ircReport($stuff)
{
	global $selfsettings;

	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_connect($sock, $selfsettings["host"], $selfsettings["port"]);
	socket_write($sock, $stuff."\n");
	socket_close($sock);
}
