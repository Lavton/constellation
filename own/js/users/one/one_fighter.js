(function() {
  /*логика ангулара*/

  function init_angular_o_f_c($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var userid = fid[fid.length - 1] * 1;

    $("#page-container").on("_final_select", "input", function(e) {
      $scope.fighter.domain = $(this).val()
      $scope.$apply()
    })

    /*инициализация*/
    $scope.fighter = {};
    $scope.f_groups = _.toArray(window.groups)
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

        // отображение смен
        var data = {
          action: "get_shifts_nd_ach",
          uid: $scope.fighter.uid
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            showShifts(response);
            $scope.$apply();
          }
        });

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
    $scope.submit = function(is_me) {
      $scope.fighter.domain = $("input.vk_now").val()
      getVkData($scope.fighter.domain, ["photo_200", "domain"],
        function(response) {
          var data = angular.copy($scope.fighter);
          data.vk_id = "" + response[$scope.fighter.domain].uid;
          data.uid = data.vk_id;
          data.action = "fighter_modify"
          if (is_me) {
            data.id = 0;
          }
          _.each(data, function(element, index, list) {
            if (!element) {
              data[index] = null;
            }
          })
          _.extend($scope.fighter, response[$scope.fighter.domain])
          $scope.fighter.photo = $scope.fighter.photo_200;
          console.log("data submite")
          console.log(data);
          $http.post('/handlers/user.php', data).success(function(response) {
            window.clearPeople()
            window.setPeople()
            var saved = $(".saved");
            $(saved).stop(true, true);
            $(saved).fadeIn("slow");
            $(saved).fadeOut("slow");
          });

        }
      );
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

    // готовит данные для отображения прошедших смен
    function showShifts(json) {
      console.log(json);
      $scope.shifts = json.shifts;
      _.each($scope.shifts, function(detachment, index, list) {
        detachment.fn_date = new Date(detachment.finish_date);
      });
    }
  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("oneFighterApp", init_angular_o_f_c, "one-fighter");
  }
  init();
  window.registerInit(init)

})();
