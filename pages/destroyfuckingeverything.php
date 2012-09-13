<?php
Kill("This feature is VERRRY DANGEROUS and has been disabled");
if($loguser['powerlevel'] != 4)
	Kill(__("You're not a root user. There is nothing for you here."));

if(!isset($_GET['doitfaggot']))
	Kill(__("Are you sure?")."<br/><br/><button style=\"font-size: 150%; margin: 0.5em; padding: 0px 1em;\" onclick=\"document.location = '".actionLink("destroyfuckingeverything", 0, "doitfaggot")."';\">DO IT FAGGOT</button>", __("Oh boy."));

$tables = array
(
	"blockedlayouts",
	"categories",
	"forummods",
	"forums",
	"groupaffiliations",
	"groups",
	"guests",
	"ignoredforums",
	"ip2c",
	"lastsearches",
	"moodavatars",
	"pmsgs",
	"pmsgs_text",
	"poll",
	"pollvotes",
	"poll_choices",
	"postradar",
	"posts",
	"posts_text",
	"rpgbattles",
	"rpgitems",
	"threads",
	"threadsread",
	"uploader",
	"usercomments",
	"users",
	"users_rpg",
	"uservotes",
);

foreach($tables as $table)
	Query("truncate {0}", $table);

KillFolder("uploader");
mkdir("uploader");

Kill(__("You just destroyed the board."), __("Congratulations."));
Report("[b]".$loguser['name']."[/] destroyed the board!");

function KillFolder($folderPath)
{
	if(is_dir($folderPath))
	{
		foreach(scandir($folderPath) as $value)
		{
			if($value != "." && $value != "..")
			{
				$value = $folderPath."/".$value;
				if(is_dir($value))
					KillFolder($value);
				else if(is_file($value))
					@unlink($value);
			}
		}
		return rmdir($folderPath);
	}
	else
		return FALSE;
}

?>
