'use strict';
(function() {
  // логика ангулара
  function init_angular_o_e_c($scope, $http, $locale) {
    var fid = window.location.href.split("/")
    var eventid = fid[fid.length - 1] //TODO сделать тут нормально!
    $scope.window = window;
    $scope.event = {};
    $(".event-info").removeClass("hidden")
    var data = {
      action: "get_one_info",
      id: eventid
    }
    $.ajax({
      type: "POST",
      url: "/handlers/event.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(json) {
      $scope.app2 = _.after(3, function() {
        $scope.$apply();
      })

      $scope.event = json.event;
      $scope.event.visibility = $scope.event.visibility * 1;
      $scope.parent_event = json.parent_event;
      var bbdata = {
        bbcode: $scope.event.comments,
        ownaction: "bbcodeToHtml"
      };
      $.ajax({
        type: "POST",
        url: "/standart/markitup/sets/bbcode/parser.php",
        dataType: 'text',
        global: false,
        data: $.param(bbdata)
      }).done(function(rdata) {
        $scope.event.bbcomments = rdata,
          $("div.bb-codes").html(rdata)
        $scope.app2();
      });
      //TODO make works all html. (jquery?)

      $("a.event_priv").attr("href", json.prev.mid)
      $("a.event_next").attr("href", json.next.mid)
      if (!json.prev.mid) {
        $("a.event_priv").hide();
      }
      if (!json.next.mid) {
        $("a.event_next").hide();
      }


      window.setPeople(function() {
        _.each($scope.event.users, function(person) {
          _.extend(person, _.findWhere(window.people, {
            "uid": person.vk_id * 1
          }));
          // если мы уже записаны на мероприятие - кнопку убираем
          if (person.uid == window.getCookie("vk_id") * 1) {
            $scope.IAmIn = true;
          }
        })

        $scope.app2();
      });


      $scope.app2();
    });
    $scope.editEventInfo = function(flag) {
      $(".event-info").toggleClass("hidden");
      $(".event-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.event);

      if (flag) { // начинаем редактировать
        var spl = $scope.event.start_time.split(" ");
        $scope.event.start_date = spl[0];
        var time = spl[1].split(":");
        $scope.event.start_ttime = time[0] + ":" + time[1];

        spl = $scope.event.end_time.split(" ");
        $scope.event.end_date = spl[0];
        $scope.event.end_ttime = spl[1];
        var time = spl[1].split(":");
        $scope.event.end_ttime = time[0] + ":" + time[1];

        //Если ещё не редактировали до этого, найдём все мероприятия в кандидаты в родители
        if (!$scope.pos_parents) {
          var data = {
            action: "get_reproduct",
            visibility: $scope.event.visibility,
            end_time: $scope.event.end_time
          }
          $http.post('/handlers/event.php', data).success(function(response) {
            $scope.pos_parents = [];
            if (response.pos_parents) {
              $scope.pos_parents = response.pos_parents;
            }
            $scope.pos_parents.push({
              id: null,
              name: "--нет--"
            })
          });

        }
      }
    }
    $scope.resetInfo = function() {
      $scope.shift = angular.copy($scope.master);
    }

    $scope.submit = function() {
      $scope.event.start_time = $scope.event.start_date + " " + $scope.event.start_ttime + ":00";
      $scope.event.end_time = $scope.event.end_date + " " + $scope.event.end_ttime + ":00";
      var data = angular.copy($scope.event);
      data.editor_user = null;
      data.action = "set_new_data"
      _.each(data, function(element, index, list) {
        if (!element) {
          data[index] = null;
        }
      })
      if (!$scope.parent_event) {
        $scope.parent_event = {}
      }
      $scope.parent_event.id = $scope.event.parent_id;
      $scope.parent_event.name = _.findWhere($scope.pos_parents, {
        id: $scope.event.parent_id
      }).name;

      var bbdata = {
        bbcode: $scope.event.comments,
        ownaction: "bbcodeToHtml"
      };
      $.ajax({
        type: "POST",
        url: "/standart/markitup/sets/bbcode/parser.php",
        dataType: 'text',
        global: false,
        data: $.param(bbdata)
      }).done(function(rdata) {
        $scope.event.bbcomments = rdata,
          $scope.$apply();
      });

      $http.post('/handlers/event.php', data).success(function(response) {
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });
    }

    $scope.killEvent = function() {
      var fid = window.location.href.split("/")
      var shiftid = fid[fid.length - 1] //TODO сделать тут нормально!
      if (confirm("Точно удалить мероприятие со всей информацией?")) {
        var data = {
          action: "kill_event",
          id: eventid
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/event.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          window.location = "/events/";
        });
      }
    }

    $scope.setContactMe = function() {
      var data = {
        action: "getMe"
      }
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        $scope.event.contact = response.me.name + " " + response.me.surname;
        if (response.me.phone) {
          $scope.event.contact += ", +7" + response.me.phone;
        }
        if (response.me.second_phone) {
          $scope.event.contact += ", +7" + response.me.second_phone;
        }

        $scope.$apply();
        getVkData(response.me.vk_id, ["domain"],
          function(vk_response) {
            $scope.event.contact = response.me.name + " " + response.me.surname + " ( https://vk.com/" + vk_response[response.me.vk_id].domain + " )";
            if (response.me.phone) {
              $scope.event.contact += ", +7" + response.me.phone;
            }
            if (response.me.second_phone) {
              $scope.event.contact += ", +7" + response.me.second_phone;
            }

            $scope.$apply();
          }
        );
      });

    }

    // создаём текст поста для ВК. Пока заглушка
    $scope.exportToVK = function() {
      $scope.vk_export = "sadsad";
    }

    // добавляем себя на мероприятие
    $scope.applyToEvent = function(person) {
      var data = {
        "action": "apply_to_event",
        "event_id": $scope.event.id,
      }
      if (person) {
        data.vk_id = _.find(window.people, function(p) {
          return person == p.domain;
        }).uid
      } else {
        $scope.IAmIn = true;
        $scope.event.users.push(_.findWhere(window.people, {
          "uid": window.getCookie("vk_id") * 1
        }))
      }
      console.log("apply", data.vk_id)

      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        $scope.personToApply = "";
        if (data.vk_id) {
          $scope.event.users.push(_.findWhere(window.people, {
            "uid": data.vk_id * 1
          }))
        }

        $scope.$apply();
      });
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

    // удаляем участие в мероприятии человека
    $scope.deleteApply = function(person) {
      if (confirm("Удалить? ")) {
        var data = {
          "action": "delete_apply_from_event",
          "event_id": $scope.event.id,
        }
        var uid = null;
        if (person) {
          data.vk_id = person.uid
          uid = person.uid * 1;
        } else {
          uid = window.getCookie("vk_id") * 1;
          $scope.IAmIn = false;
        }
        $scope.event.users = _.reject($scope.event.users, function(user) {
          return user.uid * 1 == uid;
        })

        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/event.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {});

      }
    }

  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("oneEventApp", init_angular_o_e_c, "event-one");
  }
  init();
  window.registerInit(init)
})();
