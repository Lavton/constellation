if (typeof String.prototype.startsWith != 'function') {
  String.prototype.startsWith = function (str){
    return this.indexOf(str) == 0;
  };
}

(function() {
	// $("nav a").click(function(event) {
 //        console.log($(this).attr("href"));
 //        event.preventDefault();
 //        Backbone.history.navigate($(this).attr("href"), {
 //          trigger: true
 //    	});
	// });
    
    /*смотрим путь, на котором мы сейчас*/
	var locat = window.location.pathname;
	
	if ((locat.startsWith("/about")) || (locat.startsWith("/events"))) {
		/*показываем подменю*/
		$(".header-lvl2-container").show();
		add_submenu(locat);
	} else {
		$(".header-lvl2-container").hide();
	}

	function add_submenu (locat) {
		$("nav li.current").removeClass("current");
		$("nav .header.lvl2").removeClass("current");
		$("nav a[href='"+locat+"'] li").addClass("current");
		if (locat.startsWith("/about")) {
			$("nav a.about.index li").addClass("current");
			$("nav .header.lvl2.about").addClass("current");
		} else if (locat.startsWith("/events")) {
			$("nav .header.lvl2.events").addClass("current");
		}
	}
})();