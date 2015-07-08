(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.Object = Object;
    var today = new Date();

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
        console.log(json)

        // получаем норм отображение.
        $scope.people = [];
        _.each(json.fighters, function(person, id, list) {
          window.getPerson(person.vk_id * 1, function(pers, flag) {
            _.extend(person, pers)
            person.url_s = "users"            
            $scope.people.push(person)
            if (flag) {
              $scope.$apply();
            }
          });
        });
        _.each(json.candidats, function(person, id, list) {
          window.getPerson(person.vk_id * 1, function(pers, flag) {
            _.extend(person, pers)
            person.url_s = "candidats"
            $scope.people.push(person)
            if (flag) {
              $scope.$apply();
            }
          });
        });

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
