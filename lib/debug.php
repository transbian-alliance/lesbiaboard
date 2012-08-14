<?php


function backTrace($backtrace)
{
	foreach ($backtrace as $bt) {
		$args = '';
		foreach ($bt['args'] as $a) {
			if ($args) {
				$args .= ', ';
			}
			if (in_array($bt['function'], array('RawQuery', 'Query', 'FetchResult')) && !$args)
				$args .= '"..."';
			else
				$args .= var_export($a, true);
		}
		$output .= "<td>{$bt['file']}<td>{$bt['line']}<td><code>";
		$output .= htmlspecialchars("{$bt['class']}{$bt['type']}{$bt['function']}($args)");
		$output .= "</code><tr class=cell0>";
	}
	return $output;
}


function var_format($v) // pretty-print var_export
{
	return (str_replace(array("\n"," ","array"),
array("<br>","&nbsp;","&nbsp;<i>array</i>"),
var_export($v,true))."<br>");
}
