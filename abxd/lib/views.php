<?php
//  AcmlmBoard XD support - View counter support

//Update view counter
if(!$isBot)
{
	$qViewCounter = "update misc set views = views + 1";
	$rViewCounter = Query($qViewCounter);
	$misc['views']++;

	//Milestone reporting
	if($viewcountInterval > 0 && $misc['views'] > 0 && $misc['views'] % $viewcountInterval == 0)
	{
		if($loguserid)
		{
			$who = UserLink($loguser); //$loguser['name'];
			//3.0 update: give a badge
			Query("insert ignore into badges values(".$loguserid.", 'View ".number_format($misc['views'])."', 0)");
		}
		else
			$who = "a guest at ".$_SERVER['REMOTE_ADDR'];

		Query("update misc set milestone = 'View ".$misc['views']." reached by ".justEscape($who)."'");
	}
}

?>
