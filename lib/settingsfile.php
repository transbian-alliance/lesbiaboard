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
		"breadcrumbsMainName" => array (
			"type" => "text",
			"default" => "Main",
			"name" => "Text in breadcrumbs 'main' link",
		),
		"mailResetSender" => array (
			"type" => "text",
			"default" => "",
			"name" => "Password Reset e-mail Sender",
			"help" => "Email address used to send the pasword reset e-mails. If left blank, the password reset feature is disabled.",
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
		"defaultLanguage" => array (
			"type" => "language",
			"default" => "en_US",
			"name" => "Board language",
		),
		"floodProtectionInterval" => array (
			"type" => "integer",
			"default" => "10",
			"name" => "Minimum time between user posts"
		),
		"tagsDirection" => array (
			"type" => "options",
			"options" => array('Left' => 'Left', 'Right' => 'Right'),
			"default" => 'Right',
			"name" => "Direction of thread tags.",
		),
		"showPoRA" => array (
			"type" => "boolean",
			"default" => "1",
			"name" => "Show Points of Required Attention",
		),
		"PoRATitle" => array (
			"type" => "text",
			"default" => "Points of Required Attention&trade;",
			"name" => "PoRA title",
		),
		"PoRAText" => array (
			"type" => "texthtml",
			"default" => "Welcome to your new ABXD Board!",
			"name" => "PoRA text",
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
		
		"trashForum" => array (
			"type" => "forum",
			"default" => "1",
			"name" => "Trash forum",
		),
	);
?>
