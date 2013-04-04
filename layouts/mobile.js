var headerShown = false;

function mobile_openHeader()
{
	$('.mobile_openHeader').toggleClass("selected");
	if(headerShown)
		$("#mobile_header").css("display", "none");
	else
		$("#mobile_header").css("display", "block");

	headerShown = !headerShown;
}

