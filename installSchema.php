<?php

$tables = array
(
	"badges" => array
	(
		"fields" => array
		(
			"owner" => $genericInt,
			"name" => $var256,
			"color" => $smallerInt,
		),
		"special" => "unique key `steenkinbadger` (`owner`,`name`)"
	),
	"settings" => array
	(
		"fields" => array
		(
			"plugin" => $var128,
			"name" => $var128,
			"value" => $text,
		),
		"special" => "unique key `mainkey` (`plugin`,`name`)"
	),
	
	//Weird column names: An entry means that "blockee" has blocked the layout of "user"
	"blockedlayouts" => array
	(
		"fields" => array
		(
			"user" => $genericInt,
			"blockee" => $genericInt,
		),
		"special" => "key `mainkey` (`blockee`, `user`)"
	),
	"categories" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"name" => $var256,
			"corder" => $smallerInt,
		),
		"special" => $keyID
	),
	"enabledplugins" => array
	(
		"fields" => array
		(
			"plugin" => $var256,
		),
		"special" => "unique key `plugin` (`plugin`)"
	),
	"forummods" => array
	(
		"fields" => array
		(
			"forum" => $genericInt,
			"user" => $genericInt,			
		),
		"special" => "key `mainkey` (`forum`, `user`)"
	),
	"forums" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"title" => $var256,
			"description" => $text,
			"catid" => $smallerInt,
			"minpower" => $smallerInt,
			"minpowerthread" => $smallerInt,
			"minpowerreply" => $smallerInt,
			"minpostsread" => $genericInt,
			"numthreads" => $genericInt,
			"numposts" => $genericInt,
			"lastpostdate" => $genericInt,
			"lastpostuser" => $genericInt,
			"lastpostid" => $genericInt,
			"hidden" => $bool,
			"forder" => $smallerInt,
		),
		"special" => $keyID.", key `catid` (`catid`)"
	),
	"guests" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"ip" => $var50,
			"date" => $genericInt,
			"lasturl" => $var128,
			"lastforum" => $genericInt,	
			"bot" => $bool,
		),
		"special" => $keyID.", key `ip` (`ip`), key `bot` (`bot`)"
	),
	"ignoredforums" => array
	(
		"fields" => array
		(
			"uid" => $genericInt,
			"fid" => $genericInt,			
		),
		"special" => "key `mainkey` (`uid`, `fid`)"
	),
	"ip2c" => array
	(
		"fields" => array
		(
			"ip_from" => "bigint(12) NOT NULL DEFAULT '0'",
			"ip_to" => "bigint(12) NOT NULL DEFAULT '0'",
			"cc" => "varchar(2) DEFAULT ''",			
		),
		"special" => "key `ip_from` (`ip_from`)"
	),
	"ipbans" => array
	(
		"fields" => array
		(
			"ip" => $var50,
			"reason" => $var128,			
			"date" => $genericInt,			
			"whitelisted" => $bool,
		),
		"special" => "unique key `ip` (`ip`), key `date` (`date`)"
	),
	"misc" => array
	(
		"fields" => array
		(
			"version" => $genericInt,
			"views" => $genericInt,
			"hotcount" => $genericInt,			
			"maxusers" => $genericInt,
			"maxusersdate" => $genericInt,
			"maxuserstext" => $text,
			"maxpostsday" => $genericInt,
			"maxpostsdaydate" => $genericInt,
			"maxpostshour" => $genericInt,
			"maxpostshourdate" => $genericInt,
			"milestone" => $text,
		),
	),
	"moodavatars" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"uid" => $genericInt,			
			"mid" => $genericInt,			
			"name" => $var256,
		),
		"special" => $keyID. ", key `mainkey` (`uid`, `mid`)"
	),
	"pmsgs" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"userto" => $genericInt,
			"userfrom" => $genericInt,
			"date" => $genericInt,
			"ip" => $var50,
			"msgread" => $bool,
			"deleted" => "tinyint(4) NOT NULL DEFAULT '0'",
			"drafting" => $bool,
		),
		"special" => $keyID.", key `userto` (`userto`), key `userfrom` (`userfrom`), key `msgread` (`msgread`), key `date` (`date`)"
	),
	"pmsgs_text" => array
	(
		"fields" => array
		(
			"pid" => $genericInt,
			"title" => $var256,
			"text" => $postText,
		),
		"special" => "primary key (`pid`)"
	),
	"poll" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"question" => $var256,
			"briefing" => $text,
			"closed" => $bool,
			"doublevote" => $bool,
		),
		"special" => $keyID
	),
	"pollvotes" => array
	(
		"fields" => array
		(
			"user" => $genericInt,
			"choiceid" => $genericInt,
			"poll" => $genericInt,
		),
		"special" => "key `lol` (`user`, `choiceid`), key `poll` (`poll`)"
	),
	"poll_choices" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"poll" => $genericInt,
			"choice" => $var256,
			"color" => $varcolor,
		),
		"special" => $keyID.", key `poll` (`poll`)"
	),
	"posts" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"thread" => $genericInt,
			"user" => $genericInt,
			"date" => $genericInt,
			"ip" => $var50,
			"num" => $genericInt,
			"deleted" => $bool,
			"deletedby" => $genericInt,
			"reason" => $var256,
			"options" => "tinyint(4) NOT NULL DEFAULT '0'",
			"mood" => $genericInt,
			"currentrevision" => $genericInt,
		),
		"special" => $keyID.", key `thread` (`thread`), key `date` (`date`), key `user` (`user`), key `ip` (`ip`), key `id` (`id`, `currentrevision`), key `deletedby` (`deletedby`)"
	),
	"posts_text" => array
	(
		"fields" => array
		(
			"pid" => $genericInt,
			"text" => $postText,
			"revision" => $genericInt,
			"user" => $genericInt,
			"date" => $genericInt,
		),
		"special" => "fulltext key `text` (`text`), key `pidrevision` (`pid`, `revision`), key `user` (`user`)"
	),
	"proxybans" => array
	(
		"fields" => array
		(
			"id" => $AI,			
			"ip" => $var50,
		),
		"special" => $keyID.", unique key `ip` (`ip`)"
	),
	"queryerrors" => array
	(
		"fields" => array
		(
			"id" => $AI,		
			"user" => $genericInt,	
			"ip" => $var50,
			"time" => $genericInt,	
			"query" => $text,
			"get" => $text,
			"post" => $text,
			"cookie" => $text,
			"error" => $text
		),
		"special" => $keyID
	),
	"log" => array
	(
		"fields" => array
		(
			"user" => $genericInt,
			"date" => $genericInt,
			"type" => "varchar(16)".$notNull,
			"user2" => $genericInt,
			"thread" => $genericInt,
			"post" => $genericInt,
			"forum" => $genericInt,
			"forum2" => $genericInt,
			"pm" => $genericInt,
			"text" => $var1024,
			"ip" => $var50,
		),
	),
	"sessions" => array
	(
		"fields" => array
		(
			"id" => $var256,
			"user" => $genericInt,
			"expiration" => $genericInt,
			"autoexpire" => $bool,
			"iplock" => $bool,
			"iplockaddr" => $var128,
			"lastip" => $var128,
			"lasturl" => $var128,
			"lasttime" => $genericInt,
		),
		"special" => $keyID.", key `user` (`user`), key `expiration` (`expiration`)"
	),
	"smilies" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"code" => $var32,
			"image" => $var32,
		),
		"special" => $keyID
	),
	"threads" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"forum" => $genericInt,
			"user" => $genericInt,
			"date" => $genericInt,
			"firstpostid" => $genericInt,
			"views" => $genericInt,
			"title" => $var128,
			"icon" => $var256,
			"replies" => $genericInt,
			"lastpostdate" => $genericInt,
			"lastposter" => $genericInt,
			"lastpostid" => $genericInt,
			"closed" => $bool,
			"sticky" => $bool,
			"poll" => $genericInt,
		),
		"special" => $keyID.", key `forum` (`forum`), key `user` (`user`), key `sticky` (`sticky`), key `lastpostdate` (`lastpostdate`), key `date` (`date`), fulltext key `title` (`title`)"
	),
	"threadsread" => array
	(
		"fields" => array
		(
			"id" => $genericInt,
			"thread" => $genericInt,
			"date" => $genericInt,
		),
		"special" => "primary key (`id`, `thread`)"
	),
	// cid = user who commented
	// uid = user whose profile received the comment
	"usercomments" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"uid" => $genericInt,
			"cid" => $genericInt,
			"text" => $text,
			"date" => $genericInt,
		),
		"special" => $keyID.", key `uid` (`uid`), key `date` (`date`)"
	),
	"users" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"name" => $var32,
			"displayname" => $var32,
			"password" => $var256,
			"pss" => "varchar(16)".$notNull,
			"powerlevel" => $smallerInt,
			"posts" => $genericInt,
			"regdate" => $genericInt,
			"minipic" => $var128,
			"picture" => $var128,
			"title" => $var256,
			"postheader" => $text,
			"signature" => $text,
			"bio" => $text,
			"gender" => $var128,
			"rankset" => $var128,
			"realname" => $var128,
			"lastknownbrowser" => $text,
			"location" => $var128,
			"birthday" => $genericInt,
			"email" => $var128,
			"homepageurl" => $var128,
			"homepagename" => $var128,			
			"lastposttime" => $genericInt,
			"lastactivity" => $genericInt,
			"lastip" => $var50,
			"lasturl" => $var128,
			"lastforum" => $genericInt,
			"postsperpage" => "int(8) NOT NULL DEFAULT '20'",
			"threadsperpage" => "int(8) NOT NULL DEFAULT '50'",
			"timezone" => "float NOT NULL DEFAULT '0'",
			"theme" => $var32,
			"signsep" => $bool,
			"dateformat" => $var32." DEFAULT 'm-d-y'",
			"timeformat" => $var32." DEFAULT 'h:i a'",
			"fontsize" => "int(8) NOT NULL DEFAULT '80'",
			"karma" => $genericInt,
			"blocklayouts" => $bool,
			"globalblock" => $bool,
			"usebanners" => "tinyint(1) NOT NULL DEFAULT '1'",
			"showemail" => $bool,
			"newcomments" => $bool,
			"tempbantime" => $hugeInt,
			"tempbanpl" => $smallerInt,
			"forbiddens" => $var1024,
			"pluginsettings" => $text,
			"lostkey" => $var128,
			"lostkeytimer" => $genericInt,
			"loggedin" => $bool,
			"convertpassword" => $var256,
			"convertpasswordsalt" => $var256,
			"convertpasswordtype" => $var256,
			"namecolor" => $varcolor,
		),
		"special" => $keyID.", key `posts` (`posts`), key `name` (`name`), key `lastforum` (`lastforum`), key `lastposttime` (`lastposttime`), key `lastactivity` (`lastactivity`)"
	),
	"uservotes" => array
	(
		"fields" => array
		(
			"uid" => $genericInt,
			"voter" => $genericInt,
			"up" => $bool,
		),
		"special" => "primary key (`uid`, `voter`)"
	),
);
?>
