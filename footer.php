<?php $bucket = "footer"; include("./lib/pluginloader.php");?>

<?php print $footerExtensionsA?>

Powered by <a href="https://github.com/Dirbaio/ABXD">AcmlmBoard XD</a><br />
By Dirbaio, Kawa, Mega-Mario, Nikolaj, et al<br />
AcmlmBoard &copy; Jean-Fran&ccedil;ois Lapointe<br />
Page rendered in <?php print sprintf("%1.3f",usectime()-$timeStart)?> seconds with <?php print Plural($queries, __("MySQL query"))?> <br />
<?php print __("<!-- English translation by Kawa -->")?>
<a href="http://validator.w3.org/check?uri=referer">
	<img src="img/xhtml10.png" alt="Valid XHTML 1.0 Transitional" />
</a>
<a href="http://jigsaw.w3.org/css-validator/">
	<img src="img/css.png" alt="Valid CSS!" />
</a>
<a href="http://abxd.dirbaio.net/">
	<img src="img/getabxd.png" alt="Get a copy for yourself" />
</a>

<?php print $footerButtons?>
<?php print $footerExtensionsB?>


