var headerShown = false;

function mobile_openHeader()
{
	$('.mobile_openHeader').toggleClass("selected");
	if(headerShown)
	{
		$("#mobile_header").css("display", "none");
		$(window).unbind('scroll');
		$("body").unbind('scroll');
		document.body.style.overflow = "visible";
	}
	else
	{
		$("#mobile_header").css("display", "block");
		$("#mobile_header").scrollTop(0);
		$('#mobile_header').css("height", $(window).height());
		$(window).bind("scroll", function(e){e.preventDefault(); return false;});
		$("body").bind("scroll", function(e){e.preventDefault(); return false;});
		document.body.style.overflow = "hidden";
	}
	headerShown = !headerShown;
}

$(window).resize(function() {
	$('#mobile_header').css("height", $(window).height());
});
