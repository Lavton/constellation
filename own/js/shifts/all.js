(function() {
  $('#page-container').on('click', ".pre-add-s-new", function() {
    var today = new Date();
    var start_date = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate();
    var finish_date = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate();
    var data = {
      action: "add_new_shift",
      start_date: start_date,
      finish_date: finish_date
    }
    $.ajax({ //TODO: make with angular
      type: "POST",
      url: "/handlers/shift.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(response) {
      if (response.result == "Success") {
        window.location = "/events/shifts/" + response.id;
      }
    });
  });


  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.shifts = {}
    var today = new Date();

    /*инициализация*/
    var data = {
      action: "all",
    };
    $http.post('/handlers/shift.php', data).success(function(response) {
      $scope.shifts.all = response.shifts;
      $scope.shifts.people = response.people;

      $scope.shifts.actual = [];
      $scope.shifts.future = [];
      $scope.Object = Object;
      $scope.shifts.max_arhive = ((new Date()).getFullYear()) * 1;
      $scope.shifts.arhive_year = $scope.shifts.max_arhive;
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
        if (element.st_date > today) {
          $scope.shifts.future.push(element);
        } else {
          $scope.shifts.actual.push(element);
        }
      }); // end _.each

      window.setPeople(function(flag) {
        $scope.fighters = {}
        $scope.candidats = {}
        _.each($scope.shifts.people, function(person, uid, list) {
          _.each(person, function(sh_element, sh_index, list) {
            sh_element["shift_name"] = _.findWhere($scope.shifts.all, {
              id: sh_element.shift_id
            }).name;
          });

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
  var state = window.state.events.shifts.all;
  window.init_ang("shiftsApp", init_angular_s_c, "all-shifts");
  state.controller = "shiftsApp";
  state.init_f = init_angular_s_c;
  state.element = "all-shifts"
})();