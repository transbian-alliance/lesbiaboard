<?php
// If mb exists, use it.
if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('UTF-8');
// Otherwise, we are screwed. Fix as much we can.
else
{
	function mb_strtolower($string)
	{
		return strtolower($string);
	}
	function mb_strtoupper($string)
	{
		return strtoupper($string);
	}
}
