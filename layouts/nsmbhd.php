<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
	<title><?php print $layout_title?></title>
	<link rel="stylesheet" type="text/css" href="<?php print resourceLink("layouts/nsmbhd.css");?>" />
	<?php include("header.php"); ?>
</head>

<body style="width:100%; font-size: <?php print $loguser['fontsize']; ?>%;">
<div id="header">
	<div id="boardheader">

		<!-- Board header goes here -->
		<table>
			<tr>
				<td style="border: 0px none; text-align: left;">
					<a href="<?php print resourceLink("");?>">
						<img id="theme_banner" src="<?php print htmlspecialchars($layout_logopic); ?>" alt="" title="<?php print htmlspecialchars($layout_title); ?>" style="padding: 8px;" />
					</a>
				</td>
				<td style="border: 0px none; text-align: left;">
							<?php if($layout_pora) { ?>
							<td style="border: 0px none;">
								<?php print $layout_pora; ?>
							</td>
							<?php } ?>
				</td>

				<td style="border: 0px none; text-align: right; padding:0px; vertical-align:bottom;" class="smallFonts">
					<div class="cell1" style="float:right; padding:5px; border-top:1px solid black; border-left:1px solid black;">
						<ul class="pipemenu">
							<?php print $layout_userpanel; ?>
						</ul>
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
</div>
	<div id="sidebar">

		<table class="outline margin" style="width:130px; ">
			<tr class="header1">
				<th>Navigation
				</th>
			</tr>
			<tr class="cell0">
				<td>
					<ul class="sidemenu">
					<?php print $layout_navigation;?>
					</ul>
				</td>
			</tr>
		</table>
	</div>

	<div id="main">
<form action="<?php print actionLink('login'); ?>" method="post" id="logout">
		<input type="hidden" name="action" value="logout" />
	</form>
	
	<?php print $layout_bars; ?>
	<?php print $layout_crumbs;?>
	<?php print $layout_contents;?>
	<?php print $layout_crumbs;?>

	</div>
	<div class="footer" style='clear:both;'>
	<?php print $layout_footer;?>
	</div>
</body>
</html>
