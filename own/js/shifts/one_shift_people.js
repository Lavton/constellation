'use strict';
(function() {
  /*логика ангулара*/

  function init_angular_o_s_c($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 1] * 1;

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
      $.ajax({
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        $scope.fighters = []
        $scope.candidats = []
        $scope.all_apply = json.all_apply;
        // автоматически не сбиндить. Делаем вручную после всех загрузок
        var bind_comments = _.after(json.all_apply.length + 1, function() {
          _.each(json.all_apply, function(application) {
            $("div." + application.vk_id + "-comments").html(application.bbcomments)
          });
        })
        _.each(json.all_apply, function(person, id, list) {
          window.getPerson(person.vk_id * 1, function(pers, flag) {
            var set_f = _.after(7, function() {
              if (pers.isFighter == true) {
                $scope.fighters.push(person)
              } else {
                $scope.candidats.push(person)
              }
              bind_comments();

            })
            _.extend(person, pers)
            if (flag) {
              $scope.$apply();
            }

            _.each(person.likes, function(person, ind, list) {
              if (person) {
                window.getPerson(person, function(pers, flag) {
                  list[ind] = pers;
                  if (flag) {
                    $scope.$apply();
                  }
                })
              }
              set_f();
            })
            _.each(person.dislikes, function(person, ind, list) {
              if (person) {
                window.getPerson(person, function(pers, flag) {
                  list[ind] = pers;
                  if (flag) {
                    $scope.$apply();
                  }
                })
              }
              set_f();
            })
            set_f();
          })
        });

        /*отдельно обрабатываем запрос про себя. Ибо инфы в нём больше*/
        $scope.me = json.myself;
        if ($scope.me) {
          _.extend($scope.me, _.find(window.people, function(p) {
            return p.uid * 1 == $scope.me.vk_id * 1;
          }))
          _.each($scope.me.likes, function(person, ind, list) {
            if (person) {
              window.getPerson(person, function(pers, flag) {
                list[ind] = pers;
                if (flag) {
                  $scope.$apply();
                }
              })
            }
          })
          _.each($scope.me.dislikes, function(person, ind, list) {
            if (person) {
              window.getPerson(person, function(pers, flag) {
                list[ind] = pers;
                if (flag) {
                  $scope.$apply();
                }
              })
            }
          })
          var bbdata = {
            bbcode: json.myself.comments,
            ownaction: "bbcodeToHtml"
          };
          console.log("comments")
          console.log(bbdata)
          $.ajax({
            type: "POST",
            url: "/standart/markitup/sets/bbcode/parser.php",
            dataType: 'text',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            console.log(rdata)
            $scope.me.bbcomments = rdata;
            $("div.me-comments").html(rdata); // почему-то бинд не работает(
            $scope.$apply();
          })
        }

        var comments = [];
        _.each(json.all_apply, function(element, index, list) {
          comments.push({
            id: element.vk_id,
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
          _.each($scope.all_apply, function(element, index, list) {
            element.bbcomments = _.findWhere(rdata, {
              id: element.vk_id
            }).bbcomment;
          });
          bind_comments();
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
      $scope.adding.soc = paste_data.social > 1;
      $scope.adding.nonsoc = paste_data.social % 2 ? true : false;
      $scope.adding.prof = paste_data.profile > 1;
      $scope.adding.nonprof = paste_data.profile % 2 ? true : false;
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
      $("#page-container").trigger("_edit_guess", [$scope.adding]);
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

    /*удалить все заявки на смену*/
    $scope.killappsShift = function() {
      if (confirm("Точно удалить все заявки на поездку? (сама смена не удалиться)")) {
        var aft_click = _.after($scope.all_apply.length, function() {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);

          $("#page-container").append(lnk);
          $(lnk).trigger("click");
        })
        _.each($scope.all_apply, function(element, index, list) {
          var data = {};
          data.action = "del_from_shift";
          data.shift_id = shiftid;
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
          }).done(function(json) {
            aft_click()
          });
        })
      }
    }

    /*удалить заявку на смену*/
    $scope.deleteGuess = function(delid) {
      if (confirm("удалить заявку?")) {
        var data = {};
        data.action = "del_from_shift";
        data.shift_id = shiftid;
        if (delid) {
          data.vk_id = delid;
        }
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
        }).done(function(json) {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        });
      }
    }
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
