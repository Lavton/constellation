// кусок кода про сводки про людей
(function() {
  /*логика ангулара*/
  function init_angular($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.shifts = {}
    var today = new Date();
    $scope.shifts.max_arhive = ((new Date()).getFullYear()) * 1;
    $scope.shifts.arhive_year = $scope.shifts.max_arhive;
    window.initDatePicker($scope, function() {
      $scope.onSetDate()
    })

    $scope.get_arhive = function(year) {
      var data = {
        action: "arhive",
        year: year
      };
      $http.post('/handlers/shift.php', data).success(function(response) {
        var today = new Date();
        $scope.shifts.prev = response.shifts;
        _.each($scope.shifts.prev, function(element, index, list) {
          element.st_date = new Date(element.start_date);
          element.fn_date = new Date(element.finish_date);
        });
      });
    }


    // показывает форму создания нового мероприятия
    $scope.addNewEvent = function() {
      $scope.adding_new = true;
      $scope.edit_ev = false;
      $scope.newevent = {
        "visibility": 2,
      }
      setTimeout(function() {
        $('html, body').animate({
          scrollTop: $(".scrl").offset().top
        }, 500); // анимируем скроолинг к элементу
      }, 100);
    }

    // создаёт новую смену
    $scope.addNewEventSubmit = function() {
      var data = angular.copy($scope.newevent);
      data.action = "add_new_shift";
      $.ajax({
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
        $('html, body').animate({
          scrollTop: $("nav").offset().top
        }, 500); // анимируем скроолинг к элементу
        var lnk = document.createElement("a");
        lnk.setAttribute("class", "ajax-nav")
        $(lnk).attr("href", "/events/shifts/" + json.id);
        $("#page-container").append(lnk);
        $(lnk).trigger("click")
      })
    }

    $scope.onSetDate = function() {
      if (!$scope.newevent.finish_date) {
        var st = new Date($scope.newevent.start_date)
        st.setDate(st.getDate() + 7 * 3)
        st = st.toJSON().split("T")[0];
        $scope.newevent.finish_date = st;
      }

      if (!$scope.newevent.name) {
        var start_date = $scope.newevent.start_date;
        var finish_date = $scope.newevent.finish_date;
        /*определим название заранее*/
        var st_date = new Date(start_date);
        var fn_date = new Date(finish_date);
        var name = "";
        var st_month = st_date.getMonth() * 1 + 1; //нумерация с нуля была
        var fn_month = fn_date.getMonth() * 1 + 1;
        if ((st_month == 10) || (st_month == 11)) {
          //октябрь или ноябрь -> осень
          name = "Осень";
        } else if ((st_month == 12) || (st_month == 1)) {
          //декабрь или январь -> зима
          name = "Зима";
        } else if ((st_month == 3) || (st_month == 4)) {
          //март или апрель -> весна
          name = "Весна";
        } else {
          name = "Лето ";
          if (fn_month == 6) { //в июне кончается первая смена
            name += "1";
          } else if (st_month == 6) { //в июне начинается вторая смена (или первая, но её уже обработали)
            name += "2";
          } else if (st_month == 7) { //в июле начинается третья смена
            name += "3";
          } else { //осталась четвёртая
            name += "4";
          }
        }
        $scope.newevent.name = name;
      }
    }

  }

  function init() {
    window.init_ang("shiftsAppPeople", init_angular, "all-shifts-arhive");
  }
  init();
  window.registerInit(init)

})();
