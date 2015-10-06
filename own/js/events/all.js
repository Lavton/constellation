'use strict';
(function() {
  /*логика ангулара*/
  function init_angular_e_c($scope, $http) {
    
    // возращает дату в формате "6 мая 2015"
    $scope.formatDate = function(date) {
      date = new Date(date);
      Number.prototype.toMonthName = function() {
        var month = ['января','февраля','марта','апреля','мая','июня',
        'июля','августа','сентября','октября','ноября','декабря'];
        return month[this];
      };
      return date.getDate()+" "+date.getMonth().toMonthName()+" "+date.getFullYear();
    }

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
    });

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

  }

  // работа с новым событием
  $('#page-container').on('click', ".pre-add-new-event", function() {
    var auto_date = null;
    if (!$(".pre-add-new-event").hasClass("clicked")) {
      $(".add-new-input-w").removeClass("hidden")
      $(".pre-add-new-event").addClass("clicked")
      $(".pre-add-new-event").text("Добавить")
      auto_date = setInterval(function() {
        var start_date = $(".add-new-event-start-date").val();
        var end_date = $(".add-new-event-end-date").val();
        if (end_date == "") {
          if (start_date != "") {
            $(".add-new-event-end-date").val(start_date);
          }
        } else {
          clearInterval(auto_date);
        }
      }, 750);

    } else {
      var name = $(".add-new-event-name").val();
      var start_date = $(".add-new-event-start-date").val();
      var start_time = $(".add-new-event-start-time").val();
      var end_date = $(".add-new-event-end-date").val();
      var end_time = $(".add-new-event-end-time").val();
      var send_data = {
        action: "add_new_event",
        name: name,
        start_time: start_date + " " + start_time + ":00",
        end_time: end_date + " " + end_time + ":00"
      }
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(send_data)
      }).done(function(response) {
        if (response.result == "Success") {
          window.location = "/events/" + response.id;
        }
      });
    }
  });

  function init() {
    window.init_ang("eventsApp", init_angular_e_c, "events-all");
  }
  init();
  window.registerInit(init)

})();
