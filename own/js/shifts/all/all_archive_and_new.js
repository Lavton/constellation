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
        $scope.shifts.all = response.shifts;
        $scope.shifts.prev = [];
        _.each($scope.shifts.all, function(element, index, list) {
          element.st_date = new Date(element.start_date);
          element.fn_date = new Date(element.finish_date);
          var name = "";
          var st_month = element.st_date.getMonth() * 1 + 1; //нумерация с нуля была
          var fn_month = element.fn_date.getMonth() * 1 + 1;
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
          name += ", " + element.fn_date.getFullYear()
          if (element.place) {
            name += " (" + element.place + ")";
          }
          element.name = name;
          if (element.fn_date < today) {
            $scope.shifts.prev.push(element);
          }
        });
      });
    }
  }


  function init() {
    window.init_ang("shiftsAppPeople", init_angular, "all-shifts-arhive");
  }
  init();
  window.registerInit(init)

})();
