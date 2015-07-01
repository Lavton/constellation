if (typeof String.prototype.startsWith != 'function') {
  String.prototype.startsWith = function(str) {
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
    if (typeof(if_history) === 'undefined') if_history = false;
    if (!if_history) {
      // добавляем в историю
      window.history.pushState({
        "page": page,
        "type": "page",
        "title": document.title
      }, document.title, page);
    }
    $.get(page, function(data) {
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
          if ($("#footer-js script[src='" + atrib + "']")[0] == undefined) {
            document.getElementById("footer-js").appendChild(scrpt);
          }
        } else {
          document.getElementById("after-js-container").appendChild(scrpt);
        }
      })

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

    /*смотрим, какие библиотеки надо грузить по этому пути*/
    var script_date = _.find(window.locs, function(loc) {
      return loc[0].test(locat);
    })
    if (script_date) {
      document.title = script_date[1].title;
      var lab = $LAB;
      lab.setGlobalDefaults({
        AlwaysPreserveOrder: true
      });
      _.reduce(script_date[1].js, function(memo, js) {
        if (window.jsFiles[js] == false) {
          window.jsFiles[js] = true;
          return memo.script(js);
        } else {
          return memo;
        }
      }, lab);
      // все элементы 2+ - нужно запускать каждый раз
      for (var i = 2; i < script_date.length; i++) {
        script_date[i]()
      };
      window.last = script_date
    }
    if ((locat.startsWith("/about")) || (locat.startsWith("/events")) || (locat.startsWith("/method")) || (locat.startsWith("/cs"))) {
      /*показываем подменю*/
      add_submenu(locat);
      $(".header-lvl2-container").show('slow');
    } else {
      $("nav li.current").removeClass("current");
      $("nav .header.lvl2").removeClass("current");
      $(".header-lvl2-container").hide('slow');
    }


    function add_submenu(locat) {
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
      } else if (locat.startsWith("/events/")) {
        locat = "/events/";
      }

      $("nav a[href='" + locat + "'] li").addClass("current");
      if (locat.startsWith("/about")) {
        $("nav a.about.index li").addClass("current");
        $("nav .header.lvl2.about").addClass("current");
      } else if (locat.startsWith("/events")) {
        $("nav a.events.index li").addClass("current");
        $("nav .header.lvl2.events").addClass("current");
      } else if (locat.startsWith("/method")) {
        $("nav a.method.index li").addClass("current");
        $("nav .header.lvl2.method").addClass("current");
      } else if (locat.startsWith("/cs")) {
        $("nav a.cs.index li").addClass("current");
        $("nav .header.lvl2.cs").addClass("current");
      }
    }

  }

  on_change(); // при загрузки скрипта так же приведём вид в нормальную форму.
})();