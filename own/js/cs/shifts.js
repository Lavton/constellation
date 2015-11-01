(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.Object = Object;
    $scope.shifts = {}
    var today = new Date();
    /*инициализация*/
    var data = {
      action: "shifts",
    };
    $http.post('/handlers/cs.php', data).success(function(response) {
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
      console.log(ids)
      var data = {
        action: "get_people",
        ids: ids
      };
      $.ajax({
        type: "POST",
        url: "/handlers/cs.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
        window.setPeople(function() {
          _.each(json.people, function(person) {
            _.extend(person, _.findWhere(window.people, {
              "id": person.user * 1
            }));
            person.fn_date = new Date(person.finish_date);
          })
        });

        // группируем по людям
        $scope.people = _.groupBy(json.people, function(person) {
          return person.user;
        });
        console.log("people")
        $scope.fighters = _.filter($scope.people, function(person) {
          return person[0].isFighter;
        })
        $scope.candidats = _.filter($scope.people, function(person) {
          return !person[0].isFighter;
        })
        $scope.$apply();
      })
    }
  }


  function init() {
    window.init_ang("CSshiftsApp", init_angular_s_c, "cs-shifts");
  }
  init();
  window.registerInit(init)

})();
