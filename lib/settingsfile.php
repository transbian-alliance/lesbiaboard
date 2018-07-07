<?php

$settings = array(
	"boardname" => array (
		"type" => "text",
		"default" => "Lesbiaboard",
		"name" => "Board name"
	),
	"metaDescription" => array (
		"type" => "text",
		"default" => "Lesbiaboard",
		"name" => "Meta description"
	),
	"metaTags" => array (
		"type" => "text",
		"default" => "Lesbiaboard trans lesbian transbian",
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
	"breadcrumbsMainName" => array (
		"type" => "text",
		"default" => "Main",
		"name" => "Text in breadcrumbs 'main' link",
	),
	"menuMainName" => array (
		"type" => "text",
		"default" => "Main",
		"name" => "Text in menu 'main' link",
	),
	"defaultTheme" => array (
		"type" => "theme",
		"default" => "abxd30",
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
	"nofollow" => array (
		"type" => "boolean",
		"default" => "0",
		"name" => "Add rel=nofollow to all user-posted links"
	),
	"tagsDirection" => array (
		"type" => "options",
		"options" => array('Left' => 'Left', 'Right' => 'Right'),
		"default" => 'Right',
		"name" => "Direction of thread tags.",
	),
	"alwaysMinipic" => array (
		"type" => "boolean",
		"default" => "0",
		"name" => "Show Minipics everywhere",
	),
	"showExtraSidebar" => array (
		"type" => "boolean",
		"default" => "1",
		"name" => "Show extra info in post sidebar",
	),
	"showNews" => array (
		"type" => "boolean",
		"default" => "1",
		"name" => "Show news on header",
	),
	"NewsTitle" => array (
		"type" => "text",
		"default" => "News",
		"name" => "News title",
	),
	"NewsText" => array (
		"type" => "texthtml",
		"default" => "Welcome to your new Lesbiaboard!<br>The first person to register gets root/owner access. For this reason, avoid showing people the URL of your site before it is set up.<br>Then, when you have registered, you can edit the board settings, forum list, this very message, and other stuff from the admin panel.<br>Enjoy Lesbiaboard!",
		"name" => "News text",
	),

	"profilePreviewText" => array (
		"type" => "textbbcode",
		"default" => "This is a sample post. You [b]probably[/b] [i]already[/i] [u]know[/u] what this is for.

[quote=another lesbian][quote=a lesbian]I fuckin' love girls[/quote]wow that's gay[/quote]

Well, what more could you [url=http://en.wikipedia.org]want to know[/url]? Perhaps how to do the classic infinite loop?
[code]while(true){
  printf(\"Hello World!\");
}[/code]",
		"name" => "Post Preview text"
	),

	"trashForum" => array (
		"type" => "forum",
		"default" => "1",
		"name" => "Trash forum",
	),
	"hiddenTrashForum" => array (
		"type" => "forum",
		"default" => "1",
		"name" => "Forum for deleted threads",
	),
	"mailSmtpEnabled" => array (
		"type" => "boolean",
		"default" => 0,
		"name" => "Use SMTP for forum emails",
	),
	"mailSmtpHost" => array (
		"type" => "text",
		"default" => "smtp.example.com",
		"name" => "SMTP: Server hostname",
	),
	"mailSmtpPort" => array (
		"type" => "integer",
		"default" => "587",
		"name" => "SMTP: Server port",
	),
	"mailSmtpAuth" => array (
		"type" => "boolean",
		"default" => 1,
		"name" => "SMTP: Use authentication",
	),
	"mailSmtpUser" => array (
		"type" => "text",
		"default" => "user@example.com",
		"name" => "SMTP: Username",
	),
	"mailSmtpPass" => array (
		"type" => "text",
		"default" => "himitsu",
		"name" => "SMTP: Password",
	),
	"mailSmtpSecure" => array (
		"type" => "text",
		"default" => "tls",
		"name" => "SMTP: Secure login type",
		"help" => "tls, ssl, or none",
	),
	"mailSenderAddress" => array (
		"type" => "text",
		"default" => "",
		"name" => "Forum email from address",
		"help" => "Email address used to send forum email. If left blank, the password reset feature is disabled.",
	),
	"mailSenderName" => array (
		"type" => "text",
		"default" => "Lesbiaboard",
		"name" => "Forum email from friendly name",
		"help" => "Friendly name used on forum email From: field.",
	),
);
?>
