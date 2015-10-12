(function() {
  /*логика ангулара*/
  function init_angular_f_c($scope, $http) {
    $scope.newperson = {}
    $scope.newperson.pos_status = [{"id":0, "title": "--нет--"}, {"id":1, "title": "кандидат"}, , {"id":2, "title": "боец"}]
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
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

    // получаем информацию по-подробнее
    $scope.getMoreInfo = function() {
      /*сначала - данные с сервера*/
      allPeople.moreFromServer("all", $scope, $scope.fighters);
      /*после - данные с ВК*/
      allPeople.moreFromVK($scope.fighters, $scope)
    }

    // просто изменение формата вывода телефона
    $scope.goodView = window.goodTelephoneView;

    // показывает форму добавления человека
    $scope.addNewPerson = function() {
      $scope.adding_new = true;
      $('html, body').animate({
        scrollTop: $(".scrl").offset().top
      }, 500); // анимируем скроолинг к элементу
    }

    $scope.setVK = function() {
      console.log($scope.newperson.uid)
    }

    // синхронизируемся, где надо
    $("#page-container").on("_final_select", "input", function(e) {
      /*в ng-model лежит путь. Но не прямое значение(( Пройдём по нему до почти конца и впишем*/
      var path = this.getAttribute("ng-model").split(".")
      var self = $scope;
      for (var i = 0; i < path.length - 1; i++) {
        self = self[path[i]]
      };
      self[path[path.length - 1]] = this.value;
      $scope.$apply();

      $scope.after_sinh();
    })

    // после нахождения человека - вставляем данные о нём
    $scope.after_sinh = function() {
      console.log($scope.newperson.uid)
      if (!$scope.newperson.first_name) {
        getVkData($scope.newperson.uid, ["bdate"], function(response) {
          response = response[$scope.newperson.uid];
          _.extend($scope.newperson, response)
          var bd = [];
          if (response.bdate) {
            bd = response.bdate.split(".");
          }
          if (!bd[0]) {
            bd[0] = "01"
          }
          if (!bd[1]) {
            bd[1] = "01"
          }
          if (!bd[2]) {
            bd[2] = "0001"
          }
          bd = bd[2] + "-" + bd[1] + "-" + bd[0];
          $scope.newperson.birthdate = bd;
          $scope.$apply();
        })
      }
    }

    // чтобы дату вводить
    $('input.date').pickmeup({
      format: 'Y-m-d',
      hide_on_select: true,
      position: "top",
      change: function() {
        var path = this.getAttribute("ng-model").split(".")
        var self = $scope;
        for (var i = 0; i < path.length - 1; i++) {
          self = self[path[i]]
        };
        self[path[path.length - 1]] = $(this).val();

        $scope.$apply();
        return true;
      }
    });
    $(document).keyup(function(e) {
      if (e.keyCode == 27) {
        $('.date').pickmeup('hide');
      }
    });

  }


  /*добавить нового бойца*/
  allPeople.addNewPerson("get_all_ids", "add_new_fighter", ".add-new-fighter", "/about/users/")

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("fightersApp", init_angular_f_c, "all-figh");
  }
  init();
  window.registerInit(init)
})();
