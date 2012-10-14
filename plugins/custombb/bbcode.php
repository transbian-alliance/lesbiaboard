<?php

include_once('plugins/custombb/defines.php');

if(file_exists(BB_FILE)){
	$bbcodes = unserialize(file_get_contents(BB_FILE));
}
else{
	$bbcodes = array();
}

$bb_regexpes = array(
	BB_NONE   => '',
	BB_TEXT   =>'(.*)',
	BB_ID     =>'(\w*)',
	BB_NUMBER =>'([0-9]*)',
	BB_COLOR  =>'\#?([0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?)'
);

foreach($bbcodes as $bbcode){
	$bbcodeCallbacks[$bbcode['name']] = function ($content, $arg) use ($bbcode, $bb_regexpes) {
		$content = preg_replace("(^{$bb_regexpes[$bbcode['text']]}$)", '$1', $content, 1, $con1);
		$arg = preg_replace("(^{$bb_regexpes[$bbcode['value']]}$)", '$1', $arg, 1, $con2);
		if (!$con1 || !$con2) return "[$bbcode[name]=".htmlspecialchars($arg)."]${content}[/$bbcode[name]]";
		return str_replace(array('{V}', '{T}'), array(htmlspecialchars($arg), $content), $bbcode['html']);
	};
}
