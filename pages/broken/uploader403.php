<?php

$file = $_SERVER['REQUEST_URI'];
$imgs = array('bmp', 'png', 'apng', 'jpg', 'jpeg', 'gif', 'tif');

foreach ($imgs as $ext)
{
	if (strtolower(substr($file, strlen($file)-strlen($ext))) == $ext)
	{
		$img = imagecreate(150, 20);
		$bg = imagecolorallocate($img, 0, 0, 0);
		$fg = imagecolorallocate($img, 255, 255, 255);
		
		imagestring($img, 4, 0, 0, 'Please use get.php', $fg);
		
		header('Content-type: image/png');
		imagepng($img);
		imagedestroy($img);
		die();
	}
}

print 'Please use get.php';

?>