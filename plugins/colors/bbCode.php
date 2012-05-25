<?php

	$s = preg_replace("'\[color=[\'\"]([A-Za-z#0-9]*?)[\'\"]\](.*?)\[/color\]'si","<span style=\"color: \\1\">\\2</span>", $s);
	$s = preg_replace("'\[color=([A-Za-z#0-9]*?)\](.*?)\[/color\]'si","<span style=\"color: \\1\">\\2</span>", $s);

?>