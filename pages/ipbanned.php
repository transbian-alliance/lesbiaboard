<table class="outline margin center" style="width: 60%; overflow: auto; margin: auto; margin-top: 40px; margin-bottom: 40px;">
<tr><td class="cell0" style="padding:60px">
<?php
$ipban['date'] = (int) $ipban['date'];
if($ipban['date'])
	print format(__("You have been IP-banned from this board until {0}. That's {1} left."),
				gmdate("M jS Y, G:i:s",$ipban['date']),
				TimeUnits($ipban['date']-time()));
else
	print __("You have been <strong>permanently</strong> IP-banned from this board");
print "<br />";
print __("Attempting to get around this in any way will result in worse things.");
//$bucket = "ipbanned"; include('lib/pluginloader.php');
?>

</td></tr></table>
