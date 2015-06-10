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
    $scope.adding.profile = 3;
    $scope.adding.social = 3;

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
      $scope.myself = json.myself;
      if (json.like_h) {
        $scope.app2 = _.after(json.like_h.length, function() {
          $scope.like_h = json.like_h;
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

    /*синхронизируемся, где надо*/
    $("#page-container").on("_final_select", "input", function(e) {
      /*в ng-model лежит путь. Но не прямое значение(( Пройдём по нему до почти конца и впишем*/
      var path = this.getAttribute("ng-model").split(".")
      var self = $scope;
      for (var i = 0; i < path.length - 1; i++) {
        self = self[path[i]]
      };
      self[path[path.length - 1]] = this.value;
      $scope.$apply();
    })


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
        data.shift_id = shiftid;
        /*преобразуем доп. поля*/
        // data.social = data.soc * 1 + data.nonsoc * 2;
        // data.profile = data.prof * 1 + data.nonprof * 2;
        data.social = 3;
        data.profile = 3;

        /*заменяем введённые домены на uid*/
        if (data.smbdy) { // мы комсостав и хотим добавить другого человека
          data.vk_id = _.find(window.people, function(p) {
            return data.smbdy == p.domain;
          }).uid
        }
        if (data.like1) {
          data.like_one = _.find(window.people, function(p) {
            return data.like1 == p.domain;
          }).uid
        }
        if (data.like2) {
          data.like_two = _.find(window.people, function(p) {
            return data.like2 == p.domain;
          }).uid
        }
        if (data.like3) {
          data.like_three = _.find(window.people, function(p) {
            return data.like3 == p.domain;
          }).uid
        }

        if (data.dislike1) {
          data.dislike_one = _.find(window.people, function(p) {
            return data.dislike1 == p.domain;
          }).uid
        }
        if (data.dislike2) {
          data.dislike_two = _.find(window.people, function(p) {
            return data.dislike2 == p.domain;
          }).uid
        }
        if (data.dislike3) {
          data.dislike_three = _.find(window.people, function(p) {
            return data.dislike3 == p.domain;
          }).uid
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
