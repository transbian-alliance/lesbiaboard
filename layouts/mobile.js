var headerShown = false;

function mobile_openHeader() {
	$('.mobile_openHeader').toggleClass("selected");
	if(headerShown) {
		$("#mobile_header").removeClass("shown");
	} else {
		$("#mobile_header").addClass("shown");
	}

	headerShown = !headerShown;
}

