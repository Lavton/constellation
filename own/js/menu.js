if (typeof String.prototype.startsWith != 'function') {
  String.prototype.startsWith = function (str){
    return this.indexOf(str) == 0;
  };
}

(function() {

	if (history.pushState) { // если поддерживает HTML5 History API
	    $('body').on('click', 'a.ajax-nav', function(event) // вешаем обработчик на все ссылки, даже созданные после загрузки страницы
	    {
	    	if ($(this).attr('target') != "_blank") {
		    	var url = $(this).attr('href');
	      		setPage(url);
	      		return false;
	    	}
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
        			/*дабы не качать внешние (скорее всего большие) скрипты каждый раз - занесём их в шаблон*/
        			if ($("#footer-js script[src='"+atrib+"']")[0] == undefined) {
		        		document.getElementById("footer-js").appendChild(scrpt);  
		        	}
        		} else {
    	    		document.getElementById("after-js-container").appendChild(scrpt);  
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
		
		if ((locat.startsWith("/about")) || (locat.startsWith("/events")) || (locat.startsWith("/method"))) {
			/*показываем подменю*/
			add_submenu(locat);
			$(".header-lvl2-container").show('slow');
		} else {
			$("nav li.current").removeClass("current");
			$("nav .header.lvl2").removeClass("current");
			$(".header-lvl2-container").hide('slow');
		}
		if (locat == "/events/shifts") {
			var evID = setInterval(function(){
 				if (typeof(angular) !== "undefined") {
					if ((window.shifts) && (window.shifts.was_init) && (! $(".shifts-container").is(":visible"))) {
						angular.bootstrap(document, ['common_sc_app']);
				    }
				    clearInterval(evID);
  				}
  			}, 50);
		}
		
		if (locat == "/events/") {
			var eveID = setInterval(function(){
 				if (typeof(angular) !== "undefined") {
					if ((window.events) && (window.events.was_init) && (! $(".events-container").is(":visible"))) {
						angular.bootstrap(document, ['common_ec_app']);
				    }
				    clearInterval(eveID);
  				}
  			}, 50);
		}

		if (locat == "/about/users") {
			var usersID = setInterval(function(){
 				if (typeof(angular) !== "undefined") {
					if ((window.fighters) && (window.fighters.was_init) && (! $(".table-container").is(":visible"))) {
						angular.bootstrap(document, ['common_fc_app']);
				    }
				    clearInterval(usersID);
  				}
  			}, 50);
		}

		if (locat == "/about/candidats") {
			var usersID = setInterval(function(){
 				if (typeof(angular) !== "undefined") {
					if ((window.candidats) && (window.candidats.was_init) && (! $(".table-container").is(":visible"))) {
						angular.bootstrap(document, ['common_candc_app']);
				    }
				    clearInterval(usersID);
  				}
  			}, 50);
		}


		if (locat == "/method/games") {
			var gmID = setInterval(function(){
 				if (typeof(angular) !== "undefined") {
					if ((window.games) && (window.games.was_init) && (! $(".games-container").is(":visible"))) {
						angular.bootstrap(document, ['game_app']);
				    }
				    clearInterval(gmID);
  				}
  			}, 50);
		}


		function add_submenu (locat) {
			$("nav li.current").removeClass("current");
			$("nav .header.lvl2").removeClass("current");
			if (locat.startsWith("/about/users")) {
				locat = "/about/users";
			}
			if (locat.startsWith("/about/candidats")) {
				locat = "/about/candidats";
			}			
			if (locat.startsWith("/events/shifts")) {
				locat = "/events/shifts";
			} else if (locat.startsWith("/events/") ) {
				locat = "/events/";
			}

			$("nav a[href='"+locat+"'] li").addClass("current");
			if (locat.startsWith("/about")) {
				$("nav a.about.index li").addClass("current");
				$("nav .header.lvl2.about").addClass("current");
			} else if (locat.startsWith("/events")) {
				$("nav a.events.index li").addClass("current");
				$("nav .header.lvl2.events").addClass("current");
			} else if (locat.startsWith("/method")) {
				$("nav a.method.index li").addClass("current");
				$("nav .header.lvl2.method").addClass("current");
			}
		}
	}

	on_change(); // при загрузки скрипта так же приведём вид в нормальную форму.

})();