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
  }

  $('#page-container').on('click', ".add-new-shift .pre-add-new", function() {
    var auto_date = null;
    if (!$(".pre-add-new").hasClass("clicked")) {
      $(".add-new").removeClass("hidden")
      $(".pre-add-new").addClass("clicked")
      $(".pre-add-new").text("Добавить")
      auto_date = setInterval(function() {
        var start_date = $(".add-new-start-date").val();
        var finish_date = $(".add-new-end-date").val();
        if (finish_date == "") {
          if (start_date != "") {
            var st = new Date(start_date)
            st.setDate(st.getDate() + 7 * 3)
            $(".add-new-end-date").val(st.toJSON().split("T")[0]);
          }
        } else {
          clearInterval(auto_date);
        }
      }, 750);

    } else {
      var start_date = $(".add-new-start-date").val();
      var finish_date = $(".add-new-end-date").val();
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

      var send_data = {
        action: "add_new_shift",
        start_date: start_date,
        finish_date: finish_date,
        time_name: name
      }
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(send_data)
      }).done(function(response) {
        if (response.result == "Success") {
          window.location = "/events/shifts/" + response.id;
        }
      });
    }


  });

  function init() {
    window.init_ang("shiftsAppPeople", init_angular, "all-shifts-arhive");
  }
  init();
  window.registerInit(init)

})();
