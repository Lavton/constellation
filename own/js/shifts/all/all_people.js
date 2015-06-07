// кусок кода про сводки про людей
(function() {
  /*логика ангулара*/
  function init_angular($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.shifts = {}
    var today = new Date();

    /*инициализация*/
    var data = {
      action: "all_people",
    };
    $http.post('/handlers/shift.php', data).success(function(response) {
      $scope.shifts.people = response.people;

      $scope.shifts.actual = [];
      $scope.shifts.future = [];
      $scope.Object = Object;
      
      window.setPeople(function(flag) {
        $scope.fighters = {}
        $scope.candidats = {}
        _.each($scope.shifts.people, function(person, uid, list) {
          var cached_person = _.find(window.people, function(p) {
            return p.uid * 1 == uid;
          })
          _.extend(person[0], cached_person)
          if (cached_person.isFighter == true) {
            $scope.fighters[uid] = person
          } else {
            $scope.candidats[uid] = person
          }
        });

        if (flag) {
          $scope.$apply();
        }
      })

    }); //end http

  }


  function init() {
    window.init_ang("shiftsAppPeople", init_angular, "all-shifts-people");
  }
  init();
  window.registerInit(init)

})();
