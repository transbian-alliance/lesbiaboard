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
	<div id="mobile_sidebar">
		<img id="theme_banner" style="width:100%" src="<?php print htmlspecialchars($layout_logopic); ?>" alt="" title="<?php print htmlspecialchars($layout_title); ?>" style="padding: 8px;" />
									
		<div id="mobile_online">
			<?php print $layout_onlineusers; ?> &nbsp;&mdash;&nbsp;
			<?php print $layout_views; ?>
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
		<?php print $layout_links->build(); ?>
		&nbsp;
		<?php print $layout_navigation->build(); ?>
		&nbsp;
		<?php print $layout_userpanel->build(); ?>
	</div>
	<div id="body">
		<div id="mobile_headerBar" class="cell0">
			<table style="width:100%"><tr>
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
					$now = $now2."&nbsp;&nbsp;&mdash;&nbsp;&nbsp;&nbsp;".$now;
				}		
				if($last2 == NULL)
					$last2 = $layout_crumbs->pop();
			
				if($last2 != NULL)
					echo "<td style=\"width:40px\"><a class=\"button\" href=\"".htmlspecialchars($last2->getLink())."\"><img style=\"vertical-align:bottom; width:24px; height:24px;\" src=\"".resourceLink("img/mobile-back.png")."\"/></a></td>";
				echo "<td>$now</td>";
			?>
			<td style="width:40px">
				<a id="mobile_openHeader" href="#" class="button"><img style="width:24px; height:24px;" src="<?php echo resourceLink("img/mobile-menu.png");?>"/></a>
			</td>
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
	</div>
</body>
</html>
