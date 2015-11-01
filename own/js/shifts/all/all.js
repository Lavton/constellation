(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.shifts = {}
    var today = new Date();

    /*инициализация*/
    var data = {
      action: "all_shifts",
    };
    $http.post('/handlers/shift.php', data).success(function(response) {
      console.log(response)
      $scope.shifts.all = response.shifts;

      $scope.shifts.actual = [];
      $scope.shifts.future = [];
      _.each($scope.shifts.all, function(element, index, list) {
        element.st_date = new Date(element.start_date);
        element.fn_date = new Date(element.finish_date);
        var st_month = element.st_date.getMonth() * 1 + 1; //нумерация с нуля была
        var fn_month = element.fn_date.getMonth() * 1 + 1;
        if (element.st_date > today) {
          $scope.shifts.future.push(element);
        } else {
          $scope.shifts.actual.push(element);
        }
      }); // end _.each
    }); //end http

  }


  function init() {
    window.init_ang("shiftsApp", init_angular_s_c, "all-shifts");
  }
  init();
  window.registerInit(init)

})();
