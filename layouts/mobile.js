var headerShown = false;



function mobile_openHeader() {
	$('.mobile_openHeader').toggleClass("selected");

	/* This is used for preventing scrolling on the body element */
	var body = document.getElementsByTagName("body")[0];
	var pContents = document.getElementById("page_contents");
	var header = document.getElementById("mobile_header");

	if(!headerShown) {
		$(header).addClass("shown");
		pContents.addEventListener("touchstart", closeOnTouch, false);
		body.addEventListener("touchmove", bListener, false);
	} else {
		$(header).removeClass("shown");
		pContents.removeEventListener("touchstart", closeOnTouch, false);
		body.removeEventListener("touchmove", bListener, false);
	}

	headerShown = !headerShown;
}

var closeOnTouch = function(event) {
	mobile_openHeader();
};

var bListener = function(event) {
	event.preventDefault();
};
