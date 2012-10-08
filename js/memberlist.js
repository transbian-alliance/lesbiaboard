//Memberlist JavaScript

function hookUpMemberlistControls() {
	$("#orderBy,#order,#sex,#power").change(function(e) {
		refreshMemberlist();
	});

	$("#submitQuery").click(function() {
		refreshMemberlist();
	});

	refreshMemberlist();
}

function refreshMemberlist(page) {
	var orderBy = document.getElementById("orderBy").value;
	var order   = document.getElementById(  "order").value;
	var sex     = document.getElementById(    "sex").value;
	var power   = document.getElementById(  "power").value;
	var query   = document.getElementById(  "query").value;
	if (typeof page == "undefined")
		page = 0;

	$.get("./?page=memberlist", {
		listing: true,
		dir: order,
		sort: orderBy,
		sex: sex,
		pow: power,
		from: page,
		query: query}, function(data) {
			$("#memberlist").html(data);
		});
}


$(document).ready(function() {
	hookUpMemberlistControls();
});
