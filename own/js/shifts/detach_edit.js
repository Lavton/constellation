'use strict';
(function() {
  /*логика ангулара*/
  function init_angular_o_s_c($scope, $http, $locale) {
    /*инициализация*/
    $scope.window = window;
    var fid = window.location.href.split("/")
    var shiftid = fid[fid.length - 2] //TODO сделать тут нормально!
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

      $scope.$apply();
    });

    /*конец инициализации*/

    /*подтверждение, что человек найден*/
    $scope.okAddPerson = function() {
      $scope.newdetachment.people.push($scope.newdetachment.newPerson)
      $scope.newdetachment.newPerson = "";
      $scope.newdetachment.setFieldKeys();
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
      $scope.newdetachment.setFieldKeys();
      console.log($scope.newdetachment)

    }

    // var inthrefID = setInterval(function() {
    //   var fid = window.location.href.split("/")
    //   var shiftid = fid[fid.length - 2] //TODO сделать тут нормально!
    //   if (shiftid != "shifts") {
    //     clearInterval(inthrefID);

    //     /*получаем информацию о смене*/
    //     var data = {
    //       action: "get_one_info",
    //       id: shiftid,
    //       "edit": true
    //     }
    //     $scope.shift.photo_200 = "http://vk.com/images/camera_b.gif"
    //     $.ajax({
    //       type: "POST",
    //       url: "/handlers/shift.php",
    //       dataType: "json",
    //       data: $.param(data)
    //     }).done(function(json) {
    //       $scope.myself = json.myself;
    //       $scope.all_apply = json.all_apply;
    //       $scope.detachments = json.detachments;
    //       var comments = [];
    //       if (json.myself) {
    //         comments.push({
    //           id: json.myself.vk_id,
    //           comment: json.myself.comments
    //         });
    //       }
    //       _.each(json.all_apply, function(element, index, list) {
    //         comments.push({
    //           id: element.vk_id,
    //           comment: element.comments
    //         });
    //       });
    //       var bbdata = {
    //         bbcode: comments,
    //         ownaction: "bbcodesToHtml"
    //       };
    //       $.ajax({
    //         type: "POST",
    //         url: "/standart/markitup/sets/bbcode/parser.php",
    //         dataType: 'json',
    //         global: false,
    //         data: $.param(bbdata)
    //       }).done(function(rdata) {
    //         if ($scope.myself) {
    //           $scope.myself.bbcomments = _.findWhere(rdata, {
    //             id: $scope.myself.vk_id
    //           }).bbcomment;
    //         }
    //         _.each($scope.all_apply, function(element, index, list) {
    //           element.bbcomments = _.findWhere(rdata, {
    //             id: element.vk_id
    //           }).bbcomment;
    //         });
    //         _.each($scope.detachments, function(element, index, list) {
    //           element.ranking *= 1
    //         });
    //         console.log($scope.detachments)
    //         $scope.rankings = _.groupBy($scope.detachments, function(detach) {
    //           return detach.ranking;
    //         })
    //         console.log($scope.rankings)
    //         $scope.max_rank = _.chain($scope.rankings).keys().map(function(key) {
    //           return key * 1
    //         }).max().value()
    //         if (!_.isFinite($scope.max_rank)) {
    //           $scope.max_rank = 0
    //         }
    //         $scope.$apply();
    //       });

    //       $scope.detachments = json.detachments;
    //       var comments = []
    //       _.each(json.detachments, function(element, index, list) {
    //         comments.push({
    //           id: element.in_id,
    //           comment: element.comments
    //         });
    //       });
    //       var bbdata = {
    //         bbcode: comments,
    //         ownaction: "bbcodesToHtml"
    //       };
    //       $.ajax({
    //         type: "POST",
    //         url: "/standart/markitup/sets/bbcode/parser.php",
    //         dataType: 'json',
    //         global: false,
    //         data: $.param(bbdata)
    //       }).done(function(comment_data) {
    //         //формируем запрос сразу для всех нужных id для ВКонтакте
    //         var vk_idsD = []
    //         _.each($scope.detachments, function(element, index, list) {
    //           element.bbcomments = _.findWhere(comment_data, {
    //             id: element.in_id
    //           }).bbcomment;
    //           element.people = element.people.split("$");
    //           vk_idsD = vk_idsD.concat(element.people);
    //         });
    //         getVkData(vk_idsD, ["domain", "photo_50"],
    //           function(response) {
    //             _.each($scope.detachments, function(detachment, index, list) {
    //               _.each(detachment.people, function(person, index_p, list) {
    //                 var vk_d = response[person];
    //                 if (vk_d) {
    //                   detachment.people[index_p] = vk_d;
    //                 }
    //               })
    //             })
    //             $scope.$apply();
    //           });
    //         $scope.$apply();

    //       });
    //       //формируем запрос сразу для всех нужных id для ВКонтакте
    //       var vk_ids = []
    //       _.each(json.like_h, function(element, index, list) {
    //         vk_ids.push(element.vk_id);
    //       });
    //       var vk_ids = []
    //       if ($scope.myself) {
    //         vk_ids.push($scope.myself.vk_id)
    //         vk_ids.push($scope.myself.like_one)
    //         vk_ids.push($scope.myself.like_two)
    //         vk_ids.push($scope.myself.like_three)
    //         vk_ids.push($scope.myself.dislike_one)
    //         vk_ids.push($scope.myself.dislike_two)
    //         vk_ids.push($scope.myself.dislike_three)
    //       }
    //       _.each($scope.all_apply, function(element, index, list) {
    //         vk_ids.push(element.vk_id)
    //         vk_ids.push(element.like_one)
    //         vk_ids.push(element.like_two)
    //         vk_ids.push(element.like_three)
    //         vk_ids.push(element.dislike_one)
    //         vk_ids.push(element.dislike_two)
    //         vk_ids.push(element.dislike_three)
    //       })
    //       getVkData(vk_ids, ["domain", "photo_50"],
    //         function(response) {
    //           $scope.vk_info = response;
    //           // ищем тех, кому нравится данный человек
    //           $scope.adding.vk_likes = [];
    //           _.each(json.like_h, function(element, index, list) {
    //             var vk_d = response[element.vk_id];
    //             _.each(vk_d, function(element2, index, list) {
    //               element[index] = vk_d[index];
    //             })
    //             element.fighter = element.fighter_id;
    //           });
    //           $scope.adding.vk_likes = json.like_h;



    //           if ($scope.myself) {
    //             // ищем инфу для данного человека
    //             var vk_d = response[$scope.myself.vk_id];
    //             _.each(vk_d, function(element2, index, list) {
    //               $scope.myself[index] = vk_d[index];
    //             })

    //             $scope.myself.like_1 = {}
    //             var vk_d = response[$scope.myself.like_one]
    //             _.each(vk_d, function(element2, index, list) {
    //               $scope.myself.like_1[index] = vk_d[index];
    //             })
    //             $scope.myself.like_2 = {}
    //             var vk_d = response[$scope.myself.like_two];
    //             _.each(vk_d, function(element2, index, list) {
    //               $scope.myself.like_2[index] = vk_d[index];
    //             })
    //             $scope.myself.like_3 = {}
    //             var vk_d = response[$scope.myself.like_three];
    //             _.each(vk_d, function(element2, index, list) {
    //               $scope.myself.like_3[index] = vk_d[index];
    //             })

    //             $scope.myself.dislike_1 = {}
    //             var vk_d = response[$scope.myself.dislike_one];
    //             _.each(vk_d, function(element2, index, list) {
    //               $scope.myself.dislike_1[index] = vk_d[index];
    //             })
    //             $scope.myself.dislike_2 = {}
    //             var vk_d = response[$scope.myself.dislike_two];
    //             _.each(vk_d, function(element2, index, list) {
    //               $scope.myself.dislike_2[index] = vk_d[index];
    //             })
    //             $scope.myself.dislike_3 = {}
    //             var vk_d = response[$scope.myself.dislike_three];
    //             _.each(vk_d, function(element2, index, list) {
    //               $scope.myself.dislike_3[index] = vk_d[index];
    //             })
    //           }

    //           //ищем инфу для всех записавшихся людей
    //           _.each($scope.all_apply, function(app_el, index, list) {
    //             var vk_d = response[app_el.vk_id];
    //             _.each(vk_d, function(element2, index, list) {
    //               app_el[index] = vk_d[index];
    //             })

    //             app_el.like_1 = {}
    //             var vk_d = response[app_el.like_one];
    //             _.each(vk_d, function(element2, index, list) {
    //               app_el.like_1[index] = vk_d[index];
    //             })
    //             app_el.like_2 = {}
    //             var vk_d = response[app_el.like_two];
    //             _.each(vk_d, function(element2, index, list) {
    //               app_el.like_2[index] = vk_d[index];
    //             })
    //             app_el.like_3 = {}
    //             var vk_d = response[app_el.like_three];
    //             _.each(vk_d, function(element2, index, list) {
    //               app_el.like_3[index] = vk_d[index];
    //             })

    //             app_el.dislike_1 = {}
    //             var vk_d = response[app_el.dislike_one];
    //             _.each(vk_d, function(element2, index, list) {
    //               app_el.dislike_1[index] = vk_d[index];
    //             })
    //             app_el.dislike_2 = {}
    //             var vk_d = response[app_el.dislike_two];
    //             _.each(vk_d, function(element2, index, list) {
    //               app_el.dislike_2[index] = vk_d[index];
    //             })
    //             app_el.dislike_3 = {}
    //             var vk_d = response[app_el.dislike_three];
    //             _.each(vk_d, function(element2, index, list) {
    //               app_el.dislike_3[index] = vk_d[index];
    //             })
    //           })


    //           $scope.$apply();
    //         });
    //       /*конец обращения к ВК*/



    //       /*преобразование базовой инфы про смену*/
    //       $scope.shift = json.shift
    //       $scope.shift.visibility *= 1;
    //       $scope.shift.st_date = new Date($scope.shift.start_date);
    //       $scope.shift.fn_date = new Date($scope.shift.finish_date);
    //       var name = "";
    //       var st_month = $scope.shift.st_date.getMonth() * 1 + 1; //нумерация с нуля была
    //       var fn_month = $scope.shift.fn_date.getMonth() * 1 + 1;
    //       if ((st_month == 10) || (st_month == 11)) {
    //         //октябрь или ноябрь -> осень
    //         name = "Осень";
    //       } else if ((st_month == 12) || (st_month == 1)) {
    //         //декабрь или январь -> зима
    //         name = "Зима";
    //       } else if ((st_month == 3) || (st_month == 4)) {
    //         //март или апрель -> весна
    //         name = "Весна";
    //       } else {
    //         name = "Лето ";
    //         if (fn_month == 6) { //в июне кончается первая смена
    //           name += "1";
    //         } else if (st_month == 6) { //в июне начинается вторая смена (или первая, но её уже обработали)
    //           name += "2";
    //         } else if (st_month == 7) { //в июле начинается третья смена
    //           name += "3";
    //         } else { //осталась четвёртая
    //           name += "4";
    //         }
    //       }
    //       name += ", " + $scope.shift.fn_date.getFullYear()
    //       if ($scope.shift.place) {
    //         name += " (" + $scope.shift.place + ")";
    //       }
    //       $scope.shift.name = name;
    //       $scope.shift.today = new Date();

    //       $("a.shift_priv").attr("href", json.prev.mid)
    //       $("a.shift_next").attr("href", json.next.mid)
    //       if (!json.prev.mid) {
    //         $("a.shift_priv").hide();
    //       }

    //       if (!json.next.mid) {
    //         $("a.shift_next").hide();
    //       }

    //       $.getJSON("/own/group_names.json", function(group_json) {
    //         $scope.groups = group_json;
    //         $scope.$apply();
    //       });

    //       var bbdata = {
    //         bbcode: $scope.shift.comments,
    //         ownaction: "bbcodeToHtml"
    //       };
    //       $.ajax({
    //         type: "POST",
    //         url: "/standart/markitup/sets/bbcode/parser.php",
    //         dataType: 'text',
    //         global: false,
    //         data: $.param(bbdata)
    //       }).done(function(rdata) {
    //         $scope.shift.bbcomments = rdata,
    //           $scope.$apply();
    //       });
    //       //TODO make works all html. (jquery?)
    //       $scope.$apply();
    //     });
    //   }
    // }, 100);

    // говорит, что создаём новую расстановку
    $scope.newRanking = function(flag) {
      $scope.new_rank = {}
      $scope.new_rank.ranking = $scope.max_rank + 1
      $scope.max_rank += 1;
      $scope.new_rank.edit = false;
    }


    /*обновляет значение ключей*/
    $scope.newdetachment.setFieldKeys = function() {
      var keys = [];
      for (var i = ($scope.newdetachment.people).length - 1; i >= 0; i--) {
        keys.push(i);
      };
      $scope.newdetachment.fieldKeys = keys;
    }
    $scope.newdetachment.setFieldKeys();

    $scope.editRanking = function(index) {
      $scope.new_rank = {}
      $scope.new_rank.ranking = index
      $scope.new_rank.edit = true;
    }

    $scope.addDetachment = function() {
      if ($scope.add_det) {
        $(".addDetachment").text("добавить отряд в расстановку")
        window.location.href = window.location.href;
      } else {
        $(".addDetachment").text("Скрыть добавление")
      }
      $scope.add_det = !$scope.add_det;
    }

    /*создаёт расстановку*/
    $scope.addDetachmentSubmit = function() {
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
            shift_id: $scope.shift.id,
            ranking: $scope.new_rank.ranking
          }
          $.ajax({ //TODO: make with angular
            type: "POST",
            url: "/handlers/shift.php",
            dataType: "json",
            data: $.param(data)
          }).done(function(json) {
            var vk_idsD = new_people
            getVkData(vk_idsD, ["domain", "photo_50"],
              function(response) {
                var detachment = {
                  people: new_people,
                  "ranking": $scope.new_rank.ranking
                }
                _.each(detachment.people, function(person, index_p, list) {
                  var vk_d = response[person];
                  if (vk_d) {
                    detachment.people[index_p] = vk_d;
                  }
                })
                $scope.detachments.push(detachment)
                $scope.newdetachment.people = ["", ];
                $scope.newdetachment.comments = "";
                $scope.newdetachment.fieldKeys = [];
                $scope.newdetachment.setFieldKeys();
                $scope.$apply();
              });

            console.log(new_people);
            $scope.$apply();
          });
        });
    }

    $scope.editDetachment = function(index, key) {
      $scope.edit_detachment = $scope.rankings[index][key];
    }

    $scope.saveDetachComment = function() {
      if (confirm("редактировать комментарий?")) {
        var data = {};
        data.action = "edit_detach_comment";
        data.in_id = $scope.edit_detachment.in_id;
        data.comments = $scope.edit_detachment.comments;
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
    $scope.deleteDetachment = function(index, key) {
      if (confirm("удалить отряд?")) {
        var data = {};
        data.action = "del_detach_shift";
        data.in_id = $scope.rankings[index][key].in_id;
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
    window.init_ang("oneShiftAppEditDetach", init_angular_o_s_c, "shift-edit-detach");
  }
  init();
  window.registerInit(init)
})();
