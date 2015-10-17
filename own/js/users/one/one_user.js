(function() {
  /*логика ангулара*/

  function init_angular_o_f_c($scope, $http) {
    $scope.window = window;
    $scope.user = {}
    $scope.isCS = window.current_group >= window.groups.COMMAND_STAFF.num
    $scope.newperson = {}
    $scope.pos_status = [{
      "id": 1,
      "title": "--нет--"
    }, {
      "id": 2,
      "title": "кандидат"
    }, , {
      "id": 3,
      "title": "боец"
    }]

    var fid = window.location.href.split("/")
    var userid = fid[fid.length - 1] * 1;

    $("#page-container").on("_final_select", "input", function(e) {
        $scope.user.domain = $(this).val()
        $scope.$apply()
      })
      // чтобы дату вводить
    window.initDatePicker($scope);
    
    /*инициализация*/
    $scope.user = {};
    $scope.f_groups = _.toArray(window.groups)
    $(".user-info").removeClass("hidden")
    window.setPeople(function(flag) {
      $scope.user = _.clone(_.find(window.people, function(person) {
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
        _.extend($scope.user, json.user);

        if ($scope.user.year_of_entrance) {
          $scope.user.year_of_entrance = 1 * $scope.user.year_of_entrance;
          $scope.user.group_of_rights = 1 * $scope.user.group_of_rights;
          $scope.user.id = $scope.user.id * 1;
          $scope.user.uid = $scope.user.uid * 1;
          $scope.canEdit = ($scope.user.id == window.getCookie('id') * 1) && window.current_group < window.groups.COMMAND_STAFF.num
        }
        $scope.user.isCandidate = Boolean($scope.user.isCandidate * 1);
        $scope.user.isFighter = Boolean($scope.user.isFighter * 1);

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
          uid: $scope.user.uid,
          id: userid
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          console.log(response);
          if (response.result == "Success") {
            $scope.shifts = response.shifts;
            $scope.achievements = response.achievements;
            _.each($scope.shifts, function(detachment, index, list) {
              detachment.fn_date = new Date(detachment.finish_date);
            });
            _.each($scope.achievements, function(element) {
              element.start_year *= 1;
              element.finish_year *= 1;
            })
            $(".achiv-info").removeClass("hidden");
            $scope.$apply();
          }
        });

        $scope.$apply();
        // показываем календарь при клике на дату
        _.each($("span.date"), function(self) {
            $(self).pickmeup({
              format: 'Y-m-d',
              hide_on_select: true,
              date: new Date($(self).attr("class").split(" ")[1])
            });
          })
          /*с ВК*/
        var data_vk = {
          user_ids: $scope.user.uid,
          fields: ["photo_200", "domain"]
        }
        getVkData($scope.user.uid, ["photo_200", "domain"],
          function(response) {
            $scope.user.photo = response[$scope.user.uid].photo_200;
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
      $scope.master = angular.copy($scope.user);
      $scope.newperson = angular.copy($scope.user);
      $scope.newperson.status = 1;
      if ($scope.user.isCandidate) {
        $scope.newperson.status = 2
      }
      if ($scope.user.isFighter) {
        $scope.newperson.status = 3;
      }
    };

    /*отправляет на сервер изменения*/
    $scope.editPersonSubmit = function(is_me) {
      $scope.newperson.phone = window.getPhone($scope.newperson.phone);
      $scope.newperson.second_phone = window.getPhone($scope.newperson.second_phone);
      var data = angular.copy($scope.newperson);
      $scope.user = angular.copy($scope.newperson);
      if (is_me) {
        data.id = 0;
      }
      data.action = "user_modify"
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
          // анимация на выходе
        $(".user-edit").hide("slow");
        $(".user-info").show("slow", function() {
          setTimeout(function() {
            var saved = $(".saved");
            $(saved).stop(true, true);
            $(saved).fadeIn("slow");
            $(saved).fadeOut("slow");
          }, 1000);
        });
        $('html, body').animate({
          scrollTop: $("nav").offset().top
        }, 500); // анимируем скроолинг к элементу
      })
    }

    // убирает форму редактирования
    $scope.hideEdit = function() {
      $(".user-edit").hide("slow");
      $(".user-info").show("slow");
    }

    // при изменении статуса
    $scope.newStatus = function(status) {
      if (status == 3) {
        $scope.newperson.group_of_rights = 3
        if (!$scope.newperson.year_of_entrance) {
          $scope.newperson.year_of_entrance = ((new Date()).getFullYear());
        }
      }
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
            $scope.achievements = _.reject($scope.achievements, function(achiv) {
              return achiv.id * 1 == achv.id * 1;
            })
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
    $scope.addAchvSubmit = function() {
      var data = angular.copy($scope.new_achv);
      data.action = "add_achv";
      data.user = userid;
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        if (response.id) {
          $scope.achievements.push(angular.copy($scope.new_achv));
        }
        $scope.new_achv = {};
        $scope.$apply();

      });
    }
  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("oneFighterApp", init_angular_o_f_c, "one-user");
  }
  init();
  window.registerInit(init)

})();
