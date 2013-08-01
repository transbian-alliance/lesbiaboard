<?php
$mcTitles = Settings::pluginGet("titles");
$mcTitles = explode("\n", $mcTitles);
	
$mcTitle = $mcTitles[array_rand($mcTitles)];
?>

	<script type="text/javascript" src="<?php print resourceLink("plugins/mctitles/makeTitle.js");?>"></script>
	<script type="text/javascript">
		window.addEventListener("load", function() {
			makeMcTitle("<?php print $mcTitle; ?>");
		}, false);
	</script>
	<link rel="stylesheet" type="text/css" href="<?php print resourceLink("plugins/mctitles/mctitles.css");?>" />
