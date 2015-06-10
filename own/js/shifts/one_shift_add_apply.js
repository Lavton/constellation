'use strict';
(function() {
  /*логика ангулара*/

  function init_angular_o_s_c($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 1] * 1;

    $scope.id = shiftid;
    $scope.shift = {};
    $scope.adding = {};
    $scope.adding.vk_likes = {};

    /*инициализация*/
    var data = {
      action: "get_one_info_adding",
      id: shiftid
    }
    $scope.shift.photo_200 = "http://vk.com/images/camera_b.gif"
    $.ajax({
      type: "POST",
      url: "/handlers/shift.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(json) {
      console.log(json);
      if (json.like_h) {
        $scope.app2 = _.after(json.like_h.length, function() {

          $scope.like_h = json.like_h;
          console.log("LIKE")
          console.log($scope.like_h)
          $scope.$apply();
        })
        _.each(json.like_h, function(element, index, list) {
          window.getPerson(element.vk_id, function(pers, flag) {
            _.extend(element, pers);
            $scope.app2();
          })
        });
      }
    })

    /*пока не нужно, но потом для редактирования записи*/
    $scope.editGuess = function(who, is_smbdy) {
      if ($scope.show_add) {
        $scope.tableToAdd();
      }
      $scope.show_edit = true;
      var paste_data = angular.copy(who);
      if (is_smbdy) {
        $scope.adding.smbdy = paste_data.domain;
      }
      $scope.adding.prob = paste_data.probability;
      $scope.adding.soc = paste_data.social > 1;
      $scope.adding.nonsoc = paste_data.social % 2 ? true : false;
      $scope.adding.prof = paste_data.profile > 1;
      $scope.adding.nonprof = paste_data.profile % 2 ? true : false;
      $scope.adding.min_age = paste_data.min_age;
      $scope.adding.max_age = paste_data.max_age;
      $scope.adding.like1 = paste_data.like_1.domain;
      $scope.adding.like2 = paste_data.like_2.domain;
      $scope.adding.like3 = paste_data.like_3.domain;
      $scope.adding.dislike1 = paste_data.dislike_1.domain;
      $scope.adding.dislike2 = paste_data.dislike_2.domain;
      $scope.adding.dislike3 = paste_data.dislike_3.domain;
      $scope.adding.comments = paste_data.comments;
    }

    /*инвертирует состояние записи*/
    $scope.tableToAdd = function() {
      if ($scope.show_add) {
        $(".show_button").text("Записаться на смену")
      } else {
        $(".show_button").text("Скрыть запись")
      }
      $scope.show_add = !$scope.show_add;
      $scope.show_edit = false;
    }


    /*добавляем(ся) на смену. Или редактируем.
    Что делает - зависит от is_edit*/
    $scope.guessAdd = function(is_edit) {
      var data = $scope.adding;
      var qw;
      if (is_edit) {
        qw = "Редактировать запись?"
        data.action = "edit_appliing";
      } else {
        qw = "Записаться на смену?"
        data.action = "apply_to_shift";
      }
      if (confirm(qw)) {
        _.each(data, function(element, index, list) {
          if (!element) {
            data[index] = null;
          }
        })
        data.shift_id = $scope.shift.id;
        /*преобразуем доп. поля*/
        data.social = data.soc * 1 + data.nonsoc * 2;
        data.profile = data.prof * 1 + data.nonprof * 2;
        /*заменяем введённые домены на uid*/
        getVkData([data.smbdy, data.like1, data.like2, data.like3, data.dislike1, data.dislike2, data.dislike3], ["domain"],
          function(response) {
            if (data.smbdy) { // мы комсостав и хотим добавить другого человека
              data.vk_id = response[data.smbdy].uid;
            }
            if (data.like1) {
              data.like_one = response[data.like1].uid;
            }
            if (data.like2) {
              data.like_two = response[data.like2].uid;
            }
            if (data.like3) {
              data.like_three = response[data.like3].uid;
            }

            if (data.dislike1) {
              data.dislike_one = response[data.dislike1].uid;
            }
            if (data.dislike2) {
              data.dislike_two = response[data.dislike2].uid;
            }
            if (data.dislike3) {
              data.dislike_three = response[data.dislike3].uid;
            }
            $.ajax({
              type: "POST",
              url: "/handlers/shift.php",
              dataType: "json",
              data: $.param(data)
            }).done(function(json) {
              var saved = $(".saved");
              $(saved).stop(true, true);
              $(saved).fadeIn("slow");
              $(saved).fadeOut("slow");
              var lnk = document.createElement("a");
              lnk.setAttribute("class", "ajax-nav")
              $(lnk).attr("href", window.location.href);
              $("#page-container").append(lnk);
              $(lnk).trigger("click")
            });

          });
      }
    }

    $scope.killappsShift = function() {
      if (confirm("Точно удалить все заявки на поездку? (сама смена не удалиться)")) {
        var data = {};
        data.action = "del_from_shift";
        data.shift_id = $scope.shift.id;
        _.each(data, function(element, index, list) {
          if (!element) {
            data[index] = null;
          }
        })
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {});
        _.each($scope.all_apply, function(element, index, list) {
          var data = {};
          data.action = "del_from_shift";
          data.shift_id = $scope.shift.id;
          data.vk_id = element.vk_id;
          _.each(data, function(element, index, list) {
            if (!element) {
              data[index] = null;
            }
          })
          $.ajax({
            type: "POST",
            url: "/handlers/shift.php",
            dataType: "json",
            data: $.param(data)
          }).done(function(json) {});
        })
      }
      var lnk = document.createElement("a");
      lnk.setAttribute("class", "ajax-nav")
      $(lnk).attr("href", window.location.href);

      $("#page-container").append(lnk);
      $(lnk).trigger("click");
    }

    $scope.submit = function() {
      $scope.shift.st_date = new Date($scope.shift.start_date);
      $scope.shift.fn_date = new Date($scope.shift.finish_date);
      var name = "";
      var st_month = $scope.shift.st_date.getMonth() * 1 + 1; //нумерация с нуля была
      var fn_month = $scope.shift.fn_date.getMonth() * 1 + 1;
      if ((st_month == 10) || (st_month == 11)) {
        //октябрь или ноябрь -> осень
        name = "Осень";
      } else if ((st_month == 12) || (st_month == 1)) {
        //декабрь или январь -> зима
        name = "Зима";
      } else if ((st_month == 3) || (st_month == 4)) {
        //март или апрель -> весна
        name = "Весна";
      } else {
        name = "Лето ";
        if (fn_month == 6) { //в июне кончается первая смена
          name += "1";
        } else if (st_month == 6) { //в июне начинается вторая смена (или первая, но её уже обработали)
          name += "2";
        } else if (st_month == 7) { //в июле начинается третья смена
          name += "3";
        } else { //осталась четвёртая
          name += "4";
        }
      }
      name += ", " + $scope.shift.fn_date.getFullYear()
      if ($scope.shift.place) {
        name += " (" + $scope.shift.place + ")";
      }
      $scope.shift.name = name;

      var data = angular.copy($scope.shift);
      data.action = "set_new_data"
      _.each(data, function(element, index, list) {
        if (!element) {
          data[index] = null;
        }
      })

      var bbdata = {
        bbcode: $scope.shift.comments,
        ownaction: "bbcodeToHtml"
      };
      $.ajax({
        type: "POST",
        url: "/standart/markitup/sets/bbcode/parser.php",
        dataType: 'text',
        global: false,
        data: $.param(bbdata)
      }).done(function(rdata) {
        $scope.shift.bbcomments = rdata,
          $scope.$apply();
      });


      $http.post('/handlers/shift.php', data).success(function(response) {
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });
    }

    $scope.detachment = [];
    $scope.newdetachment = {
      people: ["", ],
      comments: ""

    };
    $scope.newdetachment.fieldKeys = [];

    $scope.newdetachment.setFieldKeys = function() {
      var keys = [];
      for (var i = ($scope.newdetachment.people).length - 1; i >= 0; i--) {
        keys.push(i);
      };
      $scope.newdetachment.fieldKeys = keys;
    }
    $scope.newdetachment.setFieldKeys();

    $scope.addNewPersonDetach = function() {
      $scope.newdetachment.people.push("");
      $scope.newdetachment.setFieldKeys();
    }

    $scope.addDetachment = function() {
      if ($scope.add_det) {
        $(".addDetachment").text("добавить отряд в расстановку")
      } else {
        $(".addDetachment").text("Скрыть добавление")
      }
      $scope.add_det = !$scope.add_det;
    }

    /*создаёт расстановку*/
    $scope.addDetachmentSubmit = function() {
      getVkData($scope.newdetachment.people, ["domain"],
        function(response) {
          /*если передали имя ВК - заменяем на uid*/
          for (var i = 0; i < $scope.newdetachment.people.length; i++) {
            if (response[$scope.newdetachment.people[i]]) {
              $scope.newdetachment.people[i] = response[$scope.newdetachment.people[i]].uid;
            }
          };
          var new_people = [];
          for (var i = 0; i < $scope.newdetachment.people.length; i++) {
            if ($scope.newdetachment.people[i]) {
              new_people.push($scope.newdetachment.people[i])
            }
          };
          /*пушим в БД, конкатинируя имена*/
          var data = {
            comments: $scope.newdetachment.comments,
            people: new_people.join("$"),
            action: "add_detachment",
            shift_id: $scope.shift.id
          }
          $.ajax({ //TODO: make with angular
            type: "POST",
            url: "/handlers/shift.php",
            dataType: "json",
            data: $.param(data)
          }).done(function(json) {
            var lnk = document.createElement("a");
            lnk.setAttribute("class", "ajax-nav")
            $(lnk).attr("href", window.location.href);
            $("#page-container").append(lnk);
            $(lnk).trigger("click")
          });
          $scope.$apply();
        });
    }

    $scope.editDetachment = function(key) {
      $scope.edit_detachment = $scope.detachments[key];
    }

    $scope.saveDetachComment = function() {
      if (confirm("редактировать комментарий?")) {
        var data = {};
        data.action = "edit_detach_comment";
        data.in_id = $scope.edit_detachment.in_id;
        data.comments = $scope.edit_detachment.comments;
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        });
      }
    }

    $scope.deleteDetachment = function(key) {
      if (confirm("удалить отряд?")) {
        var data = {};
        data.action = "del_detach_shift";
        data.in_id = $scope.detachments[key].in_id;
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        });
      }
    }

    $scope.resetInfo = function() {
      $scope.shift = angular.copy($scope.master);
    }

    $scope.killShift = function() {
      var fid = window.location.href.split("/")
      var shiftid = fid[fid.length - 1] //TODO сделать тут нормально!
      if (confirm("Точно удалить смену со всей информацией?")) {
        var data = {
          action: "kill_shift",
          id: shiftid
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            window.location = "/";
          }
        });
      }
    }
  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("oneShiftAppAdd", init_angular_o_s_c, "shift-add-self");
  }
  init();
  window.registerInit(init)
})();
