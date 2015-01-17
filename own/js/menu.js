if (typeof String.prototype.startsWith != 'function') {
  String.prototype.startsWith = function (str){
    return this.indexOf(str) == 0;
  };
}

(function() {

	if (history.pushState) { // если поддерживает HTML5 History API
	    $('body').on('click', 'nav a', function(event) // вешаем обработчик на все ссылки, даже созданные после загрузки страницы
	    {
	    	var url = $(this).attr('href');
      		setPage(url);
      		return false;
  		});
	}

	/*Берём не всю страницу, а часть
	if_history==true, когда мы  вызываем функцию, двигаясь по истории браузера*/    
	function setPage(page, if_history) {
		if(typeof(if_history)==='undefined') if_history = false;
	    $.post(page, { ajaxLoad: true }, function(data)
        {
        	/*нужно загрузить и контекст, и js. Выполняем через одно место*/
        	var link = document.createElement('div');
        	$(link).html(data);
        	$("#page-container").html($(link).find("#page-container").html());
        	$("#after-js-container").html("");
        	/*последовательно добавляем все скрипты*/
        	_.each($(link).find("#after-js-container script"), function(element, index, list) {
        		var scrpt = document.createElement('script');
        		$(scrpt).html($(element).html());
        		var atrib = $(element).attr('src');
        		if (atrib != undefined) {
        			scrpt.src = atrib;
        		}
        		document.getElementById("after-js-container").appendChild(scrpt);  
        		scrpt.onLoad= function() {
				console.log("hello")
				}
        	})

        	if (!if_history) {
        		// добавляем в историю
    	    	window.history.pushState({"page": page, "type": "page", "title": document.title}, document.title, page); 
	        }
	        // меняем вид меню
	        on_change();
        });
	} 

	/*при проходе по истории браузера, опять вызываем ajax*/
	window.addEventListener("popstate", function(e) {
	  	if (e.state.type.length > 0) {
	  		setPage(e.state.page, true);
	  		document.title = e.state.title;
	  	}
	}, false)

	/*все изменения во внешнем виде меню*/
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
			if (locat.startsWith("/about/users")) {
				locat = "/about/users";
			}
			$("nav a[href='"+locat+"'] li").addClass("current");
			if (locat.startsWith("/about")) {
				$("nav a.about.index li").addClass("current");
				$("nav .header.lvl2.about").addClass("current");
			} else if (locat.startsWith("/events")) {
				$("nav .header.lvl2.events").addClass("current");
			}
		}
	}

	on_change(); // при загрузки скрипта так же приведём вид в нормальную форму.

})();