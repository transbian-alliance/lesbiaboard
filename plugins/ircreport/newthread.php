<?php

ircReport("New thread by "
	.$postingAsUser["name"]
	.": "
	.$thread["title"]
	."(".$forum["title"].")"
	." "
	.getServerURL()."?tid=".$tid
	);
