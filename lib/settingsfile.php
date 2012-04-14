<?php
		
	$settings = array(
		"boardname" => array (
			"type" => "text",
			"default" => "AcmlmBoard XD",
			"name" => "Board name"
		),
		"metaDescription" => array (
			"type" => "text",
			"default" => "AcmlmBoard XD",
			"name" => "Meta description"
		),
		"metaTags" => array (
			"type" => "text",
			"default" => "AcmlmBoard XD abxd",
			"name" => "Meta tags"
		),
		"dateformat" => array (
			"type" => "text",
			"default" => "m-d-y, h:i a",
			"name" => "Date format"
		),
		"customTitleThreshold" => array (
			"type" => "integer",
			"default" => "100",
			"name" => "Custom Title Threshold"
		),
		"oldThreadThreshold" => array (
			"type" => "integer",
			"default" => "3",
			"name" => "Old Thread Threshold months"
		),
		"viewcountInterval" => array (
			"type" => "integer",
			"default" => "10000",
			"name" => "Viewcount Report Interval"
		),
		"ajax" => array (
			"type" => "boolean",
			"default" => "1",
			"name" => "Enable AJAX"
		),
		"guestLayouts" => array (
			"type" => "boolean",
			"default" => "0",
			"name" => "Show post layouts to guests"
		),
		"registrationWord" => array (
			"type" => "text",
			"default" => "",
			"name" => "Word needed for registration",
			"help" => "If set, the registration page will send the user to the FAQ page to look for the word",
		),
		"defaultTheme" => array (
			"type" => "theme",
			"default" => "gold",
			"name" => "Default Board Theme",
		),
		"defaultLayout" => array (
			"type" => "layout",
			"default" => "abxd",
			"name" => "Board layout",
		),
		
		"profilePreviewText" => array (
			"type" => "textbbcode",
			"default" => "This is a sample post. You [b]probably[/b] [i]already[/i] [u]know[/u] what this is for.

[quote=Goomba][quote=Mario]Woohoo! [url=http://www.mariowiki.com/Super_Mushroom]That's what I needed![/url][/quote]Oh, nooo! *stomp*[/quote]

Well, what more could you [url=http://en.wikipedia.org]want to know[/url]? Perhaps how to do the classic infinite loop?
[source=c]while(true){
    printf(\"Hello World!
\");
}[/source]",
			"name" => "Post Preview text"		
		),
	);
?>
