'use strict';
(function() {
  /*логика ангулара*/
  function init_angular_o_s_c($scope, $http, $locale) {
    /*инициализация*/
    $scope.window = window;
    $scope._ = _;
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
      console.log(json)
      $scope.shift = json.shift;
      $scope.shift.fn_date = new Date($scope.shift.finish_date);
      $scope.all_apply = json.all_apply;
      $scope.detachments = json.detachments;
      _.each(json.all_apply, function(person, id, list) {
        var cached = _.find(window.people, function(p) {
          return p.id * 1 == person.user * 1;
        })
        _.extend(person, cached)
      });
      _.each($scope.detachments, function(element, index, list) {
        element.ranking *= 1
      });

      // группировка по расстановкам, а следом - по отрядам
      var rankings = _.groupBy($scope.detachments, function(detach) {
        return detach.ranking;
      })
      console.log("rankings", rankings)
      _.each(rankings, function(ranking, id, list) {
        $scope.rankings[id] = _.groupBy(ranking, function(detach) {
          return detach.id
        })
      })

      _.each($scope.rankings, function(ranking, id, list) {
        // инфа о людях
        _.each(ranking, function(detachments) {
          _.each(detachments, function(detachment) {
            var cached = _.find(window.people, function(p) {
              return p.id * 1 == detachment.user * 1;
            })
            _.extend(detachment, cached)
          })
        })

        // комментарии
        var bbdata = {
          bbcode: _.toArray(ranking)[0][0].comments,
          ownaction: "bbcodeToHtml"
        };
        $.ajax({
          type: "POST",
          url: "/standart/markitup/sets/bbcode/parser.php",
          dataType: 'text',
          global: false,
          data: $.param(bbdata)
        }).done(function(rdata) {
          _.toArray(ranking)[0][0].bbcomments = rdata,
            $("div.rank-comments-" + _.toArray(ranking)[0][0].ranking).html(rdata)
          $scope.$apply();
        });

      })



      $scope.$apply();
      $scope.max_rank = _.chain($scope.rankings).keys().map(function(key) {
        return key * 1
      }).max().value()
      if (!_.isFinite($scope.max_rank)) {
        $scope.max_rank = 0
      }

      /*запись комментариев*/
      // var comments = []
      // _.each(json.detachments, function(element, index, list) {
      //   comments.push({
      //     id: element.in_id,
      //     comment: element.comments
      //   });
      // });
      // var bbdata = {
      //   bbcode: comments,
      //   ownaction: "bbcodesToHtml"
      // };
      // $.ajax({
      //   type: "POST",
      //   url: "/standart/markitup/sets/bbcode/parser.php",
      //   dataType: 'json',
      //   global: false,
      //   data: $.param(bbdata)
      // }).done(function(comment_data) {
      //   _.each($scope.detachments, function(detachment, index, list) {
      //     detachment.bbcomments = _.findWhere(comment_data, {
      //       id: detachment.in_id
      //     }).bbcomment;
      //     $("div." + detachment.in_id + "-bbcomment").html(detachment.bbcomments)
      //   });
      // });

      /*люди*/
      // _.each($scope.detachments, function(detachment, index, list) {
      //   detachment.people = detachment.people.split("$");
      //   _.each(detachment.people, function(person, index, list) {
      //     window.getPerson(person, function(pers, flag) {
      //       list[index] = pers;
      //       if (flag) {
      //         $scope.$apply();
      //       }
      //     })
      //   })
      // });

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
    $("#shift-edit-detach").on("_final_select", "input", function(e) {
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
      var people = []
      for (var i = 0; i < $scope.newdetachment.people.length; i++) {
        if (i != key) {
          people.push($scope.newdetachment.people[i])
        }
      };
      $scope.newdetachment.people = people;
      $scope.setFieldKeys();

    }

    // говорит, что создаём новую расстановку
    $scope.newRanking = function() {
      var data = {
        action: "new_rank",
        shift: shiftid,
        show_it: 0
      }
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        $scope.new_rank = {}
        $scope.new_rank.ranking = response.id;
        _.each($scope.all_apply, function(el) {
          el.have_in_det = false;
        })
        $scope.new_rank.edit = false;
        $scope.$apply();
      });
    }

    $scope.saveComment = function(id) {
      var data = {
        action: "save_rank_comment",
        id: id,
        comments: $scope.new_rank.comments
      }
      console.log(data);
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });
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
      $scope.new_rank = angular.copy($scope.rankings[index])
      $scope.new_rank.ranking = index
      $scope.new_rank.comments = _.toArray($scope.new_rank)[0][0].comments
      $scope.new_rank.edit = true;
      // _.each($scope.detachments, function(detachment) {
      //   if (detachment.ranking * 1 == $scope.new_rank.ranking * 1) {
      //     $("div." + detachment.id + "-bbcomments").html(detachment.bbcomments)
      //     _.each(detachment.people, function(person) {
      //       var per = _.find($scope.all_apply, function(pers) {
      //         return pers.user * 1 == person.id * 1;
      //       })
      //       if (per) {
      //         per.have_in_det = true;
      //       }
      //     })
      //   }
      // })

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

    // создаёт отряд или редактирует его (key отвечает)
    $scope.addDetachmentSubmit = function(key) {
      var data = {
        "ranking": $scope.new_rank.ranking,
        "action": "add_detachment",
        "people": []
      }
      if (window.isNumeric(key)) { // если редактируем
        data.action = "edit_detachment";
        data.id = key;
      }

      _.each($scope.newdetachment.people, function(person, id, list) {
        if (window.isNumeric(person)) {
          data.people.push({
            "user": person,
            "name": null
          })
        } else {
          data.people.push({
            "user": null,
            "name": person
          })
        }
      })

      console.log(data)
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/shift.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json_detach) {
        console.log(json_detach)
          // debugger;
        _.each(data.people, function(person) {
          console.log(person)
          var cached = _.find(window.people, function(p) {
            return p.id * 1 == person.user * 1;
          })
          person.ranking = data.ranking * 1
          person.id = json_detach.id * 1
          _.extend(person, cached);
        })
        $scope.rankings[data.ranking][json_detach.id] = data.people
        $scope.add_det = false;
        $(".addDetachment").text("добавить отряд в расстановку")
        $scope.newdetachment = {
          people: []
        };
        $scope.setFieldKeys();
        $scope.$apply();

      })

      // getVkData($scope.newdetachment.people, ["domain"],
      //   function(response) {
      //     /*если передали имя ВК - заменяем на uid*/
      //     for (var i = 0; i < $scope.newdetachment.people.length; i++) {
      //       if (response[$scope.newdetachment.people[i]]) {
      //         $scope.newdetachment.people[i] = response[$scope.newdetachment.people[i]].uid;
      //       }
      //     };
      //     var new_people = [];
      //     for (var i = 0; i < $scope.newdetachment.people.length; i++) {
      //       if ($scope.newdetachment.people[i]) {
      //         new_people.push($scope.newdetachment.people[i] + "")
      //       }
      //     };
      //     /*пушим в БД, конкатинируя имена*/
      //     var data = {
      //       comments: $scope.newdetachment.comments,
      //       people: new_people.join("$"),
      //       action: "add_detachment",
      //       shift_id: shiftid,
      //       ranking: $scope.new_rank.ranking
      //     }
      //     if (_.isNumber(key)) { // если редактируем
      //       data.action = "edit_detachment";
      //       data.in_id = $scope.cached_detach.in_id;
      //     }
      //     console.log(data)
      //     $.ajax({ //TODO: make with angular
      //       type: "POST",
      //       url: "/handlers/shift.php",
      //       dataType: "json",
      //       data: $.param(data)
      //     }).done(function(json_detach) {
      //       _.each(new_people, function(person) {
      //         var per = _.find($scope.all_apply, function(pers) {
      //           return pers.user * 1 == person * 1;
      //         })
      //         if (per) {
      //           per.have_in_det = true;
      //         }
      //       })
      //       var vk_idsD = new_people
      //       getVkData(vk_idsD, ["domain", "photo_50"],
      //         function(response) {
      //           var detachment = {
      //             people: new_people,
      //             "ranking": $scope.new_rank.ranking,
      //             "comments": $scope.newdetachment.comments
      //           }
      //           _.each(detachment.people, function(person, index_p, list) {
      //             var vk_d = response[person];
      //             if (vk_d) {
      //               detachment.people[index_p] = vk_d;
      //             }
      //           })

      //           // чтобы показать и комментарии
      //           if (_.isNumber(key)) {
      //             detachment.in_id = $scope.cached_detach.in_id;
      //           } else {
      //             detachment.in_id = json_detach.in_id;
      //           }
      //           var bbdata = {
      //             bbcode: detachment.comments,
      //             ownaction: "bbcodeToHtml"
      //           };
      //           $.ajax({
      //             type: "POST",
      //             url: "/standart/markitup/sets/bbcode/parser.php",
      //             dataType: 'text',
      //             global: false,
      //             data: $.param(bbdata)
      //           }).done(function(comment_data) {
      //             detachment.bbcomments = comment_data;
      //             $("div." + detachment.in_id + "-bbcomments").html(detachment.bbcomments)
      //             $scope.$apply();
      //           });
      //           if (_.isNumber(key)) {
      //             $scope.add_det = false;
      //             $(".addDetachment").text("добавить отряд в расстановку")
      //           }
      //           $scope.detachments.push(detachment)
      //           $scope.newdetachment = {
      //             people: []
      //           };
      //           $scope.setFieldKeys();
      //           $scope.$apply();
      //         });

      //       console.log(new_people);
      //       $scope.$apply();
      //     });
      //   });
    }

    /*редактировать отряд*/
    $scope.editDetachment = function(detachment, rank) {
      $scope.newdetachment = {
        "id": detachment,
        "rank": rank,
        "people": angular.copy($scope.rankings[rank][detachment])
      }
      $scope.cached_detach = angular.copy($scope.newdetachment)
      for (var i = 0; i < $scope.newdetachment.people.length; i++) {
        $scope.newdetachment.people[i] = $scope.newdetachment.people[i].user || $scope.newdetachment.people[i].name;
      };
      $scope.newdetachment.editKey = detachment;
      delete $scope.rankings[rank][detachment]
      if ($scope.add_det) {
        $(".addDetachment").text("добавить отряд в расстановку")
      } else {
        $(".addDetachment").text("Скрыть добавление")
      }
      $scope.add_det = !$scope.add_det;

      $scope.setFieldKeys();
    }

    // удаляет расстановку
    $scope.deleteRanking = function(index) {
      if (confirm("удалить расстановку №" + index + "?")) {
        var data = {
          "action": "del_rank",
          "id": index
        };
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          console.log(json);
        });
      }
    }

    /*удаляем отряд*/
    $scope.deleteDetachment = function(detachment, rank) {
      if (confirm("удалить отряд? " + detachment)) {
        var data = {};
        data.action = "del_detach_shift";
        data.id = detachment;
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          delete $scope.rankings[rank][detachment];
          $scope.$apply();
        });
      }
    }

    /*публикуем расстановку*/
    $scope.publish = function(index) {
      console.log(index)
      if (confirm("Расстановка "+index+" станет видна всем. Опубликовать?")) {
        var data = {
          action: "publish_rank",
          "id": index,
          "shift": shiftid
        };

        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          console.log(json)
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
      $("input.vk_input").siteInput()
    });
    window.init_ang("oneShiftAppEditDetach", init_angular_o_s_c, "shift-edit-detach");
  }
  init();
  window.registerInit(init)
})();
