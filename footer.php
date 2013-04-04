
<?php
if ($mobileLayout) echo 'ABXD Mobile BETA - ';
if ($_COOKIE['forcelayout']) echo '<a href="?forcelayout=0" rel="nofollow">Auto view</a>';
else if ($mobileLayout) echo '<a href="?forcelayout=-1" rel="nofollow">Force normal view</a>';
else echo '<a href="?forcelayout=1" rel="nofollow">Force mobile view [BETA]</a>';
?>
<br>
<br>
<?php $bucket = "footer"; include("./lib/pluginloader.php");?>
Powered by <a href="http://abxd.dirbaio.net/">AcmlmBoard XD</a><br />
By Dirbaio, GlitchMr, Kawa, Mega-Mario, Nikolaj, et al<br />
AcmlmBoard &copy; Jean-Fran&ccedil;ois Lapointe<br />
<?php print __("<!-- English translation by The ABXD Team -->")?>

<?php print (isset($footerButtons) ? $footerButtons : "")?>
<?php print (isset($footerExtensionsB) ? $footerExtensionsB : "")?>


