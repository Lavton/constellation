(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.Object = Object;
    var today = new Date();
    $scope.today = dayFromYear(today);

    function dayFromYear(date) {
      var start = new Date(date.getFullYear(), 0, 0);
      var diff = date - start;
      var oneDay = 1000 * 60 * 60 * 24;
      var day = Math.floor(diff / oneDay);
      return day;
    }

    // достаём инфу про людей с выбранных смен
    window.setPeople();

    $scope.getBirthdays = function() {
      var data = {
        action: "get_birthdays",
      };
      $.ajax({
        type: "POST",
        url: "/handlers/cs.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {

        // получаем норм отображение.
        $scope.people = [];
        _.each(json.fighters, function(person, id, list) {
          $scope.people.push(person)
          window.getPerson(person.vk_id * 1, function(pers, flag) {
            _.extend(person, pers)
            person.url_s = "users"
            if (flag) {
              $scope.$apply();
            }
          });
        });
        _.each(json.candidats, function(person, id, list) {
          $scope.people.push(person)
          window.getPerson(person.vk_id * 1, function(pers, flag) {
            _.extend(person, pers)
            person.url_s = "candidats"
            if (flag) {
              $scope.$apply();
            }
          });
        });

        // отображаем, сколько дней осталось + сортируем по этому параметру
        _.each($scope.people, function(person) {
          person.bd = new Date(person.birthdate)
          person.dayFromYear = dayFromYear(person.bd)
        })
        $scope.people = _.sortBy($scope.people, function(person) {
          return (person.dayFromYear - dayFromYear(today) + 365) % 365;
        })
        var happyPeople = [];
        // смотрим, какие смены совпадают с ДР этого человека.
        _.each(json.shifts, function(shift) {
          shift.dStart = dayFromYear(new Date(shift.start_date))
          shift.dEnd = dayFromYear(new Date(shift.finish_date))
          shift.fn_date = new Date(shift.finish_date);
          _.each(_.filter($scope.people, function(person) {
            return (shift.dStart <= person.dayFromYear) && (shift.dEnd >= person.dayFromYear) && (shift.vk_id*1 == person.uid * 1)
          }), function(person) {
            person.shift = shift;
            happyPeople.push(person)
          })
        })
        $scope.$apply();
      })

    }
  }


  function init() {
    window.init_ang("CSpeopleApp", init_angular_s_c, "cs-people");
  }
  init();
  window.registerInit(init)

})();
