<?php
//TODO: support install.sql

set_time_limit(0);

include("lib/common.php");

if($loguser['powerlevel'] != 4)
	Kill(__("You're not a root user. There is nothing for you here."));

$src = "http://helmet.kafuka.org/abxd/repo/";
//$src= "http://127.0.0.1/abxd/repo/";

$disabled = array();
$installed = array();

if(isset($_GET['disable']))
{
	$name = preg_replace("/[^abcdefghijklmnopqrstuvwxyz]/", "", $_GET['disable']);
	$plugPath = "plugins/".$name."/";
	if(is_file($plugPath."plugin.settings"))
	{
		rename($plugPath."plugin.settings", $plugPath."plugin.disabled");
		Alert(Format(__("Plugin \"{0}\" disabled."), $name));
	}
	else if(is_file($plugPath."plugin.disabled"))
		Alert(Format(__("Plugin \"{0}\" is already disabled."), $name));
	else
		Alert(__("No such plugin."));
}
else if(isset($_GET['enable']))
{
	$name = preg_replace("/[^abcdefghijklmnopqrstuvwxyz]/", "", $_GET['enable']);
	$plugPath = "plugins/".$name."/";
	if(is_file($plugPath."plugin.disabled"))
	{
		rename($plugPath."plugin.disabled", $plugPath."plugin.settings");
		Alert(Format(__("Plugin \"{0}\" enabled."), $name));
	}
	else if(is_file($plugPath."plugin.enabled"))
		Alert(Format(__("Plugin \"{0}\" is already enabled."), $name));
	else
		Alert(__("No such plugin."));
}

$d = @opendir("plugins");
if($d !== false)
{
	while(($p = readdir($d)) !== FALSE)
	{
		if($p == "." || $p == "..")
			continue;
		if(is_dir("plugins/".$p))
		{
			if(is_file("plugins/".$p."/plugin.settings"))
				$installed[] = $p;
			else if(is_file("plugins/".$p."/plugin.disabled"))
				$disabled[] = $p;
		}
	}
}

if(isset($_GET['install']))
{
	$_GET['install'] = preg_replace("/[^abcdefghijklmnopqrstuvwxyz]/", "", $_GET['install']);

	$rawset = file_get_contents($src."getplugin.php?id=".$_GET['install']);
	if(substr($rawset, 0, 3) != "OK\n")
		Kill(__("Something went wrong:")."<br />".$rawset);
	$lines = explode("\n", $rawset);
	$setname = trim($lines[1]);
	$setfolder = trim($lines[2]);
	$setitems = array_slice($lines, 3);

	$plugPath = "plugins/".$setfolder;
	@mkdir($plugPath);
	foreach($setitems as $item)
	{
		$target = ($item[0] == "/") ? "" : $plugPath;
		$itemsrc = str_replace(".php", ".txt", $src.$plugPath."/".$item);
		$itemdst = $plugPath."/".$item;
		$content = file_get_contents($itemsrc);
		if($item[0] == "install.sql")
		{
			//Handle that.
			continue;
		}
		file_put_contents($itemdst, $content);
	}

	$installed[] = $setfolder;
	Alert(format(__("Installed \"{0}\" to folder \"{1}\"."), $setname, $setfolder), __("Install or upgrade"));
}

//if(!isset($_GET['install']))
{
	$rawlist = trim(@file_get_contents($src."getplugin.php?list"));
	if($rawlist == "")
		$content = __("Could not get the plugin list.");
	else
	{
		$lines = explode("\n", $rawlist);
		$items = "";
		foreach($lines as $set)
		{
			$item = explode("\t", trim($set));
			$line = Format("<span title=\"({2}) &mdash; {3}\">{1}</span> <sup>[", $item[0], $item[1], $item[2], $item[3]);
			if(in_array($item[0], $installed))
				$line .= "<a href=\"installplugin.php?disable=".$item[0]."\" title=\"".__("Disable")."\">&#x2717;</a> <a href=\"installplugin.php?install=".$item[0]."\" title=\"".__("Reinstall or Update")."\">&#x21BA;</a>]</sup>";
			else if(in_array($item[0], $disabled))
				$line .= "<a href=\"installplugin.php?enable=".$item[0]."\" title=\"".__("Enable")."\">&#x2713;</a>]</sup>";
			else
				$line = Format("<a href=\"installplugin.php?install={0}\" title=\"({2}) &mdash; {3}\">{1}</a>", $item[0], $item[1], $item[2], $item[3]);

			$extras = array();
			if($item[4] > $misc['version'])
				$extras[] = Int2Version($item[4]);
			if($item[5])
				$extras[] = formatdate($item[5]);
			if(count($extras))
				$line .= " &ndash; (".join($extras, ", ").")";

			$items .= Format(
"
				<li>
					{0}
				</li>", $line);
		}
		$content = format("
			".__("The following plugins are managed from the Repository:")."
			<ul>
				{0}
			</ul>
			".__("Any plugins not listed here are probably custom or third-party, and must be managed by hand.")."
", $items);
	}
		
	write("
	<div class=\"outline margin\" style=\"float: left; width: 40%\">
		<div class=\"errort\">
			<strong>".__("Managed plugins")."</strong>
		</div>
		<div class=\"errorc cell2\" style=\"text-align: left; padding: 8px;\">
			{0}
		</div>
	</div>", $content);
	
	die();
}



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
		return @rmdir($folderPath);
	}
	else
		return FALSE;
}

function Int2Version($i)
{
	$revision = $i % 10;
	$minor = floor($i / 10) % 10;
	$major = floor($i / 100);
	if($revision == 0)
		return $major.".".$minor;
	return $major.".".$minor.".".$revision;
}

?>