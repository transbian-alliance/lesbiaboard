<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
	<title><?php print $layout_title?></title>
	<meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta name="description" content="<?php print $metaDescription; ?>" />
	<meta name="keywords" content="<?php print $metaKeywords; ?>" />
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="<?php print resourceLink("css/common.css");?>" />
	<link rel="stylesheet" type="text/css" href="<?php print resourceLink("css/nsmbhd-common.css");?>" />
	<link rel="stylesheet" type="text/css" href="<?php print themeResourceLink("style.css");?>" />

	<script type="text/javascript" src="<?php print resourceLink("lib/tricks.js");?>"></script>
	<script type="text/javascript" src="<?php print resourceLink("lib/jquery.js");?>"></script>
	<script type="text/javascript" src="<?php print resourceLink("lib/jquery.tablednd_0_5.js");?>"></script>

</head>

<body style="width:100%; font-size: <?php print $loguser['fontsize']; ?>%;">
	
	<div id="boardheader">

		<!-- Board header goes here -->
		<table>
			<tr>
				<td style="border: 0px none; text-align: left;">
					<a href="<?php print resourceLink("");?>">
						<img src="<?php print htmlspecialchars($layout_logopic); ?>" alt="" title="<?php print htmlspecialchars($layout_title); ?>" style="padding: 8px;" />
					</a>
				</td>

				<td style="border: 0px none; text-align: right; padding:0px; vertical-align:bottom;" class="smallFonts">
					<div class="cell1" style="float:right; padding:5px; border-top:1px solid black; border-left:1px solid black;">
						<?php print $layout_userpanel; ?>
					</div>
				</td>
			</tr>
		</table>
	</div> <!--END OF HEADER-->
	
	<div id="boardheader2" class="cell1">
		<span style="position: absolute;left: 6px;"><?php print $layout_views; ?></span></span>
		<?php print $layout_onlineusers; ?>
		<span style="position: absolute;right: 6px;"><?php print $layout_time; ?></span>
		<?php print $layout_birthdays; ?>
	</div>
	
	<div id="sidebar">

		<table class="outline margin" style="width:130px; ">
			<tr class="header1">
				<th>Navigation
				</th>
			</tr>
			<tr class="cell0">
				<td>
					<?php print $layout_navigation;?>
				</td>
			</tr>
		</table>
	</div>

	<div id="main">
<form action="<?php print actionLink('login'); ?>" method="post" id="logout">
		<input type="hidden" name="action" value="logout" />
	</form>
	
	<?php print $layout_crumbs;?>
	<?php print $layout_contents;?>
	<?php print $layout_crumbs;?>

	</div>
	<div class="footer" style='clear:both;'>
	<?php print $layout_footer;?>
	</div>
</body>
</html>
