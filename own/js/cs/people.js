(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.Object = Object;
    var today = new Date();
    $scope.today = dayFromYear(today);

    // возращает дату в формате "6 мая 2015"
    $scope.formatDate = window.formatDate;
    window.initDatePicker($scope)

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
        $scope.people = json.people
        _.each($scope.people, function(person, id, list) {
          window.getPerson(person.uid * 1, function(pers, flag) {
            _.extend(person, pers)
            if (flag) {
              $scope.$apply();
            }
          });
        });
        // отображаем, сколько дней осталось + сортируем по этому параметру
        _.each($scope.people, function(person) {
          person.bd = new Date(person.birthdate)
          person.dayFromYear = dayFromYear(person.bd)
          person.dbThisYear = (new Date()).getFullYear() + "-" + ((person.bd).getMonth() + 1) + "-" + ((person.bd).getDate())
        })
        $scope.people = _.sortBy($scope.people, function(person) {
          return (person.dayFromYear - dayFromYear(today) + 365) % 365;
        })
        var happyPeople = [];
        // смотрим, какие смены совпадают с ДР этого человека.
        _.each(_.groupBy(json.shifts, function(shift) {
          return shift.shift
        }), function(shift) {
          console.log(shift[0].start_date)
          shift[0].dStart = dayFromYear(new Date(shift[0].start_date))
          shift[0].dEnd = dayFromYear(new Date(shift[0].finish_date))
          shift[0].fn_date = new Date(shift[0].finish_date);
          _.each(_.filter($scope.people, function(person) {
            return (shift[0].dStart <= person.dayFromYear) && (shift[0].dEnd >= person.dayFromYear) && (shift[0].user * 1 == person.id * 1)
          }), function(person) {
            person.shift = shift[0];
            happyPeople.push(person)
          })
        })
        $scope.$apply();

        // хотим привязку, но в этот момент элементы ещё не созданы
        setTimeout(function() {
          _.each($("span.date"), function(self) {
            $(self).pickmeup({
              format: 'Y-m-d',
              hide_on_select: true,
              date: new Date($(self).attr("class").split(" ")[1])
            });
          })
        }, 500);

      })

    }
  }


  function init() {
    window.init_ang("CSpeopleApp", init_angular_s_c, "cs-people");
  }
  init();
  window.registerInit(init)

})();
