(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.groups = window.groups;
    $scope.Object = Object;
    var today = new Date();

    // инициализация: получаем список базовых мероприятий
    var data = {
      action: "get_base_events",
    };
    $.ajax({
      type: "POST",
      url: "/handlers/cs.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(json) {
      $scope.events = json.events;
      $scope.$apply();
    })

    // показывает форму создания нового базового мероприятия
    $scope.addNewEvent = function() {
      $scope.adding_new = !$scope.adding_new;
      $('html, body').animate({ scrollTop: $("footer").offset().top }, 500); // анимируем скроолинг к элементу
    }

    // создаёт новое базовое мероприятие
    $scope.addNewEventSubmit = function() {
      var data = angular.copy($scope.newevent);
      data.action = "add_base_event";

      $.ajax({
        type: "POST",
        url: "/handlers/cs.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")

      })

    }

  }


  function init() {
    window.init_ang("CSeventsApp", init_angular_s_c, "cs-events");
  }
  init();
  window.registerInit(init)

})();
