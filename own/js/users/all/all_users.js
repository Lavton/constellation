(function() {
  /*логика ангулара*/
  function init_angular_f_c($scope, $http) {
    $scope.newperson = {}
    $scope.newperson.pos_status = [{
      "id": 1,
      "title": "--нет--"
    }, {
      "id": 2,
      "title": "кандидат"
    }, , {
      "id": 3,
      "title": "боец"
    }]
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    $scope.window = window;
    /*инициализация*/
    $scope.users = [];
    window.setPeople(function(flag) {
      $scope.users = _.chain(window.people)
        .filter(function(person) {
          return person.isFighter || person.isCandidate;
        })
        .sortBy(function(person) {
          return person.id
        })
        .sortBy(function(person) {
          return !person.isFighter;
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
      $scope.app2 = _.after(3, $scope.$apply);
      // сначала - данные с сервера
      var data = {
        "action": "get_all_more_info",
      };
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        _.each($scope.users, function(element, index, list) {
          element.photo_100 = "http://vk.com/images/camera_b.gif";
          _.extend(element,
            _.findWhere(response.users, {
              "id": element.id + ""
            }))
        })
        $scope.app2();

        // Если мы нашли несоответствие между закешированной версией и той, которую получили
        if (response.users.length != people.length) {
          window.clearPeople()
          window.setPeople(function() {
            $scope.app2()
          })
        } else {
          $scope.app2();
        }
      });
      getVkData(_.map($scope.users, function(user) {
          return user.uid;
        }), ["photo_100", "photo_200", "domain"],
        function(response) {
          _.each($scope.users, function(element, index, list) {
            element.photo = response[element.uid].photo_100;
          });
          $scope.app2();
        }
      );
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

    // отправляем форму добавления человека
    $scope.addNewPersonSubmit = function() {
      var data = angular.copy($scope.newperson);
      data.action = "add_new_person";
      data.year_of_entrance = (new Date()).getFullYear();
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
        window.clearPeople()
        var lnk = document.createElement("a");
        lnk.setAttribute("class", "ajax-nav")
        $(lnk).attr("href", "/users/" + json.id);
        $("#page-container").append(lnk);
        $(lnk).trigger("click")
      })
    }


    $scope.filterShow = function(what) {
      console.log(what)

      function cond(person) {
        switch (what) {
          case "candidats":
            return person.isCandidate;
            break;
          case "fighters":
            return person.isFighter;
            break;
          case "all":
            return true;
            break;
          case "fightersANDcandidats":
            return person.isFighter || person.isCandidate;
            break;
          case "last":
            return !(person.isFighter || person.isCandidate);
            break;

        }

      }
      $scope.users = _.chain(window.people)
        .filter(function(person) {
          return cond(person)
        })
        .sortBy(function(person) {
          return person.id
        })
        .sortBy(function(person) {
          return !person.isFighter;
        })
        .map(function(person) {
          return _.clone(person)
        })
        .value();
    }
  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("usersApp", init_angular_f_c, "all-figh");
  }
  init();
  window.registerInit(init)
})();
