<?php
/* High score!
 * By Kawa
 *
 * External requirements:
 *   None!
 */

registerPlugin("High score");

function HighScore_Write()
{
	global $misc;
	$df = "l, F jS Y, G:i:s";

	write(
"
			<dt>Highest number of users in five minutes:</dt>
			<dd>
				{0}, on {1} GMT:<br />
				{2}
			</dd>
", $misc['maxusers'], gmdate($df, $misc['maxusersdate']), $misc['maxuserstext']);
}

register("admins", "HighScore_Write");

?>