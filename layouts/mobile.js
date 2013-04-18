var sidebarShown = false;
var flag = false;

function showSidebar()
{
	if(flag) return false;
	if(sidebarShown) 
	{
		hideSidebar();
		return false;
	}
	
	sidebarShown = true;

	flag = true;
	setTimeout(function(){ flag = false; }, 500);

	$("#body").addClass("shown");
	$('body').css('overflow','hidden');
	$('html').css('overflow','hidden');
	setTimeout(function(){ $("#mobile_sidebar").toggleClass("shown"); }, 200);
	
	return false;
}

function hideSidebar()
{
	if(flag) return false;
	if(!sidebarShown) return true;
	sidebarShown = false;
	flag = true;
	setTimeout(function(){ flag = false; }, 500);

	$("#body").removeClass("shown");
	$('body').css('overflow','visible');
	$('html').css('overflow','visible');
	$("#mobile_sidebar").toggleClass("shown");
	
	return false;
}

$(function() {

	$('#mobile_openHeader').bind('touchstart mousedown click', showSidebar);
	$('#body').bind('touchstart mousedown click', hideSidebar);
});
