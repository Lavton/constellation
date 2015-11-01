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
      console.log(response)

      // добавляем фото
      window.setPeople(function() {
        _.each(response.people, function(person) {
          _.extend(person, _.findWhere(window.people, {
            "id": person.user * 1
          }));
        })
      });

      // группируем по людям
      $scope.shifts.people = _.groupBy(response.people, function(person) {
        return person.user;
      });
      console.log($scope.shifts.people)
      $scope.fighters = _.filter($scope.shifts.people, function(person) {
        return person[0].fighter_id;
      })
      $scope.candidats = _.filter($scope.shifts.people, function(person) {
        return !person[0].fighter_id;
      })
    }); //end http

  }


  function init() {
    window.init_ang("shiftsAppPeople", init_angular, "all-shifts-people");
  }
  init();
  window.registerInit(init)

})();
