<?php

function LayoutMaker_Header($tag)
{
	if($tag == "top")
		Write(
"
	<li>
		<a href=\"layoutmaker.php\">Layout maker</a>
	</li>
");
}

register("headers", "LayoutMaker_Header", 1);

?>