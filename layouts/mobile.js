var headerShown = false;
var flag = false;

$(function() {

	$('#mobile_openHeader').bind('touchstart mousedown click', function(){
		if (!flag) {
			flag = true;
			setTimeout(function(){ flag = false; }, 500);

			$("#body").toggleClass("shown");
			if(!headerShown)
			{
				$('body').css('overflow','hidden');
				$('html').css('overflow','hidden');
				setTimeout(function(){ $("#mobile_sidebar").toggleClass("shown"); }, 200);
			}
			else
			{
				$('body').css('overflow','visible');
				$('html').css('overflow','visible');
				$("#mobile_sidebar").toggleClass("shown");
			}
			headerShown = !headerShown;
		}
		return false;

	});
});
