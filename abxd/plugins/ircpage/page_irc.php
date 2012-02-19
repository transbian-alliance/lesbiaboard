<?php

$title = "IRC Chat";

$bad = array("~", "&", "@", "?", "!", ".", ",", "=", "+", "%", "*");
$handle = str_replace(" ", "", $loguser['name']);
$handle = str_replace($badchars, "_", $handle);
if(!$handle)
{
	$handle = "ABXDGuest";
	$guest = "<p>When you've connected to the IRC network, please use the command <kbd>/nick NICKNAME</kbd>.</p>";
}

$server = $self["server"];
$channel = $self["channel"];
if(isset($_GET['connect']))
{

	write("
	<div class=\"faq outline margin\" style=\"width: 75%; margin: 2em auto; padding: 2em; text-align: center;\">
		<applet code=\"IRCApplet.class\" codebase=\"irc/\"  
		archive=\"irc.jar,pixx.jar\" width=\"100%\" height=\"400\">
		<param name=\"CABINETS\" value=\"irc.cab,securedirc.cab,pixx.cab\">

		<param name=\"nick\" value=\"{0}\">
		<param name=\"alternatenick\" value=\"{0}_??\">
		<param name=\"fullname\" value=\"ABXD IRC User\">
		<param name=\"host\" value=\"{1}\">
		<param name=\"port\" value=\"6667\">
		<param name=\"gui\" value=\"pixx\">
		<param name=\"authorizedcommandlist\" value=\"all-server-s\">

		<param name=\"quitmessage\" value=\"Leaving\">
		<param name=\"autorejoin\" value=\"true\">

		<param name=\"style:bitmapsmileys\" value=\"false\">
		<param name=\"style:backgroundimage\" value=\"false\">
		<param name=\"style:backgroundimage1\" value=\"none+Channel all 2 background.png.gif\">
		<param name=\"style:sourcecolorrule1\" value=\"all all 0=000000 1=ffffff 2=0000ff 3=00b000 4=ff4040 5=c00000 6=c000a0 7=ff8000 8=ffff00 9=70ff70 10=00a0a0 11=80ffff 12=a0a0ff 13=ff60d0 14=a0a0a0 15=d0d0d0\">

		<param name=\"pixx:timestamp\" value=\"true\">
		<param name=\"pixx:highlight\" value=\"true\">
		<param name=\"pixx:highlightnick\" value=\"true\">
		<param name=\"pixx:nickfield\" value=\"false\">
		<param name=\"pixx:styleselector\" value=\"true\">
		<param name=\"pixx:setfontonstyle\" value=\"true\">

		<param name=\"command1\" value=\"/join {2}\">

		</applet>
	</div>
", $handle, $server, $channel);
}
else
{
	write("
	<div class=\"faq outline margin\" style=\"width: 75%; margin: 2em auto; padding: 2em; text-align: center;\">
		<h3>IRC chat</h3><br />We advice you to get a real IRC client such as <strong>XChat</strong> (or <strong>XChat-WDK</strong> if you are using Windows).<br />If you don't want to get a real IRC client, you can use the client on the board.
		<p>
			<strong>Server:</strong> {1}<br />
			<strong>Channel:</strong> {2}<br />
			<strong>Nickname:</strong> {0}
		</p>
		<p>
			<a href=\"".actionLink("irc", "", "connect")."\">Use the on-board Java IRC client.</a>
		</p>
		{3}
	</div>
", $handle, $server, $channel, $guest);
}

?>
