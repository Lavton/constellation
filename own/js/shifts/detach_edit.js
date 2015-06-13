'use strict';
(function() {
  /*логика ангулара*/
  function init_angular_o_s_c($scope, $http, $locale) {
    /*инициализация*/
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 2] //TODO сделать тут нормально!
    $scope.isNumber = _.isNumber;
    $scope.shift = {};
    $scope.adding = {};
    $scope.adding.vk_likes = {};
    $scope.rankings = {}
    $scope.max_rank = 0; // изначально расстановок нет
    $scope.new_rank = {}
    $scope.detachment = [];
    $scope.newdetachment = {
      people: [],
      comments: "",
      fieldKeys: []

    };

    var data = {
      action: "get_one_detach_info",
      id: shiftid,
      "edit": true
    }
    $.ajax({
      type: "POST",
      url: "/handlers/shift.php",
      dataType: "json",
      data: $.param(data)
    }).done(function(json) {
      console.log("new way")
      console.log(json)
      $scope.shift = json.shift;
      $scope.shift.fn_date = new Date($scope.shift.finish_date);
      $scope.all_apply = json.all_apply;
      $scope.detachments = json.detachments;
      _.each(json.all_apply, function(person, id, list) {
        window.getPerson(person.vk_id * 1, function(pers, flag) {
          _.extend(person, pers)
          if (flag) {
            $scope.$apply();
          }

        })
      });
      _.each($scope.detachments, function(element, index, list) {
        element.ranking *= 1
      });
      $scope.rankings = _.groupBy($scope.detachments, function(detach) {
        return detach.ranking;
      })
      $scope.max_rank = _.chain($scope.rankings).keys().map(function(key) {
        return key * 1
      }).max().value()
      if (!_.isFinite($scope.max_rank)) {
        $scope.max_rank = 0
      }

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
          $("div." + detachment.in_id + "-bbcomment").html(detachment.bbcomments)
        });
      });

      /*люди*/
      _.each($scope.detachments, function(detachment, index, list) {
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

    /*конец инициализации*/

    /*подтверждение, что человек найден*/
    $scope.okAddPerson = function() {
      $scope.newdetachment.people.push($scope.newdetachment.newPerson)
      $scope.newdetachment.newPerson = "";
      $scope.setFieldKeys();
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

    /*удаляет человека из рассмотрения для создания отряда*/
    $scope.deletePersonEdit = function(key) {
      console.log(key)
      var people = []
      for (var i = 0; i < $scope.newdetachment.people.length; i++) {
        if (i != key) {
          people.push($scope.newdetachment.people[i])
          console.log(i)
          console.log($scope.newdetachment.people[i])
        }
      };
      $scope.newdetachment.people = people;
      $scope.setFieldKeys();
      console.log($scope.newdetachment)

    }

    // говорит, что создаём новую расстановку
    $scope.newRanking = function(flag) {
      $scope.new_rank = {}
      $scope.new_rank.ranking = $scope.max_rank + 1
      $scope.max_rank += 1;
      $scope.new_rank.edit = false;
    }

    /*скрывает растановку*/
    $scope.hideRanking = function() {
      $scope.new_rank = {}
    }

    /*обновляет значение ключей*/
    $scope.setFieldKeys = function() {
      var keys = [];
      for (var i = ($scope.newdetachment.people).length - 1; i >= 0; i--) {
        keys.push(i);
      };
      $scope.newdetachment.fieldKeys = keys;
    }
    $scope.setFieldKeys();

    /*редактировать расстановку*/
    $scope.editRanking = function(index) {
      console.log($scope.rankings[index])
      $scope.new_rank = angular.copy($scope.rankings[index])
      $scope.new_rank.ranking = index
      $scope.new_rank.edit = true;
      _.each($scope.detachments, function(detachment) {
        if (detachment.ranking * 1 == $scope.new_rank.ranking * 1) {
          $("div." + detachment.in_id + "-bbcomments").html(detachment.bbcomments)
        }
      })
    }

    /*инвертирует видимость ддля создания / редактирования отряда*/
    $scope.addDetachment = function() {
      if ($scope.add_det) {
        $(".addDetachment").text("добавить отряд в расстановку")
        if (_.isNumber($scope.newdetachment.editKey)) { // если редактирование
          var detachments = []
          for (var i = 0; i < $scope.detachments.length; i++) {
            detachments.push($scope.detachments[i])
            if (i == $scope.newdetachment.editKey) {
              detachments.push(angular.copy($scope.cached_detach));
            }
          };
          $scope.detachments = detachments;
          _.delay(function() {
            _.each($scope.detachments, function(detachment) {
              if (detachment.ranking * 1 == $scope.new_rank.ranking * 1) {
                $("div." + detachment.in_id + "-bbcomments").html(detachment.bbcomments)
              }
            })
          }, 20);

        }
        $scope.newdetachment = {}
      } else {
        $(".addDetachment").text("Скрыть добавление")
      }
      $scope.add_det = !$scope.add_det;
    }

    /*создаёт отряд или редактирует его (key отвечает)*/
    $scope.addDetachmentSubmit = function(key) {
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
              new_people.push($scope.newdetachment.people[i] + "")
            }
          };
          /*пушим в БД, конкатинируя имена*/
          var data = {
            comments: $scope.newdetachment.comments,
            people: new_people.join("$"),
            action: "add_detachment",
            shift_id: shiftid,
            ranking: $scope.new_rank.ranking
          }
          if (_.isNumber(key)) { // если редактируем
            data.action = "edit_detachment";
            data.in_id = $scope.cached_detach.in_id;
          }
          console.log(data)
          $.ajax({ //TODO: make with angular
            type: "POST",
            url: "/handlers/shift.php",
            dataType: "json",
            data: $.param(data)
          }).done(function(json) {
            console.log(json)
            var vk_idsD = new_people
            getVkData(vk_idsD, ["domain", "photo_50"],
              function(response) {
                var detachment = {
                  people: new_people,
                  "ranking": $scope.new_rank.ranking,
                  "comments": $scope.newdetachment.comments
                }
                _.each(detachment.people, function(person, index_p, list) {
                  var vk_d = response[person];
                  if (vk_d) {
                    detachment.people[index_p] = vk_d;
                  }
                })

                // чтобы показать и комментарии
                if (_.isNumber(key)) {
                  detachment.in_id = $scope.cached_detach.in_id;
                } else {
                  detachment.in_id = _.uniqueId();
                }
                var bbdata = {
                  bbcode: detachment.comments,
                  ownaction: "bbcodeToHtml"
                };
                $.ajax({
                  type: "POST",
                  url: "/standart/markitup/sets/bbcode/parser.php",
                  dataType: 'text',
                  global: false,
                  data: $.param(bbdata)
                }).done(function(comment_data) {
                  detachment.bbcomments = comment_data;
                  $("div." + detachment.in_id + "-bbcomments").html(detachment.bbcomments)
                  $scope.$apply();
                });

                $scope.detachments.push(detachment)
                $scope.newdetachment.people = [];
                $scope.newdetachment.comments = "";
                $scope.newdetachment.fieldKeys = [];
                $scope.setFieldKeys();
                $scope.$apply();
              });

            console.log(new_people);
            $scope.$apply();
          });
        });
    }

    /*редактировать отряд*/
    $scope.editDetachment = function(key) {
      // $scope.edit_detachment = $scope.rankings[index][key];
      $scope.newdetachment = angular.copy($scope.detachments[key])
      $scope.cached_detach = angular.copy($scope.newdetachment)
      for (var i = 0; i < $scope.newdetachment.people.length; i++) {
        if ($scope.newdetachment.people[i].domain) {
          $scope.newdetachment.people[i] = $scope.newdetachment.people[i].domain;
        }
      };
      $scope.newdetachment.editKey = key;
      var detachments = [];
      for (var i = 0; i < $scope.detachments.length; i++) {
        if (i != key) {
          detachments.push($scope.detachments[i])
        }
      };
      $scope.detachments = detachments;
      if ($scope.add_det) {
        $(".addDetachment").text("добавить отряд в расстановку")
      } else {
        $(".addDetachment").text("Скрыть добавление")
      }
      $scope.add_det = !$scope.add_det;

      $scope.setFieldKeys();
    }

    $scope.deleteRanking = function(index) {
      if (confirm("удалить расстановку №" + index + "?")) {
        var data = {};
        data.action = "del_detach_shift";
        var rel = _.after($scope.rankings[index].length, function() {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        })
        _.each($scope.rankings[index], function(element) {
          data.in_id = element.in_id;
          $.ajax({
            type: "POST",
            url: "/handlers/shift.php",
            dataType: "json",
            data: $.param(data)
          }).done(function(json) {
            rel();
          });
        });
      }
    }

    /*удаляем отряд*/
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
          var detachments = []
          for (var i = 0; i < $scope.detachments.length; i++) {
            if (i != key) {
              detachments.push($scope.detachments[i])
            }
          };
          $scope.detachments = detachments;
          $scope.$apply();
        });
      }
    }

    /*публикуем расстановку*/
    $scope.publish = function(index) {
      console.log(index)
      if (confirm("Расстановка станет видна всем. Опубликовать?")) {
        var data = {
          action: "publish_rank",
          "rank_id": index,
          "shift_id": shiftid
        };
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          if (json.result == "Success") {
            window.location.href = "/events/shifts/" + shiftid;
          } else {
            alert("смена уже содержит опубликованную расстановку. Уберите её, чтобы добавить новую!")
          }
        });
      }
    }
  }


  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("oneShiftAppEditDetach", init_angular_o_s_c, "shift-edit-detach");
  }
  init();
  window.registerInit(init)
})();
