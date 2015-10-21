'use strict';
(function() {
  /*логика ангулара*/

  function init_angular_o_s_c($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 1] * 1;
    // кс доступно больше инфы. Поэтому и лучше расположить друг под другом
    if (window.current_group == window.groups.COMMAND_STAFF.num) {
      $(".table-all-sh").attr("style", "width:100% !important;");
    }
    $scope.id = shiftid;
    $scope.shift = {};
    $scope.fighters = [];
    $scope.candidats = [];
    $scope.adding = {};
    $scope.adding.vk_likes = {};
    $(".shift-info").removeClass("hidden")
      /*инициализация*/
    window.setPeople(function() {
      initialize();
    })

    function initialize() {
      var data = {
        action: "get_one_info_people",
        id: shiftid
      }
      console.log("YEAH", data)
      $.ajax({
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
        $scope.fighters = []
        $scope.candidats = []
        $scope.all_apply = json.all_apply;
        // автоматически не сбиндить. Делаем вручную после всех загрузок
        var bind_comments = _.after(1, function() {
          _.each(json.all_apply + 1, function(application) {
            $("div." + application.id + "-comments").html(application.bbcomments)
          });
        })
        _.each(json.all_apply, function(person, id, list) {
          console.log("all_apply", json.all_apply)
          var cached_person = _.find(window.people, function(p) {
            return p.id * 1 == person.user * 1;
          })
          _.extend(person, cached_person);

          _.each(person.likes, function(person2, ind, list) {
            var cached = _.find(window.people, function(p) {
              return p.id * 1 == person2.other * 1;
            })
            _.extend(list[ind], cached);
          })
          person.dislikes = _.filter(person.likes, function(person2) {
            return person2.pole * 1 < 0;
          })
          person.likes = _.filter(person.likes, function(person2) {
            return person2.pole * 1 > 0;
          })

          if (person.isFighter == true) {
            $scope.fighters.push(person)
          } else {
            $scope.candidats.push(person)
          }
        });

        $scope.$apply();
        // отдельно обрабатываем запрос про себя. Ибо инфы в нём больше
        $scope.me = json.myself;
        if ($scope.me) {
          _.extend($scope.me, _.find(window.people, function(p) {
            return p.id * 1 == $scope.me.user * 1;
          }))
          _.each($scope.me.likes, function(person, ind, list) {
            var cached = _.find(window.people, function(p) {
              return p.id * 1 == person.other * 1;
            })
            _.extend(list[ind], cached);
          })
          $scope.me.dislikes = _.filter($scope.me.likes, function(person) {
            return person.pole * 1 < 0;
          })
          $scope.me.likes = _.filter($scope.me.likes, function(person) {
            return person.pole * 1 > 0;
          })
          $scope.$apply();

          var bbdata = {
            bbcode: json.myself.comments,
            ownaction: "bbcodeToHtml"
          };
          $.ajax({
            type: "POST",
            url: "/standart/markitup/sets/bbcode/parser.php",
            dataType: 'text',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            $scope.me.bbcomments = rdata;
            $("div.me-comments").html(rdata); // почему-то бинд не работает(
            $scope.$apply();
          })
        }

        var comments = [];
        _.each(json.all_apply, function(element, index, list) {
          comments.push({
            id: element.id,
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
        }).done(function(rdata) {
          console.log(rdata);
          _.each($scope.all_apply, function(element, index, list) {
            element.bbcomments = _.findWhere(rdata, {
              id: element.id + ""
            }).bbcomment;
          });
          _.each(json.all_apply, function(application) {
            $("div." + application.id + "-comments").html(application.bbcomments)
          });
          $scope.$apply();
        });
      })
    }

    /*для редактирование записи перебрасываем параметры*/
    $scope.editGuess = function(who, is_smbdy) {

      var paste_data = angular.copy(who);
      $scope.adding = {}
      if (is_smbdy) {
        $scope.adding.smbdy = paste_data.domain;
      }
      $scope.adding.prob = paste_data.probability;
      $scope.adding.min_age = paste_data.min_age;
      $scope.adding.max_age = paste_data.max_age;
      if (paste_data.likes[0]) {
        $scope.adding.like1 = paste_data.likes[0].domain;
      }
      if (paste_data.likes[1]) {
        $scope.adding.like2 = paste_data.likes[1].domain;
      }
      if (paste_data.likes[2]) {
        $scope.adding.like3 = paste_data.likes[2].domain;
      }
      if (paste_data.dislikes[0]) {
        $scope.adding.dislike1 = paste_data.dislikes[0].domain;
      }
      if (paste_data.dislikes[1]) {
        $scope.adding.dislike2 = paste_data.dislikes[1].domain;
      }
      if (paste_data.dislikes[2]) {
        $scope.adding.dislike3 = paste_data.dislikes[2].domain;
      }
      $scope.adding.comments = paste_data.comments;
      $("#page-container").trigger("_edit_guess", [$scope.adding, shiftid]);
    }

    /*удалить заявку на смену*/
    $scope.deleteGuess = function(delid) {
      if (confirm("удалить заявку?")) {
        var data = {};
        data.action = "del_from_shift";
        data.event = shiftid;
        if (delid) {
          data.user = delid;
        }
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          if (delid == $scope.me.id) {
            delid = null;
          }

          if (!delid) {
            delid = $scope.me.id;
            $scope.me = {}

          }
          $scope.fighters = _.reject($scope.fighters, function(user) {
            return user.id * 1 == delid * 1;
          })
          $scope.candidats = _.reject($scope.candidats, function(user) {
            return user.id * 1 == delid * 1;
          })

          $scope.$apply();
        });
      }
    }


    // после записи человека на смену
    $("#page-container").on("_guess_apply_shift", function(e, json) {

      if (shiftid * 1 == json.shiftid) {
        json.likes = [];
        if (json.like1) {
          (json.likes).push({
            "other": json.like1,
            "pole": 1
          })
        }
        if (json.like2) {
          (json.likes).push({
            "other": json.like2,
            "pole": 1
          })
        }
        if (json.like3) {
          (json.likes).push({
            "other": json.like3,
            "pole": 1
          })
        }
        if (json.dislike1) {
          (json.likes).push({
            "other": json.dislike1,
            "pole": -1
          })
        }
        if (json.dislike2) {
          (json.likes).push({
            "other": json.dislike2,
            "pole": -1
          })
        }
        if (json.dislike3) {
          (json.likes).push({
            "other": json.dislike3,
            "pole": -1
          })
        }
        console.log("get", json)
        if (!json.smbdy) {
          json.smbdy = window.getCookie("id");
          $scope.me = angular.copy(json);
          $scope.me.user = json.smbdy;
          _.extend($scope.me, _.find(window.people, function(p) {
            return p.id * 1 == $scope.me.user * 1;
          }))
          _.each($scope.me.likes, function(person, ind, list) {
            var cached = _.find(window.people, function(p) {
              return p.id * 1 == person.other * 1;
            })
            _.extend(list[ind], cached);
          })
          $scope.me.dislikes = _.filter($scope.me.likes, function(person) {
            return person.pole * 1 < 0;
          })
          $scope.me.likes = _.filter($scope.me.likes, function(person) {
            return person.pole * 1 > 0;
          })
          $scope.$apply();

          var bbdata = {
            bbcode: json.comments,
            ownaction: "bbcodeToHtml"
          };
          $.ajax({
            type: "POST",
            url: "/standart/markitup/sets/bbcode/parser.php",
            dataType: 'text',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            $scope.me.bbcomments = rdata;
            $("div.me-comments").html(rdata); // почему-то бинд не работает(
            $scope.$apply();
          })

        }
      }
      // debugger;

      var person = angular.copy(json);
      person.user = json.smbdy;
      var cached_person = _.find(window.people, function(p) {
        return p.id * 1 == person.user * 1;
      })
      _.extend(person, cached_person);

      _.each(person.likes, function(person2, ind, list) {
        var cached = _.find(window.people, function(p) {
          return p.id * 1 == person2.other * 1;
        })
        _.extend(list[ind], cached);
      })
      person.dislikes = _.filter(person.likes, function(person2) {
        return person2.pole * 1 < 0;
      })
      person.likes = _.filter(person.likes, function(person2) {
        return person2.pole * 1 > 0;
      })

      if (person.isFighter == true) {
        $scope.fighters.push(person)
      } else {
        $scope.candidats.push(person)
      }
      var bbdata = {
        bbcode: json.comments,
        ownaction: "bbcodeToHtml"
      };
      $.ajax({
        type: "POST",
        url: "/standart/markitup/sets/bbcode/parser.php",
        dataType: 'text',
        global: false,
        data: $.param(bbdata)
      }).done(function(rdata) {
        person.bbcomments = rdata;
        $("div." + person.id + "-comments").html(rdata); // почему-то бинд не работает(
        $scope.$apply();
      })

      $scope.$apply();
    });
  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("oneShiftAppPeople", init_angular_o_s_c, "shift-people");
  }
  init();
  window.registerInit(init)
})();
