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
    $scope.newdetachment = {}

    /*инициализация*/
    var data = {
      action: "get_one_info_adding",
      id: shiftid
    }
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

    /*ещё забираем инфу про расстановку*/
    var data = {
      action: "get_one_detach_info",
      id: shiftid,
    }
    $.ajax({
      type: "POST",
      url: "/handlers/shift.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(json) {
      $scope.detachments = json.detachments;

      /*запись комментариев*/
      var comments = []
      _.each(json.detachments, function(element, index, list) {
        comments.push({
          id: element.in_id,
          comment: element.comments
        });
      });
      var bbdata = {
        bbcode: comments,
        ownaction: "bbcodesToHtml"
      };
      $.ajax({
        type: "POST",
        url: "/standart/markitup/sets/bbcode/parser.php",
        dataType: 'json',
        global: false,
        data: $.param(bbdata)
      }).done(function(comment_data) {
        _.each($scope.detachments, function(detachment, index, list) {
          detachment.bbcomments = _.findWhere(comment_data, {
            id: detachment.in_id
          }).bbcomment;
          $("div." + detachment.in_id + "-bbcomment-detach").html(detachment.bbcomments)
        });
      });

      /*люди*/
      _.each($scope.detachments, function(detachment, index, list) {
        detachment.children_num *= 1;
        detachment.people = detachment.people.split("$");
        _.each(detachment.people, function(person, index, list) {
          window.getPerson(person, function(pers, flag) {
            list[index] = pers;
            if (flag) {
              $scope.$apply();
            }
          })
        })
      });

      $scope.$apply();
    });


    /*появление редактирования. Перебрасываем событием из людей*/
    $("#page-container").on("_edit_guess", function(e, json, sid) {
      if (shiftid * 1 == sid * 1) {
        if ($scope.show_add) {
          $scope.tableToAdd();
        }
        $scope.show_edit = true;
        $scope.adding = json;
        $scope.$apply();
      }
    });

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
      console.log($(this).val())
      console.log(path)
      console.log(self[path[path.length - 1]])
      self[path[path.length - 1]] = $(this).val();
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

    /*убрать расстановку для редактирования*/
    $scope.removeRank = function() {
      var data = {
        "action": "remove_rank",
        "shift_id": shiftid
      }
      $.ajax({
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
        var lnk = document.createElement("a");
        lnk.setAttribute("class", "ajax-nav")
        $(lnk).attr("href", window.location.href + "/edit");
        $("#page-container").append(lnk);
        $(lnk).trigger("click")
      });
    }

    /*добавлять доп инфу про отряд (количество детей и описание) может КС и вожатые этого отряда*/
    $scope.canEditDet = function(key) {
      if (window.current_group >= window.groups.COMMAND_STAFF.num) {
        return true;
      }
      var ouid = window.getCookie("vk_id") * 1;
      return _.find($scope.detachments[key].people, function(element) {
        return element.uid * 1 == ouid;
      })
    }

    /*редактируем про детей для тех, кто может*/
    $scope.editChildren = function(key) {
      $scope.detachments[key].childrenEdit = true;
      $scope.master_child_num = $scope.detachments[key].children_num;
      $scope.master_child_dis = $scope.detachments[key].children_dis;
    }

    /*отменяет редактирование про детей*/
    $scope.editChildrenDel = function(key) {
      $scope.detachments[key].childrenEdit = false;
      $scope.detachments[key].children_num = $scope.master_child_num;
      $scope.detachments[key].children_dis = $scope.master_child_dis;
    }

    /*сохраняет редактирование про детей*/
    $scope.editChildrenOK = function(key) {
      $scope.detachments[key].childrenEdit = false;
      var data = {
        action: "set_children",
        in_id: $scope.detachments[key].in_id,
        "children_num": $scope.detachments[key].children_num,
        "children_dis": $scope.detachments[key].children_dis
      }
      $.ajax({
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {});
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
