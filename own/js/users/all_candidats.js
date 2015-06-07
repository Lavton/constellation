(function() {
  /*логика ангулара*/
  function init_angular_cand_c($scope, $http) {
    $scope.window = window;
    /*инициализация*/
    $scope.candidats = [];
    window.setPeople(function(flag) {
      $scope.candidats = _.chain(window.people)
        .filter(function(person) {
          return person.isFighter == false
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
      allPeople.moreFromServer("all_candidats", $scope, $scope.candidats);
      /*после - данные с ВК*/
      allPeople.moreFromVK($scope.candidats, $scope)
    }

    /*просто изменение формата вывода телефона*/
    $scope.goodView = window.goodTelephoneView

  }

  /*добавить нового кандидата*/
  allPeople.addNewPerson("get_all_candidats_ids", "add_new_candidate", ".add-new-candidate", "/about/candidats/")

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });

    window.init_ang("candidatsApp", init_angular_cand_c, "all-cand");
  }
  init();
  window.registerInit(init)
})();