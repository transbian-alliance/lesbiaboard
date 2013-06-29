<?php
function loadSmilies()
{
	global $smilies, $smiliesReplaceOrig, $smiliesReplaceNew;

	$rSmilies = Query("select * from {smilies} order by length(code) desc");
	$smilies = array();

	while($smiley = Fetch($rSmilies))
		$smilies[] = $smiley;

	$smiliesReplaceOrig = $smiliesReplaceNew = array();
	for ($i = 0; $i < count($smilies); $i++)
	{
		$smiliesReplaceOrig[$smilies[0]['code']][] = '/\G(?<!\w)'.preg_quote($smilies[$i]['code'], "/").'(?!\w)/';
		$smiliesReplaceNew[$smilies[0]['code']][] = resourceLink("img/smilies/".$smilies[$i]['image']);
	}
}


function loadSmiliesOrdered()
{
	global $smiliesOrdered;

	$rSmilies = Query("select * from {smilies}");
	$smilies = array();

	while($smiley = Fetch($rSmilies))
		$smiliesOrdered[] = $smiley;
}
