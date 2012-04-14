<?php
//  AcmlmBoard XD - Board Settings editing page
//  Access: administrators

$title = __("Edit settings");

AssertForbidden("editSettings");

if($loguser['powerlevel'] < 3)
	Kill(__("You must be an administrator to edit the board settings."));

$plugin = "main";
if(isset($_GET["plugin"]))
	$plugin = $_GET["plugin"];
if(isset($_POST["_plugin"]))
	$plugin = $_POST["_plugin"];

$settings = Settings::getForPlugin($plugin);

if(isset($_POST["_plugin"]))
{
	//Save the settings.
	$valid = true;
		
	foreach($_POST as $key => $value)
	{
		if($key == "_plugin") continue;
		if(!isset($settings[$key])) continue; //Don't accept unexisting settings.
		print $key."<br>";
		Settings::$pluginsettings[$plugin][$key] = $value;

		if(!Settings::validate($value, $type))
			$valid = false;
	}

	if($valid)
	{
		Settings::save($plugin);
		if($plugin == "main")
			die(header("Location: ".actionLink("admin")));
		else
			die(header("Location: ".actionLink("pluginmanager")));
		
//		Alert("Settings saved");
	}
	else
		Alert("Settings were NOT saved because there were invalid values. Please correct them and try again.");
}

$plugintext = "";
if($plugin != "main")
	$plugintext = " for plugin ".$plugin;
print "
	<form action=\"".actionLink("editsettings")."\" method=\"post\">
		<input type=\"hidden\" name=\"_plugin\" value=\"$plugin\">
		<table class=\"outline margin width75\">

			<tr class=\"header1\">
				<th colspan=\"2\">
					".__("Settings")."$plugintext
				</th>
			</tr>";

$class = 0;

foreach($settings as $name => $data)
{
	$friendlyname = $name;
	if(isset($data["name"]))
		$friendlyname = $data["name"];
	
	$type = $data["type"];
	$help = $data["help"];
	$value = Settings::$pluginsettings[$plugin][$name];
	
	$input = "[Bad setting type]";
	
	$value = htmlspecialchars($value);

	if($type == "boolean")
		$input = makeSelect($name, $value, array(1=>"Yes", 0=>"No"));
	if($type == "integer")
		$input = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" />";
	if($type == "text")
		$input = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" class=\"width75\"/>";
	if($type == "textbox" || $type == "textbbcode" || $type == "texthtml")
		$input = "<textarea id=\"$name\" name=\"$name\" rows=\"8\" style=\"width: 98%;\">$value</textarea>";
	if($type == "forum")
		$input = makeForumList($name, $value);
	if($type == "theme")
		$input = makeThemeList($name, $value);
	if($type == "layout")
		$input = makeLayoutList($name, $value);
	
	if($help)
		$help = "<img src=\"img/icons/icon4.png\" title=\"$help\" alt=\"[!]\" />";
	
	print "<tr class=\"cell$class\">
				<td>
					<label for=\"$name\">$friendlyname</label>
				</td>
				<td>
					$input
					$help
				</td>
			</tr>";
	$class = ($class+1)%2;
}

print "			<tr class=\"cell2\">
				<td>
				</td>
				<td>
					<input type=\"submit\" name=\"_action\" value=\"".__("Save")."\" />
					<input type=\"hidden\" name=\"key\" value=\"{31}\" />
				</td>
			</tr>
		</table>
	</form>
";

function makeSelect($fieldName, $checkedIndex, $choicesList, $extras = "")
{
	$checks[$checkedIndex] = " selected=\"selected\"";
	foreach($choicesList as $key=>$val)
		$options .= format("
						<option value=\"{0}\"{1}>{2}</option>", $key, $checks[$key], $val);
	$result = format(
"
					<select id=\"{0}\" name=\"{0}\" size=\"1\" {1} >{2}
					</select>", $fieldName, $extras, $options);
	return $result;
}

function prepare($text)
{
	$s = str_replace("\\'", "'", addslashes($text));
	return $s;
}


function makeForumList($fieldname, $selectedID)
{
	global $fid, $loguser;
	
	$lastCatID = -1;	
	$rFora = Query("	SELECT 
							f.id, f.title, f.catid,
							c.name cname
						FROM 
							forums f
							LEFT JOIN categories c ON c.id=f.catid
						ORDER BY c.corder, c.id, f.forder");
	
	$theList = "";
	$optgroup = "";
	while($forum = Fetch($rFora))
	{
		if($forum['catid'] != $lastCatID)
		{
			$lastCatID = $forum['catid'];
			$theList .= format(
"
			{0}
			<optgroup label=\"{1}\">
", $optgroup, strip_tags($forum['cname']));
			$optgroup = "</optgroup>";
		}

		$theList .= format(
"
				<option value=\"{0}\"{2}>{1}</option>
", $forum['id'], strip_tags($forum['title']), ($forum['id'] == $selectedID ? " selected=\"selected\"" : ""));
	}
	
	return "<select id=\"$fieldname\" name=\"$fieldname\">$theList</select>";
}

function makeThemeList($fieldname, $value)
{
	$themes = array();
	$dir = @opendir("themes");
	while ($file = readdir($dir)) {
		if ($file != "." && $file != "..") {
			$name = explode("\n", @file_get_contents("./themes/".$file."/themeinfo.txt"));
			$themes[$file] = trim($name[0]);
		}
	}
	closedir($dir);
	return makeSelect($fieldname, $value, $themes);
}

function makeLayoutList($fieldname, $value)
{
	$layouts = array();
	$dir = @opendir("layouts");
	while ($file = readdir($dir)) {
		if (endsWith($file, ".php")) {
			$layout = substr($file, 0, strlen($file)-4);
			$layouts[$layout] = @file_get_contents("./layouts/".$layout.".info.txt");
		}
	}
	closedir($dir);
	return makeSelect($fieldname, $value, $layouts);
}

//From the PHP Manual User Comments
function foldersize($path)
{
	$total_size = 0;
	$files = scandir($path);
	$files = array_slice($files, 2);
	foreach($files as $t)
	{
		if(is_dir($t))
		{
			//Recurse here
			$size = foldersize($path . "/" . $t);
			$total_size += $size;
		}
		else
		{
			$size = filesize($path . "/" . $t);
			$total_size += $size;
		}
	}
	return $total_size;
}

?>
