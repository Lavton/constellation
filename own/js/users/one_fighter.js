(function() {
  /*логика ангулара*/
  function init_angular_o_f_c($scope, $http) {
    // window.setPeople(window.init_vk_search.init());
    $scope.window = window;
    /*инициализация*/
    $scope.fighter = {};
    var fid = window.location.href.split("/")
    var userid = fid[fid.length - 1] * 1
    $(".user-info").removeClass("hidden")
    window.setPeople(function(flag) {
      $scope.fighter = _.clone(_.find(window.people, function(person) {
        return person.id == userid && person.isFighter == true;
      }))
      if (flag) {
        $scope.$apply();
      }
      initialize();
    });

    function initialize() {
      $scope.app2 = _.after(2, $scope.$apply)
        /*c cервера*/
      var data = {
        action: "get_one_info",
        id: userid
      }
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        _.extend($scope.fighter, json.user);

        $scope.fighter.year_of_entrance = 1 * $scope.fighter.year_of_entrance;
        $scope.fighter.group_of_rights = 1 * $scope.fighter.group_of_rights;
        $("a.profile_priv").attr("href", json.prev.mid)
        $("a.profile_next").attr("href", json.next.mid)
        if (!json.prev.mid) {
          $("a.profile_priv").hide();
        }

        if (!json.next.mid) {
          $("a.profile_next").hide();
        }
        $scope.app2();
      });
      /*с ВК*/
      var data_vk = {
        user_ids: $scope.fighter.domain,
        fields: ["photo_200", "domain"]
      }
      getVkData($scope.fighter.domain, ["photo_200", "domain"],
        function(response) {
          $scope.fighter.photo = response[$scope.fighter.domain].photo_200;
          $scope.app2();
        }
      );
    }

    /*конец инициализации*/

    $scope.goodView = window.goodTelephoneView;

    /* меняет местами просмотр и редактирование*/
    $scope.editPerson = function() {
      $(".user-info").toggleClass("hidden");
      $(".user-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.fighter);
    };

    /*отправляет на сервер изменения*/
    $scope.submit = function() {
      get_vk(function() {
        var data = angular.copy($scope.fighter);
        data.uid = "" + data.uid;
        data.action = "fighter_modify"
        _.each(data, function(element, index, list) {
          if (!element) {
            data[index] = null;
          }
        })
        $http.post('/handlers/user.php', data).success(function(response) {
          window.clearPeople()
          window.setPeople()
          var saved = $(".saved");
          $(saved).stop(true, true);
          $(saved).fadeIn("slow");
          $(saved).fadeOut("slow");
          window.clearPeople();
        });
      });
    }

    /*отменяет редактирование*/
    $scope.resetInfo = function() {
      $scope.fighter = angular.copy($scope.master);
    }

    /*удаляет бойца*/
    $scope.killFighter = function() {
      if (confirm("Точно удалить профиль?")) {
        var data = {
          action: "kill_fighter",
          id: userid
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            window.clearPeople()
            window.location = "/about/users";
          }
        });
      }
    }

    /*при падении фокуса с редактирования ВК обновляем данные*/
    $("#page-container").on("focusout", "input.vk-domain", function() {
      get_vk()
    });

    function get_vk(callback) {
      var data_vk = {
        user_ids: $scope.fighter.domain,
        fields: ["photo_200", "domain"]
      }
      getVkData($scope.fighter.domain, ["photo_200", "domain"],
        function(response) {
          var user_vk = response[$scope.fighter.domain];
          $scope.fighter.domain = user_vk.domain
          $scope.fighter.photo = user_vk.photo;
          $scope.fighter.uid = user_vk.uid;

          $scope.$apply();
          if (callback) {
            callback();
          }
        }
      );
    }
  }


  var state = window.state.about.users.fighters.one;
  window.init_ang("oneFighterApp", init_angular_o_f_c, "one-fighter");
  state.controller = "oneFighterApp";
  state.init_f = init_angular_o_f_c;
  state.element = "one-fighter";

})();