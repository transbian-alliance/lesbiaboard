<?php
include("lib/common.php");

if($loguser['powerlevel'] != 4)
	Kill(__("You're not a root user. There is nothing for you here."));
	
$key = hash('sha256', "{$loguserid},{$loguser['pss']},{$salt}");

$quickQueries = array
(
	"Remove display names" => "update users set displayname = ''",
	"Peek at uservotes" => "select taker.id, taker.name, giver.id, giver.name, up * 2 - 1 from uservotes left join users as taker on taker.id = uid left join users as giver on giver.id = voter order by taker.id"
);

if(isset($_GET['quick']))
{
	$quick = (int)$_GET['quick'];
	$vals = array_values($quickQueries);
	$_POST['command'] = $vals[$quick];
}

$quickList = "";
$quickKeys = array_keys($quickQueries);
foreach($quickKeys as $i => $n)
	$quickList .= format(
"
	<li>
		<a href=\"sql.php?quick={0}\">{1}</a>
	</li>
", $i, $n);
write(
"
	<div class=\"PoRT margin\" style=\"width: 180px;\">
		<div class=\"errort\"><strong>Quick Queries</strong></div>
		<ul class=\"errorc left cell0\" style=\"margin:0; padding:0;\">
			{0}
		</ul>
	</div>
", $quickList);

write(
"
<form method=\"POST\" action=\"sql.php\">
	<table class=\"margin outline width75\">
		<tr class=\"header1\">
			<th>
				SQL frontend
			</th>
		</tr>
		<tr class=\"cell0\">
			<td class=\"center\">
				<input type=\"text\" name=\"command\" style=\"width: 98%;\" value=\"{0}\" />
			</td>
		</tr>
		<tr class=\"cell2\">
			<td class=\"right\">
				<input type=\"submit\" />
				<input type=\"hidden\" name=\"key\" value=\"{1}\" />
			</td>
		</tr>
	</table>
</form>
", htmlspecialchars($_POST['command']), $key);

if(isset($_POST['command']))
{
	if ($_POST['key'] != $key)
		Kill('No.');
	
	$q = StripGarbage($_POST['command']);

	$forbidden = array(
		"@^((DROP|TRUNCATE)[a-z\s]+?TABLE\s|DELETE[a-z\s\.`]+?FROM\s|(INSERT|REPLACE)[a-z\s]+?(INTO)?\s?|UPDATE\s)`?reports`?@si",
		"@\sINTO\sOUTFILE\s['\"].*?['\"]@si",
		"@LOAD_FILE\s*?\(.*?\)@si",
		"@^LOAD\sDATA[a-z\s]+?INFILE@si",
		"@^(GRANT|REVOKE|SET\sPASSWORD|SHUTDOWN)\s@si",
	);
	
	foreach ($forbidden as $fq)
		if (preg_match($fq, $q))
			Kill('No.');
		
	$r = Query($q);
	$a = @mysql_affected_rows($r);
	$n = @mysql_num_rows($r);
	if($a || $n)
	{
		write(
"
<div class=\"outline margin cell2 width25\">
	{0} {1}
</div>
", ($a ? Plural($a, "affected row")."." : ""), ($n ? Plural($n, "returned row")."." : ""));
	}
	if(strtolower(substr($q,0,6)) == "select")
	{
		if($n == 1)
		{
			$row = mysql_fetch_row($r);
			for($i = 0; $i < mysql_num_fields($r); $i++)
			{
				$cellClass = ($cellClass + 1) % 2;
				$meta = mysql_fetch_field($r, $i);
				$rows .= format(
	"
		<tr>
			<td class=\"cell2\">{0}</td>
			<td class=\"cell{2}\">{1}</td>
		</tr>
	", htmlspecialchars($meta->name), htmlspecialchars($row[$i]), $cellClass);
			}
			write(
	"
	<table class=\"outline margin\">
		<tr class=\"header1\">
			<th>Field</th>
			<th>Value</th>
		</tr>
		{0}
	</table>
	",	$rows);
		}
		else
		{
			$hdr = "";
			for($i = 0; $i < mysql_num_fields($r); $i++)
			{
				$meta = mysql_fetch_field($r, $i);
				$hdr .= format(
	"
			<th>{0}</th>
	", htmlspecialchars($meta->name));
			}
			$rows = "";
			$cellClass = ($cellClass + 1) % 2;
			while($row = mysql_fetch_row($r))
			{
				$thisRow = "";
				for($i = 0; $i < mysql_num_fields($r); $i++)
				{
					$cellClass2 = ($cellClass2 + 1) % 2;
					$thisRow .= format(
	"
			<td class=\"cell{1}\">{0}</td>
	", htmlspecialchars($row[$i]), $cellClass + $cellClass2);
				}
				$rows .= format(
	"
		<tr class=\"cell0\">
			{0}
		</tr>
	", $thisRow);
			}
			write(
	"
	<table class=\"outline margin\">
		<tr class=\"header1\">
			{0}
		</tr>
		{1}
	</table>
	",	$hdr, $rows);
		}
	}
}

function StripGarbage($q)
{
	$q = preg_replace("@/\*.*?\*/@si", ' ', $q);
	$q = trim($q);
	$q = preg_replace("@\s{1,}@si", ' ', $q);
	return $q;
}

?>