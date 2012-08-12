<?php


function backTrace()
{
	$output = "";
    $output .= "Backtrace:\n";
    $backtrace = debug_backtrace();

    foreach ($backtrace as $bt) {
        $args = '';
        foreach ($bt['args'] as $a) {
            if (!empty($args)) {
                $args .= ', ';
            }
            switch (gettype($a)) {
            case 'integer':
            case 'double':
                $args .= $a;
                break;
            case 'string':
                $a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
                $args .= "\"$a\"";
                break;
            case 'array':
                $args .= 'Array('.count($a).')';
                break;
            case 'object':
                $args .= 'Object('.get_class($a).')';
                break;
            case 'resource':
                $args .= 'Resource('.strstr($a, '#').')';
                break;
            case 'boolean':
                $args .= $a ? 'True' : 'False';
                break;
            case 'NULL':
                $args .= 'Null';
                break;
            default:
                $args .= 'Unknown';
            }
        }
        $output .= "{$bt['file']}:{$bt['line']}\n";
        if(!isset($bt["class"])) $bt["class"] = "";
        if(!isset($bt["type"])) $bt["type"] = "";
        if(!isset($bt["function"])) $bt["function"] = "";
        $output .= "    {$bt['class']}{$bt['type']}{$bt['function']}($args)\n";
    }
    return $output;
}


function var_format($v) // pretty-print var_export
{
    return (str_replace(array("\n"," ","array"),
array("<br>","&nbsp;","&nbsp;<i>array</i>"),
var_export($v,true))."<br>");
}
