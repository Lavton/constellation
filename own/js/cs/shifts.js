(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.shifts = {}
    var today = new Date();
    /*инициализация*/
    var data = {
      action: "shifts",
    };
    $http.post('/handlers/cs.php', data).success(function(response) {
    console.log("init3")
      $scope.shifts = response.shifts;
      _.each($scope.shifts, function(shift) {
        shift.fn_date = new Date(shift.finish_date);
        shift.state = false;
      })
      console.log(response.shifts)
    }); //end http

    // кликнули на чекбокс - меняем его состояние
    $scope.checkClicked = function(shift) {
      shift.state = !shift.state;
    }

    // достаём инфу про людей с выбранных смен
    $scope.selectShifts = function() {
      var ids = _.chain($scope.shifts).filter(function(shift) {
        return shift.state;
      }).map(function(shift) {
        return shift.id
      }).value();
      console.log("yeah")
      console.log(ids)
    }
  }


  function init() {
    window.init_ang("CSshiftsApp", init_angular_s_c, "cs-shifts");
  }
  init();
  window.registerInit(init)

})();
