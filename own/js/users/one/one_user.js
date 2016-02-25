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
          $scope.user.entance_university_year *= 1;
        }

        $scope.notCSbutEdit = ($scope.user.id == window.getCookie('id') * 1) && (window.current_group < window.groups.COMMAND_STAFF.num);
        $scope.user.group_of_rights = 1 * $scope.user.group_of_rights;
        $scope.user.isCandidate = Boolean($scope.user.isCandidate * 1);
        $scope.user.isFighter = Boolean($scope.user.isFighter * 1);
        $scope.user.course = $scope.getCourse($scope.user.entance_university_year)
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
      $scope.no_university = false;
      $scope.no_department = false;
      $scope.master = angular.copy($scope.user);
      $scope.newperson = angular.copy($scope.user);
      $scope.newperson.status = 1;
      if ($scope.user.isCandidate) {
        $scope.newperson.status = 2
      }
      if ($scope.user.isFighter) {
        $scope.newperson.status = 3;
      }
      $scope.newperson.group_of_rights *= 1;

      // получить список универов
      var data = {
        "action": "get_university_list"
      }
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        $scope.universities = _.groupBy(json.universities, "university")
        $scope.university_array = ["--нет--"];
        _.forEach($scope.universities, function(element, university) {
          $scope.university_array.push(university);
        });
        $scope.newperson.old_university = _.findIndex($scope.university_array,
          function(uni) {
            return uni == $scope.newperson.university;
          })
        if ($scope.newperson.old_university == -1) {
          $scope.newperson.old_university = 0;
        } else {
          /*$scope.universities[$scope.university_array[$scope.newperson.old_university]]
          [$scope.newperson.old_department].id;*/
          $scope.newperson.old_department = _.findIndex($scope.universities[$scope.newperson.university], function(dep) {
            return dep.department == $scope.newperson.department;
          }) + ""
        }
        $scope.newperson.old_university += ""

        $scope.$apply();
        console.log("univ", $scope.universities)
        console.log("univ", $scope.university_array)
      })

    };

    /*отправляет на сервер изменения*/
    $scope.editPersonSubmit = function(is_me) {
      // сначала создаём новый университет+институт, если нужно
      // console.log("hello", is_me)

      if (($scope.no_university || $scope.no_department) && ($scope.newperson.university || $scope.newperson.department)) {
        var data = {
          "action": "new_university",
          "university": $scope.newperson.university || $scope.university_array[$scope.newperson.old_university],
          "department": $scope.newperson.department
        }
        console.log("UNI",data)
        $.ajax({
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          console.log("univ", json)
          $scope.newperson.university_id = json.id
          send_user()
        })
      } else {
        // строчка "--нет--" для универа
        if (!($scope.newperson.old_university * 1)) {
          $scope.newperson.university_id = 0
          $scope.newperson.university = null;
          $scope.newperson.department = null;
          $scope.newperson.entance_university_year = null;
        } else {
          $scope.newperson.university_id = $scope.universities[$scope.university_array[$scope.newperson.old_university]][$scope.newperson.old_department].id;
          $scope.newperson.university = $scope.universities[$scope.university_array[$scope.newperson.old_university]][$scope.newperson.old_department].university;
          $scope.newperson.department = $scope.universities[$scope.university_array[$scope.newperson.old_university]][$scope.newperson.old_department].department;
        }
        send_user()
      }

      function send_user() {
        // теперь отправляем изменения о пользователе
        $scope.newperson.phone = window.getPhone($scope.newperson.phone);
        $scope.newperson.second_phone = window.getPhone($scope.newperson.second_phone);
        var data = angular.copy($scope.newperson);
        $scope.user = angular.copy($scope.newperson);
        if (is_me) {
          data.id = 0;
        }
        data.action = "user_modify"
          console.log("SEND USER", data)
        $.ajax({
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          console.log("GET USER", json)
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

    // подбирает факультеты под универ
    $scope.changeUniversity = function(id) {

    }

    $scope.getCourse = function(year) {
      var d1 = new Date(year + '-09-01')
      var d2 = new Date()
      var years = (d2 - d1) / (1000 * 60 * 60 * 24 * 365) + 1
      return Math.floor(years);
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
