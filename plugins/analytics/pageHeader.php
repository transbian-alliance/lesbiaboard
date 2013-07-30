<?php

// Support Do Not Track header.
// http://donottrack.us/
function isDntEnabled() {
   return (isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1);
}

if(isDntEnabled() && Settings::pluginGet("dnt"))
	echo "<!-- Disabling Google Analytics because you have Do Not Track set! We're awesome like that. -->";
else
{
	$loginstatus = $loguserid?"Yes":"No";

	echo <<<EOS
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php print trim(Settings::pluginGet("trackingid"));?>']);
	  _gaq.push(['_setCustomVar', 1, 'Logged in', '$loginstatus', 2]);
	  _gaq.push(['_trackPageview']);
	   
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
EOS;

}
