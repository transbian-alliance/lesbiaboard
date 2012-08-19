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
			if (in_array(strtolower($bt['function']), array('rawquery', 'query', 'fetchresult')) && !$args)
				if (is_array($a))
					$args .= var_export(array_merge(array("..."), array_slice($a, 1)), true);
				else if (is_string($a))
					$args .= "'...'";
				else
					$args .= '???';
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
