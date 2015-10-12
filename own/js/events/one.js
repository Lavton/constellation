'use strict';
(function() {
  // логика ангулара
  function init_angular_o_e_c($scope, $http, $locale) {
    var fid = window.location.href.split("/")
    var eventid = fid[fid.length - 1] //TODO сделать тут нормально!
    $scope.window = window;
    $scope.event = {};
    $scope.formatDate = window.formatDate;
    $scope.formatTimestamp = window.formatTimestamp;

    // инициируем для выбора даты
    $('input.date').pickmeup({
      format: 'Y-m-d',
      hide_on_select: true,
      change: function() {
        var path = this.getAttribute("ng-model").split(".")
        var self = $scope;
        for (var i = 0; i < path.length - 1; i++) {
          self = self[path[i]]
        };
        self[path[path.length - 1]] = $(this).val();
        if (!$scope.newevent.finish_date) {
          $scope.newevent.finish_date = $scope.newevent.start_date;
        }
        $scope.$apply();
        return true;
      }
    });
    $(document).keyup(function(e) {
      if (e.keyCode == 27) {
        $('.date').pickmeup('hide');
      }
    });


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
      $scope.editors = json.editors;
      $scope.appliers = json.appliers;
      $scope.children = json.children;
      console.log(json)
      $scope.$apply();

      // показываем календарь при клике на дату
      _.each($("span.date"), function(self) {
        $(self).pickmeup({
          format: 'Y-m-d',
          hide_on_select: true,
          date: new Date($(self).attr("class").split(" ")[1])
        });
      })

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
        $scope.$apply();
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
        _.each($scope.appliers, function(person) {
          console.log(person)
          _.extend(person, _.findWhere(window.people, {
            "id": person.user * 1
          }));
          // если мы уже записаны на мероприятие - кнопку убираем
          if (person.user == window.getCookie("fighter_id") * 1) {
            $scope.IAmIn = true;
          }
        })

        $scope.app2();
      });
      $scope.app2();
    });

    // показывает реактирование
    $scope.editEventInfo = function() {

      $(".event-edit").removeClass("hidden");
      $(".event-edit").hide();
      $(".event-info").hide("slow");
      $(".event-edit").show("slow", function() {
        $('html, body').animate({
          scrollTop: $(".scrl").offset().top
        }, 500); // анимируем скроолинг к элементу

      });

      $scope.newevent = angular.copy($scope.event);

      // если ещё не получали список
      if (!$scope.eventsBase) {
        var data = {
          "action": "get_base_and_par"
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/event.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          console.log(response);
          $scope.pos_parents = response.pos_parents;
          $scope.pos_parents.push({
            id: null,
            name: "--нет--"
          })
          $scope.eventsBase = response.eventsBase;
          $scope.eventsBase.push({
            id: null,
            name: "--нет--"
          })
          $scope.$apply();
        });
      }
    }

    // убирает форму редактирования
    $scope.hideEdit = function() {
      $(".event-edit").hide("slow");
      $(".event-info").show("slow");
    }

    // отсылает изменения на сервер и изменяет мероприятие видимое
    $scope.editEventSubmit = function() {
      var data = angular.copy($scope.newevent);
      data.action = "edit_event";
      $scope.event = angular.copy($scope.newevent);
      console.log(data);

      // чтобы показывать изменённые данные
      var bbdata = {
        bbcode: $scope.newevent.comments,
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
        $scope.$apply();
      });
      if ($scope.event.parent_id) {
        $scope.event.parent_name = _.findWhere($scope.pos_parents, {
          id: $scope.event.parent_id
        }).name;
      } else {
        $scope.event.parent_name = null;
      }
      $scope.event.parent_date = "";

      if ($scope.event.base_id) {
        $scope.event.base_dis = _.findWhere($scope.eventsBase, {
          id: $scope.event.base_id
        }).name;
      } else {
        $scope.event.base_dis = null;
      }

      // сам запрос на сервер для изменения
      $.ajax({
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        // анимация на выходе
        $(".event-edit").hide("slow");
        $(".event-info").show("slow", function() {
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

    $scope.killEvent = function() {
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
            var lnk = document.createElement("a");
            lnk.setAttribute("class", "ajax-nav")
            $(lnk).attr("href", "/events/");
            $("#page-container").append(lnk);
            $(lnk).trigger("click")
          }); // анимируем скроолинг к элементу
        });
      }
    }

    // создаём текст поста для ВК. Пока заглушка
    $scope.exportToVK = function() {
      $scope.vk_export = "\n__________\n";
      $scope.vk_export += $scope.event.name + ($scope.parent_event.name ? (" (" + $scope.parent_event.name + ")\n") : "\n");
      $scope.vk_export += "Начало: " + $scope.event.start_time + "\n";
      $scope.vk_export += "Место: " + $scope.event.place + "\n";
      $scope.vk_export += "Контактное лицо: " + $scope.event.contact + "\n";
      $scope.vk_export += "\n__________\n";
      _.each($scope.event.users, function(person) {
        var pers_string = "*" + person.domain + " (" + person.first_name + "), ";
        $scope.vk_export += pers_string
      })
    }

    // добавляем на мероприятие. По умолчанию - себя
    $scope.applyToEvent = function(person) {
      var data = {
        "action": "apply_to_event",
        "event_id": $scope.event.id,
      }
      if (person) {
        data.id = _.find(window.people, function(p) {
          return person == p.domain;
        }).id
      } else {
        $scope.IAmIn = true;
        $scope.appliers.push(_.findWhere(window.people, {
          "id": window.getCookie("fighter_id") * 1
        }))
      }
      console.log("apply", data.id)

      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        $scope.personToApply = "";
        if (data.id) {
          $scope.appliers.push(_.findWhere(window.people, {
            "id": data.id * 1
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
        var id = null;
        if (person) {
          data.id = person.id
          id = person.id * 1;
        } else {
          id = window.getCookie("fighter_id") * 1;
          $scope.IAmIn = false;
        }
        $scope.appliers = _.reject($scope.appliers, function(user) {
          return user.id * 1 == id * 1;
        })
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/event.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {});
      }
    }

    // добавить к редакторам мероприятия
    $scope.addToEventEditors = function(person) {
      var data = {
        "action": "add_to_event_editors",
        "event_id": $scope.event.id,
      }
      data.id = _.find(window.people, function(p) {
        return person == p.domain;
      }).id

      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        $scope.personToAdd = "";
        if (data.id) {
          $scope.editors.push(_.findWhere(window.people, {
            "id": data.id * 1
          }))
        }
        $scope.$apply();
      });
    }

    // удалить из редакторов
    $scope.deleteFromEditors = function(person) {
      console.log(person)
      if (confirm("Удалить? ")) {
        var data = {
          "action": "delete_editor_from_event",
          "event_id": $scope.event.id,
          "id": person.id ? person.id * 1 : person.editor * 1
        }
        $scope.editors = _.reject($scope.appliers, function(user) {
          return user.id * 1 == data.id * 1;
        })
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/event.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          console.log(response)
        });
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
