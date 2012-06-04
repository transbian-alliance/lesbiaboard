<?php $bucket = "footer"; include("./lib/pluginloader.php");?>

<?php print $footerExtensionsA?>

Powered by <a href="https://github.com/Dirbaio/ABXD">AcmlmBoard XD</a><br />
By Dirbaio, Kawa, Mega-Mario, Nikolaj, et al<br />
AcmlmBoard &copy; Jean-Fran&ccedil;ois Lapointe<br />
Page rendered in <?php print sprintf("%1.3f",usectime()-$timeStart)?> seconds with <?php print Plural($queries, __("MySQL query"))?> <br />
<?php print __("<!-- English translation by Kawa -->")?>

<?php print $footerButtons?>
<?php print $footerExtensionsB?>


