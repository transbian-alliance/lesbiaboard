<!doctype html>
<html>

<head>
	<title><?php print $layout_title?></title>
	<?php include("header.php"); ?>
	<meta name="viewport" content="user-scalable=yes, initial-scale=1.0, width=device-width" />
	<script type="text/javascript" src="<?php print resourceLink("layouts/mobile.js");?>"></script>
	<link rel="stylesheet" href="<?php print resourceLink("layouts/mobile.css");?>" type="text/css" />
</head>

<body style="width:100%; font-size: <?php print $loguser['fontsize']; ?>%;">

	<div id="mobile_headerBar" class="cell0">
		<?php 

		
			$last = $layout_crumbs->pop();
			if($last == NULL)
				$now = "<a href=\"$boardroot\">".htmlspecialchars(Settings::get("boardname"))."</a>";
			else
				$now = $last->build();
	
	
			$last2 = NULL;
			if($last != NULL && $last->getLink() == "")
			{
				$last2 = $layout_crumbs->pop();
				if($last2 == NULL)
					$now2 = "<a href=\"$boardroot\">".htmlspecialchars(Settings::get("boardname"))."</a>";
				else
					$now2 = $last2->build();
				$now = $now2."&mdash;&nbsp;&nbsp;&nbsp;".$now;
			}		
			if($last2 == NULL)
				$last2 = $layout_crumbs->pop();
			
			if($last2 != NULL)
				echo "<a href=\"".htmlspecialchars($last2->getLink())."\">&lt;</a>";
			echo $now;
		?>
		<a class="mobile_openHeader" href="#" onclick="mobile_openHeader(); return false;"> ... </a>
	</div>
	
	<div id="mobile_header" style="display:none" class="cell1">
		<div id="mobile_header_padding"></div>
		<div id="mobile_online">
			<?php print $layout_onlineusers; ?> &nbsp;&mdash;&nbsp;
			<?php print $layout_views; ?> &nbsp;&mdash;&nbsp;
			<?php print $layout_time; ?>
		</div>
		<?php if($layout_pora) { ?>
		<td style="border: 0px none;">
			<?php print $layout_pora; ?>
		</td>
		<?php } ?>

		<?php 
			$layout_navigation->setClass("stackedMenu");
			$layout_userpanel->setClass("stackedMenu");
			$layout_links->setClass("stackedMenu");
		?>
		<table><tr>
			<td style="width:50%"><div>
				<?php print $layout_navigation->build(); ?>
			</div></td>
			<td style="width:50%"><div>
				<?php print $layout_links->build(); ?>
				<?php print $layout_userpanel->build(); ?>
			</div></td>
		</tr></table>			
	</div>

	<div id="main" style="padding:8px;">

	<form action="<?php print actionLink('login'); ?>" method="post" id="logout">
		<input type="hidden" name="action" value="logout" />
	</form>

	<?php print $layout_bars; ?>
	<?php print $layout_contents;?>

	</div>
	<div class="footer" style='clear:both;'>
	<?php print $layout_footer;?>
	</div>
</body>
</html>
