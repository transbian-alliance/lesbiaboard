<?php

if (!$self['forumid']) continue;
if($forum['id'] == $self['forumid'])
{
	$rndTitles = file_get_contents("./plugins/".$self['dir']."/titles.txt");
	$rndTitles = explode("\n", $rndTitles);
	$rndDescs = file_get_contents("./plugins/".$self['dir']."/descs.txt");
	$rndDescs = explode("\n", $rndDescs);

	$forum['title'] = $rndTitles[array_rand($rndTitles)];
	$forum['description'] = $rndDescs[array_rand($rndDescs)];
}

?>