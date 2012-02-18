page = 0;


function setStep(page) {
	$("#progress").html("Step&nbsp;"+page+"&nbsp;of&nbsp;"+numPages);
	$("#progress").animate({width: (((page - 1)/ numPages) * 100)+"%"}, 200);
	$(".page").slideUp(200);
	$("#page"+page).slideDown(200);
}

window.onload = function() {
	$pages = $("#installPager div.page");
	numPages = $pages.length;
	$('.page').hide();
//	$('.page').css("position", "absolute");
	$('#installUI').fadeIn(100);
	$('#progress').css("width", "0%");
	page++;
	setStep(1);
	$("#prevPageButton").click(function() {
		if (page > 1) {
			page--;
			setStep(page);
		} 
		if (page == 1) $("#nextPageButton").attr("disabled");
		$("#nextPageButton").removeAttr("disabled");

	});
	$("#nextPageButton").click(function() {
		if (page < numPages) {
			page++;
			setStep(page);
		} 
		if (page == numPages) $("#nextPageButton").attr("disabled");
		$("#prevPageButton").removeAttr("disabled");
	});
}

function validateSqlSettings() {
	$("#sqlStatus").html("All seems well for this test.");
	$("#sqlStatus").fadeIn(200);
}
