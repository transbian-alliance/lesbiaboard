var sidebarShown = false;

var touchDown = false;
var touchDownX = 0;
var touchDownY = 0;

//Scrollhax only works well on Chrome Android
var scrollhax = false;

if(navigator.userAgent.indexOf("Chrome") !== -1)
	scrollhax = true;

function alwaysSidebar() {
	return window.innerWidth >= 650;
}

function showSidebar() {
	if(alwaysSidebar()) 
		return;

	if(sidebarShown)
		return;
	sidebarShown = true;

	if (scrollhax && $(document).height() > $(window).height()) {
		var scrollTop = ($('html').scrollTop()) ? $('html').scrollTop() : $('body').scrollTop(); // Works for Chrome, Firefox, IE...
		$('html').addClass('noscroll').css('top',-scrollTop);         
	}
	$("#mobile_sidebar").scrollTop(0);
	$("#mobile_sidebar").addClass("shown");
	$("#mobile_overlay").addClass("shown");
	
	return false;
}

function hideSidebar() {
	if(!sidebarShown) 
		return;

	sidebarShown = false;
	
	if(scrollhax)
	{
		var scrollTop = parseInt($('html').css('top'));
		$('html').removeClass('noscroll');
		$('html,body').scrollTop(-scrollTop);
	}
	
	$("#mobile_sidebar").removeClass("shown");
	$("#mobile_overlay").removeClass("shown");
	
	return false;
}


$(function() {

	$('#mobile_openHeader').bind('click', function() {
		if (sidebarShown === true) {
			hideSidebar();
		} else {
			showSidebar();
		}
	});
	$('#mobile_overlay').bind('click', hideSidebar);
	
	document.addEventListener('touchstart', function(event) {
		touchDown = true;
		touchDownX = event.touches[0].pageX;
		touchDownY = event.touches[0].pageY;
	}, false);
	document.addEventListener('touchmove', function(event) {
		if(alwaysSidebar()) return;
		var dx = event.changedTouches[0].pageX-touchDownX;
		var dy = event.changedTouches[0].pageY-touchDownY;
		if(touchDown && $(window).scrollLeft() == 0 && !sidebarShown && dx > 0 && Math.abs(dx) > Math.abs(dy))
			event.preventDefault();
		else
			touchDown = false;

		if(touchDown && dx > 60)
			showSidebar();
	}, false);
	
	document.addEventListener('scroll', function(event) {
		touchDown = false;
	}, false);

	$(window).on("resize", function() {
		if(alwaysSidebar())
			hideSidebar();
	});
	$("body").removeClass("preload");
});
