<?php
	if(IsAllowed("viewCalendar") && !$isBot)
		print actionLinkTagItem(__("Calendar"), "calendar");
?>
