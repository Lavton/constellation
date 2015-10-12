(function() {
  /*логика ангулара*/

  function init_angular_o_f_c($scope, $http) {
    $scope.window = window;
    $scope.fighter = {}
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
      })) || {}
      if (flag) {
        $scope.$apply();
      }
      initialize();
    });

    function initialize() {
      // c cервера
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

        if ($scope.fighter.year_of_entrance) {
          $scope.fighter.year_of_entrance = 1 * $scope.fighter.year_of_entrance;
          $scope.fighter.group_of_rights = 1 * $scope.fighter.group_of_rights;
          $scope.fighter.id = $scope.fighter.id * 1;
          $scope.fighter.uid = $scope.fighter.uid * 1;
        }
        $scope.fighter.isCandidate = Boolean($scope.fighter.isCandidate * 1);
        $scope.fighter.isFighter = Boolean($scope.fighter.isFighter * 1);
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
          uid: $scope.fighter.uid,
          fighter_id: userid
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

        $scope.$apply();
        /*с ВК*/
        var data_vk = {
          user_ids: $scope.fighter.uid,
          fields: ["photo_200", "domain"]
        }
        getVkData($scope.fighter.uid, ["photo_200", "domain"],
          function(response) {
            $scope.fighter.photo = response[$scope.fighter.uid].photo_200;
            $scope.$apply();
          }
        );
      });
    }

    /*конец инициализации*/

    $scope.goodView = window.goodTelephoneView;

    /* меняет местами просмотр и редактирование*/
    $scope.editPerson = function() {
      $(".user-edit").removeClass("hidden");
      $(".user-edit").hide();
      $(".user-info").hide("slow");
      $(".user-edit").show("slow", function() {
        $('html, body').animate({
          scrollTop: $(".scrl").offset().top
        }, 500); // анимируем скроолинг к элементу

      });
      $scope.master = angular.copy($scope.fighter);
      $scope.newuser = angular.copy($scope.fighter);

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

    // убирает форму редактирования
    $scope.hideEdit = function() {
      $(".user-edit").hide("slow");
      $(".user-info").show("slow");
    }

    /*удаляет бойца*/
    $scope.killUser = function() {
      if (confirm("Точно удалить профиль?")) {
        var data = {
          action: "kill_user",
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
            var lnk = document.createElement("a");
            lnk.setAttribute("class", "ajax-nav")
            $(lnk).attr("href", "/about/users");
            $("#page-container").append(lnk);
            $(lnk).trigger("click");
          }
        });
      }
    }

    // готовит данные для отображения прошедших смен
    function showShifts(json) {
      $scope.shifts = json.shifts;
      $scope.achievements = json.achievements;
      _.each($scope.shifts, function(detachment, index, list) {
        detachment.fn_date = new Date(detachment.finish_date);
      });
      _.each($scope.achievements, function(element) {
        element.start_year *= 1;
        element.finish_year *= 1;
      })
    }

    // отображает панель для редактирования
    $scope.editAchvs = function() {
      $scope.edit_achiv = true;
    }

    $scope.master_achv = {}
      // редактирование достижения
    $scope.editAchv = function(achv) {
      $scope.master_achv[achv.id] = angular.copy(achv);
      achv.edit_flag = true;
    }

    // отменяем редактирование
    $scope.notOkEditAchv = function(achv) {
      for (var i = 0; i < $scope.achievements.length; i++) {
        if ($scope.achievements[i].id == achv.id) {
          $scope.achievements[i] = angular.copy($scope.master_achv[achv.id]);
          break;
        }
      };
    }

    // сохраняем редактирование, отправляем на сервер.
    $scope.okEditAchv = function(achv) {
      var data = angular.copy(achv);
      data.action = "ok_edit_achv"
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        if (response.result == "Success") {
          achv.edit_flag = false;
        }
      });
    }

    // удаляет достижение
    $scope.deleteAchv = function(achv) {
      if (confirm("точно удалить достижение?")) {
        var data = angular.copy(achv);
        data.action = "delete_achv"
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            var achievements = []
            for (var i = 0; i < $scope.achievements.length; i++) {
              if ($scope.achievements[i].id != achv.id) {
                achievements.push($scope.achievements[i])
              }
            };
            $scope.achievements = achievements;
            $scope.$apply();
          }
        });
      }
    }

    // форма для добавления достижения
    $scope.addAch = function() {
      $scope.add_achiv = true;
      $scope.new_achv = {}
      $scope.new_achv.start_year = (new Date()).getFullYear();
      $scope.new_achv.finish_year = (new Date()).getFullYear();
    }

    // добавляет достижение на сервер
    $scope.okAddAchv = function() {
      var data = angular.copy($scope.new_achv);
      data.action = "add_achv";
      data.fighter_id = userid;
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        if (response.result == "Success") {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        }
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
