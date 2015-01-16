if (typeof String.prototype.startsWith != 'function') {
  String.prototype.startsWith = function (str){
    return this.indexOf(str) == 0;
  };
}

(function() {
	if (history.pushState) {
	    $('body').on('click', 'nav a', function(event) // вешаем обработчик на все ссылки, даже созданные после загрузки страницы
	    {
	    	var url = $(this).attr('href');
      		setPage(url);
      		return false;
  		});
	}
    
	function setPage(page, if_history) {
		if(typeof(if_history)==='undefined') if_history = false;
	    $.post(page, { ajaxLoad: true }, function(data)
        {
        	var link = document.createElement('div');
        	$(link).html(data);
        	$("#page-container").html($(link).find("#page-container").html());
        	$("#after-js-container").html($(link).find("#after-js-container").html());
        	if (!if_history) {
    	    	window.history.pushState({"page": page, "type": "page", "title": document.title}, document.title, page); 
	        }
	        $("html").trigger("change_url");
	        eval($(link).find("#after-js-container script").html());
	        on_change();
        });
	} 
	window.addEventListener("popstate", function(e) {
	  	if (e.state.type.length > 0) {
	  		setPage(e.state.page, true);
	  		document.title = e.state.title;
	  	}
	}, false)

	function on_change() {
	    /*смотрим путь, на котором мы сейчас*/
		var locat = window.location.pathname;
		
		if ((locat.startsWith("/about")) || (locat.startsWith("/events"))) {
			/*показываем подменю*/
			add_submenu(locat);
			$(".header-lvl2-container").show('slow');
		} else {
			$("nav li.current").removeClass("current");
			$("nav .header.lvl2").removeClass("current");
			$(".header-lvl2-container").hide('slow');
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
	}
	on_change();

})();