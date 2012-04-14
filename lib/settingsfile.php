<?php
		
	$settings = array(
		"boardname" => array (
			"type" => "text",
			"default" => "AcmlmBoard XD",
			"name" => "Board name"
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
		"ajax" => array (
			"type" => "boolean",
			"default" => "true",
			"name" => "Enable AJAX"
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
