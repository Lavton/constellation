(function() {
	// $("nav a").click(function(event) {
 //        console.log($(this).attr("href"));
 //        event.preventDefault();
 //        Backbone.history.navigate($(this).attr("href"), {
 //          trigger: true
 //    	});
	// });
	$("nav li.current").removeClass("current");
	$("nav a[href='"+window.location.pathname+"'] li").addClass("current");
})();