<?php

$footerExtensionsA .= Format(
"
	<script>
	(function () {
		var i = 0;
		for (; i < " . ((int) $selfsettings['goombas']) . "; ++i) {
			setTimeout(function () {
				new Goomba();
			}, " . ((float) $selfsettings['interval']) . " * i);
		}
	})();
	</script>
");

?>