<?php

$footerExtensionsA .= Format(
"
	<script type="text/javascript">
	(function () {
		var i = 0;
		for (; i < " . ((int) Settings::pluginGet('goombas')) . "; ++i) {
			setTimeout(function () {
				new Goomba();
			}, " . ((float) Settings::pluginGet('interval')) . " * i);
		}
	})();
	</script>
");

?>
