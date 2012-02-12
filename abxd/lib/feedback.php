<?php
//  AcmlmBoard XD support - System feedback

function Debug($s)
{
	write("<strong>Debug</strong>: {0}<br />", $s);
}

//	Not really much different to kill()
function Alert($s, $t="")
{
	if($t=="")
		$t = __("Alert");
	write("
	<div class=\"outline margin\">
		<div class=\"errort\">
			<strong>{1}</strong>
		</div>
		<div class=\"errorc cell2\">
			{0}
		</div>
	</div>", $s, $t);
}

function Kill($s, $t="")
{
	if($t=="")
		$t = __("Error");
	Alert($s, $t);
	throw new KillException();
}

function dieAjax($what)
{
	global $ajaxPage;
	
	echo $what;
	$ajaxPage = true;
	throw new KillException();
}

function Redirect($s,$t,$n)
{
	write(
"
	<div class=\"outline margin\">
		<div class=\"errort\">
			<strong>{0}</strong>
		</div>
		<div class=\"errorc cell2\">
			".__("You will now be redirected to {3}&hellip;")."
			<div class=\"pollbarContainer\" style=\"margin: 4px auto; width: 25%; display: none;\">
				<div class=\"pollbar\" id=\"theBar\" style=\"background: silver; width: 1%;\">&nbsp;</div>
			</div>
		</div>
	</div>
	<meta http-equiv=\"REFRESH\" content=\"5;URL={1}\" />
	<script type=\"text/javascript\">
		var barWidth = 1;
		var target = \"{1}\";
		
		function doBar()
		{
			barWidth += 5; //use 2 here for smoother animation
			if (barWidth > 101)
			{
				document.location = target;
			}
			else
			{
				if(barWidth > 100)
					theBar.style['width'] = \"100%\";
				else
					theBar.style['width'] = barWidth + \"%\";
				setTimeout(\"doBar()\", 50); //use 20 here for smoother animation
			}
		}
		
		function startBar()
		{
			theBar = document.getElementById(\"theBar\");
			theBar.parentNode.style['display'] = \"block\";
			doBar();
		}
		
		window.addEventListener(\"load\",  startBar, false);
	</script>
",	$s, $t, $n, "<a href=\"".$t."\">".$n."</a>");
	exit();
}

?>
