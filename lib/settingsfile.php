<?php
		
	$settings = array(
		"boardname" => array (
			"type" => "text",
			"default" => "AcmlmBoard XD",
			"name" => __("Board name")
		),
		"metaDescription" => array (
			"type" => "text",
			"default" => "AcmlmBoard XD",
			"name" => __("Meta description")
		),
		"metaTags" => array (
			"type" => "text",
			"default" => "AcmlmBoard XD abxd",
			"name" => __("Meta tags")
		),
		"dateformat" => array (
			"type" => "text",
			"default" => "m-d-y, h:i a",
			"name" => __("Date format")
		),
		"customTitleThreshold" => array (
			"type" => "integer",
			"default" => "100",
			"name" => __("Custom Title Threshold")
		),
		"oldThreadThreshold" => array (
			"type" => "integer",
			"default" => "3",
			"name" => __("Old Thread Threshold months")
		),
		"viewcountInterval" => array (
			"type" => "integer",
			"default" => "10000",
			"name" => __("Viewcount Report Interval")
		),
		"ajax" => array (
			"type" => "boolean",
			"default" => "1",
			"name" => __("Enable AJAX")
		),
		"guestLayouts" => array (
			"type" => "boolean",
			"default" => "0",
			"name" => __("Show post layouts to guests")
		),
		"registrationWord" => array (
			"type" => "text",
			"default" => "",
			"name" => __("Word needed for registration"),
			"help" => __("If set, the registration page will send the user to the FAQ page to look for the word"),
		),
		"breadcrumbsMainName" => array (
			"type" => "text",
			"default" => "Main",
			"name" => __("Text in breadcrumbs 'main' link"),
		),
		"mailResetSender" => array (
			"type" => "text",
			"default" => "",
			"name" => __("Password Reset e-mail Sender"),
			"help" => __("Email address used to send the pasword reset e-mails. If left blank, the password reset feature is disabled."),
		),
		"defaultTheme" => array (
			"type" => "theme",
			"default" => "gold",
			"name" => __("Default Board Theme"),
		),
		"defaultLayout" => array (
			"type" => "layout",
			"default" => "abxd",
			"name" => __("Board layout"),
		),
		"defaultLanguage" => array (
			"type" => "language",
			"default" => "en_US",
			"name" => __("Board language"),
		),
		"showPoRA" => array (
			"type" => "boolean",
			"default" => "1",
			"name" => __("Show Points of Required Attention"),
		),
		"tagsDirection" => array (
			"type" => "options",
			"options" => array('Left' => 'Left', 'Right' => 'Right'),
			"default" => 'Right',
			"name" => __("Direction of thread tags."),
		),
		"PoRATitle" => array (
			"type" => "text",
			"default" => "Points of Required Attention&trade;",
			"name" => __("PoRA title"),
		),
		"PoRAText" => array (
			"type" => "texthtml",
			"default" => "Welcome to your new ABXD Board!",
			"name" => __("PoRA text"),
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
			"name" => __("Post Preview text")		
		),
	);
?>
