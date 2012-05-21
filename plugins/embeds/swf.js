/* Flashloops */
function startFlash(id)
{
	var url = $("swf" + id + "url").innerHTML;
	var mainPanel = $("swf" + id + "main");
	var playButton = $("swf" + id + "play");
	var stopButton = $("swf" + id + "stop");
	mainPanel.innerHTML = '<object data="' + url + '" style="width: 100%; height: 100%;"><embed src="' + url + '" style="width: 100%; height: 100%;" allowscriptaccess=\"never\"></embed></object>';
	playButton.className = "swfbuttonon";
	stopButton.className = "swfbuttonoff";
}
function stopFlash(id)
{
	var mainPanel = $("swf" + id + "main");
	var playButton = $("swf" + id + "play");
	var stopButton = $("swf" + id + "stop");
	mainPanel.innerHTML = '';
	playButton.className = "swfbuttonoff";
	stopButton.className = "swfbuttonon";
}
