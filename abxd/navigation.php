
					<ul class="sidemenu">
						<?php
							if($loguser['powerlevel'] > 2 && IsAllowed("viewAdminRoom"))
								print "<li><a href=\"admin.php\">".__("Admin")."</a></li>"; ?>
								
							<li><a href="./">Main</a></li>
							<li><a href="board.php">Forums</a></li>
							<li class="sidemenuseparator"> </li>
							<li><a href="uploader.php">Uploader</a></li>
							<li><a href="spriteDB.php">Sprite Database</a></li>
							<li><a href="http://code.google.com/p/nsmb-editor/" target="_blank">Google Code</a></li>
							<li><a href="builds.php">NSMBe Downloads</a></li>
							<li><a href="irc.php">IRC chat</a></li>
							<li class="sidemenuseparator"> </li>
							<li><a href="faq.php">FAQ</a></li>
							<li><a href="memberlist.php">Member list</a></li>
							<li><a href="ranks.php">Ranks</a></li>
<!--										<li><a href="calendar.php">Calendar</a></li>
							<li><a href="avatarlibrary.php">Avatars</a></li>-->
							<li><a href="online.php">Online users</a></li>
							<li><a href="search.php">Search</a></li>
							<li><a href="lastposts.php">Last posts</a></li>

						<?php
							$bucket = "topMenu"; include("./lib/pluginloader.php");
						?>

					</ul>

