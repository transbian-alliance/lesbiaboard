<?php

function ircReport($stuff)
{
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_connect($sock, Settings::pluginGet("host"), Settings::pluginGet("port"));
	socket_write($sock, $stuff."\n");
	socket_close($sock);
}
