'use strict';
(function() {
  /*логика ангулара*/

  function init_angular_name($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 1] * 1;

    /*инициализация*/
    $scope.id = shiftid;
    $scope.shift = {};
    $scope.adding = {};
    $scope.adding.vk_likes = {};
    $(".shift-info").removeClass("hidden")


    var data = {
      action: "get_one_info_name",
      id: shiftid
    }
    $.ajax({
      type: "POST",
      url: "/handlers/shift.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(json) {
      $scope.shift = json.shift_name;
      $scope.shift.fn_date = new Date($scope.shift.finish_date);
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
      $scope.$apply();
    });

    /*переход между режимами редактирования и просмотра*/
    $scope.editShiftInfo = function() {
      $(".shift-info").toggleClass("hidden");
      $(".shift-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.shift);
    };

    /*отмена редактирования*/
    $scope.resetInfo = function() {
      $scope.shift = angular.copy($scope.master);
    }

    $scope.applyShift = function() {
      $("#page-container").trigger("_apply_shift", [{
        "time_name": $scope.shift.time_name,
        "shiftid": shiftid
      }]);
    }
  }


  /*часть, отвечающая за само отображение смены*/
  function init_angular_base($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 1] * 1;

    /*инициализация*/
    $scope.id = shiftid;
    var data = {
      action: "get_one_info_shift",
      id: shiftid
    }
    $.ajax({
      type: "POST",
      url: "/handlers/shift.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(json) {
      console.log(json)
      $scope.shift = json.shift;
      $scope.shift.fn_date = new Date($scope.shift.finish_date);
      $scope.shift.st_date = new Date($scope.shift.start_date);
      $scope.shift.visibility *= 1;
      $("a.shift_priv").attr("href", json.prev.mid)
      $("a.shift_next").attr("href", json.next.mid)
      if (!json.prev.mid) {
        $("a.shift_priv").hide();
      }

      if (!json.next.mid) {
        $("a.shift_next").hide();
      }
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
        $scope.shift.bbcomments = rdata;
        $scope.$apply();
        $(".bbcomments-shift").html($scope.shift.bbcomments) // почему-то иначе не работает()
      });
    });

    /*отменить редактирование*/
    $scope.resetInfo = function() {
      $scope.shift = angular.copy($scope.master);
    }

    /*переход между режимами редактирования и просмотра*/
    $("body").on("click", "button.edit-shift", function() {
      $scope.master = angular.copy($scope.shift);
      $scope.$apply();
    })

    /*отменить редактирование: кнопка*/
    $("body").on("click", "button.cansel-shift", function() {
      $scope.resetInfo();
      $scope.$apply();
    })

    /*сохранить редактирование: кнопка*/
    $("#page-container").on("_apply_shift", function(e, json) {
      if (shiftid * 1 == json.shiftid) {
        $scope.shift.time_name = json.time_name;
        $scope.$apply();
        $scope.submit();
      }
    });

    /*сохранить редактирование*/
    $scope.submit = function() {
      $scope.shift.st_date = new Date($scope.shift.start_date);
      $scope.shift.fn_date = new Date($scope.shift.finish_date);
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
        $scope.shift.bbcomments = rdata;
        $(".bbcomments-shift").html($scope.shift.bbcomments) // почему-то иначе не работает(
        $scope.$apply();
      });
      $.ajax({
        type: "POST",
        url: '/handlers/shift.php',
        dataType: 'text',
        global: false,
        data: $.param(data)
      }).done(function(response) {
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });
    }

    /*удалить смену*/
    $scope.killShift = function() {
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

    window.init_ang("oneShiftAppName", init_angular_name, "shift-name");
    window.init_ang("oneShiftAppCommon", init_angular_base, "shift-common");
  }
  init();
  window.registerInit(init)
})();
