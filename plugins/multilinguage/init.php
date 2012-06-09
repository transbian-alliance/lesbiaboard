<?php
if (isset($loguser['linguage']))
{
	$language = $loguser['linguage'];
	if ($language !== 'en_US')
		include_once "./lib/lang/".$language."_lang.php";
}
