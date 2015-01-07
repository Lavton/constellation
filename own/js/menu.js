(function() {
	// $("nav a").click(function(event) {
 //        console.log($(this).attr("href"));
 //        event.preventDefault();
 //        Backbone.history.navigate($(this).attr("href"), {
 //          trigger: true
 //    	});
	// });
	var locat = window.location.pathname
	$("nav li.current").removeClass("current");
	$("nav a[href='"+locat+"'] li").addClass("current");
	if ((locat=="/about") || (locat=="/events")) {
		$(".header-lvl2-container").show();
		add_submenu(locat);
	} else {
		$(".header-lvl2-container").hide();
	}

	function add_submenu (locat) {
		$("nav .header.lvl2").removeClass("current");
		switch (locat) {
			case "/about":
			console.log("Beee")
				$("nav .header.lvl2.about").addClass("current");
				break;
			case "/events":
			console.log("AAA")
				$("nav .header.lvl2.events").addClass("current");
				break;

		}


	}
})();