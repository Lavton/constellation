'use strict';
(function() {
  /*логика ангулара*/

  function init_angular_o_s_c($scope, $http) {
    $scope.window = window;
    $scope._ = _;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 1] * 1;

    $scope.id = shiftid;
    $scope.shift = {};
    $scope.adding = {};
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
      _.each(json.detachments, function(detachment) {
        var cached = _.find(window.people, function(p) {
          return p.id * 1 == detachment.user * 1;
        })
        if (cached) {
          cached = _.omit(cached, "id")
        }
        _.extend(detachment, cached)
      })
      $scope.detachments = _.groupBy(json.detachments, function(detach) {
        return detach.id
      })
      if (_.toArray($scope.detachments)[0]) {
        var bbdata = {
          bbcode: _.toArray($scope.detachments)[0][0].comments,
          ownaction: "bbcodeToHtml"
        };
        $.ajax({
          type: "POST",
          url: "/standart/markitup/sets/bbcode/parser.php",
          dataType: 'text',
          global: false,
          data: $.param(bbdata)
        }).done(function(rdata) {
          $("div.rank-comments").html(rdata)
          $scope.$apply();
        });
      }
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
      $('html, body').animate({
        scrollTop: $(".scrl-apply").offset().top
      }, 500);
    }

    /*синхронизируемся, где надо*/
    $("#shift-add-self").on("_final_select", "input", function(e) {
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
      var data = angular.copy($scope.adding);
      var qw;
      if (is_edit) {
        qw = "Редактировать запись?"
        data.action = "edit_appliing";
      } else {
        qw = "Записаться на смену?"
        data.action = "apply_to_shift";
      }
      data.is_edit = is_edit;
      if (confirm(qw)) {
        _.each(data, function(element, index, list) {
          if (!element) {
            data[index] = null;
          }
        })
        data.id = shiftid;
        data.likes = [];
        if (data.like1) {
          (data.likes).push(data.like1)
        }
        if (data.like2) {
          (data.likes).push(data.like2)
        }
        if (data.like3) {
          (data.likes).push(data.like3)
        }
        data.dislikes = [];
        if (data.dislike1) {
          (data.dislikes).push(data.dislike1)
        }
        if (data.dislike2) {
          (data.dislikes).push(data.dislike2)
        }
        if (data.dislike3) {
          (data.dislikes).push(data.dislike3)
        }
        console.log(data)

        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          console.log(json)
          data.shiftid = shiftid
          if (is_edit) {
            $scope.show_edit = false;
          } else {
            $scope.tableToAdd()
          }
          $("#page-container").trigger("_guess_apply_shift", [data]);
          $scope.$apply();
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
      var ouid = window.getCookie("id") * 1;
      return _.find($scope.detachments[key], function(element) {
        return element.user * 1 == ouid;
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
        id: key,
        "children_num": $scope.detachments[key][0].children_num,
        "children_dis": $scope.detachments[key][0].children_dis
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
      $("input.vk_input").siteInput()
    });
    window.init_ang("oneShiftAppAdd", init_angular_o_s_c, "shift-add-self");
  }
  init();
  window.registerInit(init)
})();
