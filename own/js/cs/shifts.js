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
        $scope.people = {};
        var detach_shifts = {};
        for (var i = 0; i < json.detachments.length; i++) {
          _.each(json.detachments[i].people.split("$"), function(person) {
            if (!$scope.people[person]) {
              $scope.people[person] = {
                "shifts": []
              };
            }
            $scope.people[person].shifts.push({
              "id": json.detachments[i].shift_id
            })
          })
          detach_shifts[json.detachments[i].shift_id] = true;
        };
        _.each(_.reject(json.guesses, function(guess) {
          return detach_shifts[guess.shift_id];
        }), function(rj_guess) {
          if (!$scope.people[rj_guess.vk_id]) {
            $scope.people[rj_guess.vk_id] = {
              "shifts": []
            };
          }
          $scope.people[rj_guess.vk_id].shifts.push({
            "id": rj_guess.shift_id,
            "probability": rj_guess.probability
          })
        })

        // получаем норм инфу про смены
        _.each($scope.people, function(person) {
          _.each(person.shifts, function(shift) {
            _.extend(shift, _.findWhere($scope.shifts, {
              id: shift.id
            }))
          })
        })

        // получаем норм инфу про людей
        window.setPeople(function(flag) {
          $scope.fighters = {}
          $scope.candidats = {}
          _.each($scope.people, function(person, uid, list) {
            window.getPerson(uid * 1, function(pers, flag) {
              _.extend(person, pers)
              if (pers.isFighter == true) {
                $scope.fighters[uid] = person
              } else if (pers.isFighter == false) {
                $scope.candidats[uid] = person
              }
              if (flag) {
                $scope.$apply();
              }
            })

          });

          if (flag) {
            $scope.$apply();
          }
        })
        $scope.$apply();

        console.log($scope.people)
      })

    }
  }


  function init() {
    window.init_ang("CSshiftsApp", init_angular_s_c, "cs-shifts");
  }
  init();
  window.registerInit(init)

})();
