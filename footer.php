<?php
if (!$mobileLayout) echo '<a href="#" onclick="enableMobileLayout(1); return false;" rel="nofollow">Mobile view</a>';
else echo '<a href="#" onclick="enableMobileLayout(-1); return false;" rel="nofollow">Disable mobile view</a>';
?>
<br>
<br>

<?php $bucket = "footer"; include("./lib/pluginloader.php");?>
Powered by <a href="https://github.com/transbian-alliance/lesbiaboard">Lesbiaboard</a> <?php doBoardVersionFooter(); ?> by <a href="http://transbian.love/">Transbian Alliance</a><br />
Acmlmboard XD &copy; Dirbaio, xfix, Kawa, StapleButter, Nadia, et al<br />
AcmlmBoard &copy; Jean-Fran&ccedil;ois Lapointe<br />
<?php print __("<!-- English translation by The ABXD Team -->")?>

<?php print (isset($footerButtons) ? $footerButtons : "")?>
<?php print (isset($footerExtensionsB) ? $footerExtensionsB : "")?>


