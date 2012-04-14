<?php

ircReport("New reply by "
	.$postingAsUser["name"]
	.": "
	.$thread["title"]
	."(".$forum["title"].")"
	." "
	.getServerURL()."?pid=".$pid
	);
	
