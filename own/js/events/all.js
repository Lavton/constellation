'use strict';
(function() {
  /*логика ангулара*/
  function init_angular_e_c($scope, $http) {
    $scope.window = window;
    $scope.newevent = {}

    // инициируем для выбора даты
    $('input.date').pickmeup({
      format: 'Y-m-d',
      hide_on_select: true,
      change: function() {
        var path = this.getAttribute("ng-model").split(".")
        var self = $scope;
        for (var i = 0; i < path.length - 1; i++) {
          self = self[path[i]]
        };
        console.log($(this).val())
        console.log(path)
        console.log(self[path[path.length - 1]])
        self[path[path.length - 1]] = $(this).val();
        if (!$scope.newevent.finish_date) {
          $scope.newevent.finish_date = $scope.newevent.start_date;
        }
        $scope.$apply();
        return true;
      }
    });
    $(document).keyup(function(e) {
      if (e.keyCode == 27) {
        $('.date').pickmeup('hide');
      }
    });

    // возращает дату в формате "6 мая 2015"
    $scope.formatDate = window.formatDate;

    var data = {
      action: "all",
    };
    $http.post('/handlers/event.php', data).success(function(response) {
      $scope.events = {}
      var today = new Date();
      $scope.events.all = response.events;
      $scope.events.actual = [];
      $scope.events.future = [];
      _.each($scope.events.all, function(element, index, list) {
        element.st_date = new Date(element.start_date);
        element.fn_date = new Date(element.finish_date);
        if (element.st_date > today) {
          $scope.events.future.push(element);
        } else {
          $scope.events.actual.push(element);
        }
      });

      // хотим привязку, но в этот момент элементы ещё не созданы
      setTimeout(function() {
        _.each($("span.date"), function(self) {
          $(self).pickmeup({
            format: 'Y-m-d',
            hide_on_select: true,
            date: new Date($(self).attr("class").split(" ")[1])
          });
        })
      }, 500);
    });

    // получить список архивных мероприятий
    $scope.get_arhive = function(month) {
      var data = {
        action: "arhive",
        month: month
      };
      $http.post('/handlers/event.php', data).success(function(response) {
        var today = new Date();
        $scope.events.oall = response.events;
        $scope.events.prev = [];
        _.each($scope.events.oall, function(element, index, list) {
          element.st_date = new Date(element.start_date);
          element.fn_date = new Date(element.finish_date);
          $scope.events.prev.push(element);
        });
      });
    }

    // показывает форму создания нового мероприятия
    $scope.addNewEvent = function() {
      $scope.adding_new = true;
      $scope.edit_ev = false;
      $scope.newevent = {
        "visibility": 3,
        "start_time": "00:00",
        "finish_time": "23:59"
      }

      // если ещё не получали список
      if (!$scope.eventsBase) {
        var data = {
          "action": "get_base_and_par"
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/event.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          console.log(response);
          $scope.pos_parents = response.pos_parents;
          $scope.newevent.contact = response.me.first_name + " " + response.me.last_name + " +7" + response.me.phone;
          $scope.newevent.editor = response.me.id;
          $scope.pos_parents.push({
            id: null,
            name: "--нет--"
          })
          $scope.eventsBase = response.eventsBase;
          $scope.eventsBase.push({
            id: null,
            name: "--нет--"
          })
          $scope.$apply();
        });
      }
      $('html, body').animate({
        scrollTop: $("footer").offset().top
      }, 500); // анимируем скроолинг к элементу
    }

    // выполняется при выборе базового мероприятия. Меняет имя и видимость
    $scope.changeBase = function(base_id) {
      if (!$scope.newevent.name) {
        $scope.newevent.name = _.findWhere($scope.eventsBase, {
          id: base_id
        }).name;
        $scope.newevent.visibility = _.findWhere($scope.eventsBase, {
          id: base_id
        }).visibility * 1;
      }
    }


    // создаёт новое базовое мероприятие
    $scope.addNewEventSubmit = function() {
      var data = angular.copy($scope.newevent);
      data.action = "add_new_event";

      $.ajax({
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
        $('html, body').animate({
          scrollTop: $("nav").offset().top
        }, 500); // анимируем скроолинг к элементу
        var lnk = document.createElement("a");
        lnk.setAttribute("class", "ajax-nav")
        $(lnk).attr("href", "/events/" + json.id);
        $("#page-container").append(lnk);
        $(lnk).trigger("click")
      })
    }
  }

  function init() {
    window.init_ang("eventsApp", init_angular_e_c, "events-all");
  }
  init();
  window.registerInit(init)

})();
