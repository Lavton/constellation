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

    // показывает редактирование
    $scope.editShiftInfo = function() {
      $(".shift-edit").removeClass("hidden");
      $(".shift-edit").hide();
      $(".shift-info").hide("slow");
      $(".shift-edit").show("slow", function() {
        $('html, body').animate({
          scrollTop: $(".scrl").offset().top
        }, 500); // анимируем скроолинг к элементу
      });

      $scope.newshift = angular.copy($scope.shift);
    };

    // убирает форму редактирования
    $scope.hideEdit = function() {
      $(".shift-edit").hide("slow");
      $(".shift-info").show("slow");
    }


    $scope.editShiftSubmit = function() {
      $("#page-container").trigger("_apply_shift", [{
        "name": $scope.newshift.name,
        "shiftid": shiftid
      }]);
      $scope.shift = angular.copy($scope.newshift);
    }
  }


  // часть, отвечающая за само отображение смены
  function init_angular_base($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 1] * 1;
    $scope.formatDate = window.formatDate;

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
        _.each($("span.date"), function(self) {
          $(self).pickmeup({
            format: 'Y-m-d',
            hide_on_select: true,
            date: new Date($(self).attr("class").split(" ")[1])
          });
        })

      });
    });
    $(document).keyup(function(e) {
      if (e.keyCode == 27) {
        $('.date').pickmeup('hide');
      }
    });


    // показывает редактирование
    $("body").on("click", "button.edit-shift", function() {
      $scope.newshift = angular.copy($scope.shift);
      $scope.$apply();
    })

    // сохранить редактирование: кнопка
    $("#page-container").on("_apply_shift", function(e, json) {
      if (shiftid * 1 == json.shiftid) {
        console.log("get", json)
        $scope.newshift.name = json.name;
        $scope.$apply();
        $scope.editShiftSubmit();
      }
    });

    // сохранить редактирование
    $scope.editShiftSubmit = function() {
      $scope.newshift.st_date = new Date($scope.newshift.start_date);
      $scope.newshift.fn_date = new Date($scope.newshift.finish_date);
      var data = angular.copy($scope.newshift);
      $scope.shift = angular.copy($scope.newshift);
      data.action = "edit_shift"

      var bbdata = {
        bbcode: $scope.newshift.comments,
        ownaction: "bbcodeToHtml"
      };
      $.ajax({
        type: "POST",
        url: "/standart/markitup/sets/bbcode/parser.php",
        dataType: 'text',
        global: false,
        data: $.param(bbdata)
      }).done(function(rdata) {
        $scope.newshift.bbcomments = rdata;
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
        console.log(response);
        $(".shift-edit").hide("slow");
        $(".shift-info").show("slow", function() {
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
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", "/shifts/");
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        });
      }
    }

  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").siteInput()
    });

    window.init_ang("oneShiftAppName", init_angular_name, "shift-name");
    window.init_ang("oneShiftAppCommon", init_angular_base, "shift-common");
  }
  init();
  window.registerInit(init)
})();
