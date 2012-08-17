<?php


function backTrace($backtrace)
{
	require_once 'plugins/sourcetag/geshi.php';
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
		$output .= "<td>{$bt['file']}<td>{$bt['line']}<td>";
		$output .= geshi_highlight("{$bt['class']}{$bt['type']}{$bt['function']}($args)", 'scala', null, true);
		$output .= "<tr class=cell0>";
	}
	return $output;
}


function var_format($v) // pretty-print var_export
{
	return (str_replace(array("\n"," ","array"),
array("<br>","&nbsp;","&nbsp;<i>array</i>"),
var_export($v,true))."<br>");
}
