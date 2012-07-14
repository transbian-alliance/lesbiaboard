<?php
$faqscript = file_get_contents("pages/faq.php");
$faqscript = str_replace('file_get_contents("lib/lang/faq_".$language.".html")', 'Settings::pluginGet("faq")', $faqscript);
eval("?>$faqscript");
unset($faqscript);
?>
