$(document).ready(function() {
	//Grab the things we need.
	$to = $("#to");
	$addReceiver = $("#addReceiver");
	$addReceiverFormContainer = $("#addReceiverFormContainer");
	$receiver = $("#receiver");
	$done = $("#done");

	//Hook up some event handlers
	$addReceiver.click(function(e) {
		e.preventDefault();
		$addReceiver.css("position", "absolute");
		$addReceiver.slideUp(250);
		$addReceiverFormContainer.fadeIn(250, function() {
			$addReceiver.css("position", "default")
			$receiver.focus();
		});
	});

	$done.click(function(e) {
		e.preventDefault();
		$to.append("<div style=\"display: inline-block;\">" + $receiver.attr("value") + "</div>");
		$addReceiverFormContainer.hide();
		$addReceiver.css("position", "absolute");
		$addReceiver.slideDown(250, function() {
			$addReceiver.css("position", "default");
		});
	});
});
		
