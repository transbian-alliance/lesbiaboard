<?php
if(isAllowed("viewUploader"))
	$navigation->add(new PipeMenuLinkItem(__("Uploader"), "uploader"));
