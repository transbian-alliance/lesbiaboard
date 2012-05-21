<?php
header("Content-Type: text/css");

$curtime = getdate(time());
$min = $curtime['hours'] * 60 + $curtime['minutes'] + 340;

$hue = ($min / 2) % 360;
$sat = 50;
$hs = $hue.", ".$sat."%";

$css = "/* AcmlmBoard XD - Daily Cheese */

.PoRT, .pollbarContainer, .pollbar, table.post, .post_about, .post_topbar, .post_content, .PoRT .errort, .PoRT .errorc, table.post td.side, table.post td.userlink, table.post td.meta, table.post td.post, table.outline, table.outline tr:first-child th:first-child, table.outline tr:first-child th:last-child, div#tabs button
{
	border-radius: 0px;
}

.outline, .PoRT
{
	outline: 0px none;
	box-shadow: none;
}

table.outline
{
	border: 0px none;
}

.faq
{
	border: 1px solid hsl([huesat], 5%);
	background: hsl([huesat], 11%);
}

body
{
	background: #000;
	color: hsl([huesat], 75%);
}

.header0, .header1
{
	border: 1px solid hsl([huesat], 5%);
}

.header0 th
{
	background: hsl([huesat], 20%);
	color: hsl([huesat], 75%);
}

.header1 th
{
	background: hsl([huesat], 25%);
	color: hsl([huesat], 75%);
}

.cell1, table.post td.post
{
	background: hsl([huesat], 11%);
}

.cell0, table.post td.side, table.post td.userlink, table.post td.meta
{
	background: hsl([huesat], 15%);
}

.cell2
{
	background: hsl([huesat], 8%);
}

.errort
{
	background: hsl([huesat], 25%);
	border: 1px solid hsl([huesat], 5%);
	color: hsl([huesat], 75%);
}

.errorc
{
	background: hsl([huesat], 15%);
	border: 1px solid hsl([huesat], 5%);
	border-top: 0px;
}

table
{
}

td, th
{
	border: 1px solid hsl([huesat], 5%);
}

table.outline tr:first-child th:last-child
{
	border-right: 1px solid hsl([huesat], 5%);
}
table.outline tr:first-child th
{
	border-top: 1px solid hsl([huesat], 5%);
}
table.outline td
{
	border-left: 0px none;
	border-top: 0px none;
}
table.outline th
{
	border-bottom: 1px solid hsl([huesat], 5%);
}
table.outline tr td:first-child, table.outline tr th:first-child
{
	border-left: 1px solid hsl([huesat], 5%);
}


table.post
{
	border-collapse: collapse;
}

table.post td.side
{
	border-left: 1px solid hsl([huesat], 5%);
	border-bottom: 1px solid hsl([huesat], 5%);
}

table.post td.userlink
{
	border-top: 1px solid hsl([huesat], 5%);
	border-bottom: 0px none;
}

table.post td.meta
{
	border-top: 1px solid hsl([huesat], 5%);
	border-right: 1px solid hsl([huesat], 5%);
}

table.post td.post
{
	border: 1px solid hsl([huesat], 5%);
}



button, input[type=\"submit\"]
{
	border: 1px solid hsl([huesat], 5%);
	background: hsl([huesat], 15%);
	color: hsl([huesat], 75%);
}

input[type=\"text\"], input[type=\"password\"], input[type=\"file\"], input[type=\"email\"], select, textarea
{
	background: hsl([huesat], 5%);
	border: 1px solid hsl([huesat], 25%);
	color: #fff;
}

input[type=\"checkbox\"], input[type=\"radio\"]
{
	background: hsl([huesat], 5%);
	border: 1px solid hsl([huesat], 25%);
	color: hsl([huesat], 50%);
}

div#tabs button.selected
{
	border-bottom: 1px solid hsl([huesat], 20%);
	background: hsl([huesat], 20%);
}

";

print str_replace("[huesat]", $hs, $css);

?>
