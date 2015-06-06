(function() {
  /*логика ангулара*/
  function init_angular_f_c($scope, $http) {
    window.setPeople(window.init_vk_search);
    $scope.window = window;
    /*инициализация*/
    $scope.fighters = [];
    window.setPeople(function(flag) {
      $scope.fighters = _.chain(window.people)
        .filter(function(person) {
          return person.isFighter
        })
        .sortBy(function(person) {
          return person.id
        })
        .map(function(person) {
          return _.clone(person)
        })
        .value();
      if (flag) {
        $scope.$apply();
      }
    });
    /*конец инициализации*/

    /*получаем информацию по-подробнее*/
    $scope.getMoreInfo = function() {
      /*сначала - данные с сервера*/
      allPeople.moreFromServer("all", $scope, $scope.fighters);
      /*после - данные с ВК*/
      allPeople.moreFromVK($scope.fighters, $scope)
    }

    /*просто изменение формата вывода телефона*/
    $scope.goodView = window.goodTelephoneView;
  }


  /*добавить нового бойца*/
  allPeople.addNewPerson("get_all_ids", "add_new_fighter", ".add-new-fighter", "/about/users/")

  var state = window.state.about.users.fighters.all;
  window.init_ang("fightersApp", init_angular_f_c, "all-figh");
  state.controller = "fightersApp";
  state.init_f = init_angular_f_c;
  state.element = "all-figh";
})();