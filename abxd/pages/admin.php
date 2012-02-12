<?php
//  AcmlmBoard XD - Administration hub page
//  Access: administrators


AssertForbidden("viewAdminRoom");

if($loguser['powerlevel'] < 3)
	Kill(__("You're not an administrator. There is nothing for you here."));

$title = __("Administration");

$key = hash('sha256', "{$loguserid},{$loguser['pss']},{$salt}");

$cell = 1;
function cell($content) {
	global $cell;
	$cell = ($cell == 1 ? 0 : 1);
	Write("
		<tr class=\"cell{0}\">
			<td>
				{1}
			</td>
		</tr>
	", $cell, $content);
}

Write("
	<table class=\"outline margin width50\" style=\"float: right;\">
		<tr class=\"header1\">
			<th colspan=\"2\">
				".__("Information")."
			</th>
		</tr>
");
cell(Format("
			
				".__("Last viewcount milestone")."
			</td>
			<td style=\"width: 60%;\">
				{0}
			",	$misc['milestone']));

$bucket = "adminright"; include("./lib/pluginloader.php");

write(
"
	</table>
");

$cell = 1;
Write("
	<table class=\"outline margin width25\">
		<tr class=\"header1\">
			<th>
				".__("Admin tools")."
			</th>
		</tr>
");
cell(actionLinkTag(__("Recalculate statistics"), "recalc"));
cell(actionLinkTag(__("Last Known Browsers"), "lastknownbrowsers"));
cell(actionLinkTag(__("Edit Points of Required Attention"), "editpora"));
cell(actionLinkTag(__("Manage IP bans"), "ipbans"));
cell(actionLinkTag(__("Manage local moderator assignments"), "managemods"));
cell(actionLinkTag(__("Edit forum list"), "editfora", 0, "key=".$key));
cell(actionLinkTag(__("Edit category list"), "editcats"));
cell(actionLinkTag(__("Edit settings"), "editsettings"));
cell(actionLinkTag(__("Optimize tables"), "optimize"));
cell(actionLinkTag(__("View log"), "log"));
cell(actionLinkTag(__("Update the board"), "gitpull"));
if($loguser['powerlevel'] == 4)
	cell(actionLinkTag(__("SQL Console"), "sql"));

$bucket = "adminleft"; include("./lib/pluginloader.php");

write(
"
	</table>
");
?>
