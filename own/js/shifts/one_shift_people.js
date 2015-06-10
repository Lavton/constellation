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
        _.each(json.all_apply, function(person, id, list) {
          window.getPerson(person.vk_id * 1, function(pers, flag) {
            var set_f = _.after(7, function() {
              if (pers.isFighter == true) {
                $scope.fighters.push(person)
              } else {
                $scope.candidats.push(person)
              }

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
          var bbdata = { // что-то не работает((
            bbcode: [json.myself.comments],
            ownaction: "bbcodesToHtml"
          };
          $.ajax({
            type: "POST",
            url: "/standart/markitup/sets/bbcode/parser.php",
            dataType: 'json',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            $scope.me.bbcomments = rdata;
            console.log("catch")
            console.log(rdata)
            $scope.$apply();
          })
        }

        $scope.all_apply = json.all_apply;
        var comments = [];
        if (json.myself) {
          comments.push({
            id: json.myself.vk_id,
            comment: json.myself.comments
          });
        }
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
          if ($scope.myself) {
            $scope.myself.bbcomments = _.findWhere(rdata, {
              id: $scope.myself.vk_id
            }).bbcomment;
          }
          _.each($scope.all_apply, function(element, index, list) {
            element.bbcomments = _.findWhere(rdata, {
              id: element.vk_id
            }).bbcomment;
          });
          $scope.$apply();
        });

        //формируем запрос сразу для всех нужных id для ВКонтакте
        // var vk_ids = []
        // _.each(json.like_h, function(element, index, list) {
        //   vk_ids.push(element.vk_id);
        // });
        // var vk_ids = []
        // if ($scope.myself) {
        //   vk_ids.push($scope.myself.vk_id)
        //   vk_ids.push($scope.myself.like_one)
        //   vk_ids.push($scope.myself.like_two)
        //   vk_ids.push($scope.myself.like_three)
        //   vk_ids.push($scope.myself.dislike_one)
        //   vk_ids.push($scope.myself.dislike_two)
        //   vk_ids.push($scope.myself.dislike_three)
        // }
        // _.each($scope.all_apply, function(element, index, list) {
        //   vk_ids.push(element.vk_id)
        //   vk_ids.push(element.like_one)
        //   vk_ids.push(element.like_two)
        //   vk_ids.push(element.like_three)
        //   vk_ids.push(element.dislike_one)
        //   vk_ids.push(element.dislike_two)
        //   vk_ids.push(element.dislike_three)
        // })
        // getVkData(vk_ids, ["domain", "photo_50"],
        //   function(response) {
        //     $scope.vk_info = response;
        //     // ищем тех, кому нравится данный человек
        //     $scope.adding.vk_likes = [];
        //     _.each(json.like_h, function(element, index, list) {
        //       var vk_d = response[element.vk_id];
        //       _.each(vk_d, function(element2, index, list) {
        //         element[index] = vk_d[index];
        //       })
        //       element.fighter = element.fighter_id;
        //     });
        //     $scope.adding.vk_likes = json.like_h;


        //     if ($scope.myself) {
        //       // ищем инфу для данного человека
        //       var vk_d = response[$scope.myself.vk_id];
        //       _.each(vk_d, function(element2, index, list) {
        //         $scope.myself[index] = vk_d[index];
        //       })

        //       $scope.myself.like_1 = {}
        //       var vk_d = response[$scope.myself.like_one]
        //       _.each(vk_d, function(element2, index, list) {
        //         $scope.myself.like_1[index] = vk_d[index];
        //       })
        //       $scope.myself.like_2 = {}
        //       var vk_d = response[$scope.myself.like_two];
        //       _.each(vk_d, function(element2, index, list) {
        //         $scope.myself.like_2[index] = vk_d[index];
        //       })
        //       $scope.myself.like_3 = {}
        //       var vk_d = response[$scope.myself.like_three];
        //       _.each(vk_d, function(element2, index, list) {
        //         $scope.myself.like_3[index] = vk_d[index];
        //       })

        //       $scope.myself.dislike_1 = {}
        //       var vk_d = response[$scope.myself.dislike_one];
        //       _.each(vk_d, function(element2, index, list) {
        //         $scope.myself.dislike_1[index] = vk_d[index];
        //       })
        //       $scope.myself.dislike_2 = {}
        //       var vk_d = response[$scope.myself.dislike_two];
        //       _.each(vk_d, function(element2, index, list) {
        //         $scope.myself.dislike_2[index] = vk_d[index];
        //       })
        //       $scope.myself.dislike_3 = {}
        //       var vk_d = response[$scope.myself.dislike_three];
        //       _.each(vk_d, function(element2, index, list) {
        //         $scope.myself.dislike_3[index] = vk_d[index];
        //       })
        //     }

        //     //ищем инфу для всех записавшихся людей
        //     _.each($scope.all_apply, function(app_el, index, list) {
        //       var vk_d = response[app_el.vk_id];
        //       _.each(vk_d, function(element2, index, list) {
        //         app_el[index] = vk_d[index];
        //       })

        //       app_el.like_1 = {}
        //       var vk_d = response[app_el.like_one];
        //       _.each(vk_d, function(element2, index, list) {
        //         app_el.like_1[index] = vk_d[index];
        //       })
        //       app_el.like_2 = {}
        //       var vk_d = response[app_el.like_two];
        //       _.each(vk_d, function(element2, index, list) {
        //         app_el.like_2[index] = vk_d[index];
        //       })
        //       app_el.like_3 = {}
        //       var vk_d = response[app_el.like_three];
        //       _.each(vk_d, function(element2, index, list) {
        //         app_el.like_3[index] = vk_d[index];
        //       })

        //       app_el.dislike_1 = {}
        //       var vk_d = response[app_el.dislike_one];
        //       _.each(vk_d, function(element2, index, list) {
        //         app_el.dislike_1[index] = vk_d[index];
        //       })
        //       app_el.dislike_2 = {}
        //       var vk_d = response[app_el.dislike_two];
        //       _.each(vk_d, function(element2, index, list) {
        //         app_el.dislike_2[index] = vk_d[index];
        //       })
        //       app_el.dislike_3 = {}
        //       var vk_d = response[app_el.dislike_three];
        //       _.each(vk_d, function(element2, index, list) {
        //         app_el.dislike_3[index] = vk_d[index];
        //       })
        //     })


        //     $scope.$apply();
        //   });
        // /*конец обращения к ВК*/


        // var bbdata = {
        //   bbcode: $scope.shift.comments,
        //   ownaction: "bbcodeToHtml"
        // };
        // $.ajax({
        //   type: "POST",
        //   url: "/standart/markitup/sets/bbcode/parser.php",
        //   dataType: 'text',
        //   global: false,
        //   data: $.param(bbdata)
        // }).done(function(rdata) {
        //   $scope.shift.bbcomments = rdata,
        //     $scope.$apply();
        // });
        // //TODO make works all html. (jquery?)
        // $scope.$apply();
      })
    }

    var inthrefID = setInterval(function() {
      var fid = window.location.href.split("/")
      var shiftid = fid[fid.length - 1] //TODO сделать тут нормально!
      if (shiftid != "shifts") {
        clearInterval(inthrefID);

        /*получаем информацию о смене*/
        var data = {
          action: "get_one_info",
          id: shiftid
        }
        $scope.shift.photo_200 = "http://vk.com/images/camera_b.gif"
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          // $scope.myself = json.myself;
          // $scope.all_apply = json.all_apply;
          // $scope.detachments = json.detachments;
          // var comments = [];
          // if (json.myself) {
          //   comments.push({
          //     id: json.myself.vk_id,
          //     comment: json.myself.comments
          //   });
          // }
          // _.each(json.all_apply, function(element, index, list) {
          //   comments.push({
          //     id: element.vk_id,
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
          // }).done(function(rdata) {
          //   if ($scope.myself) {
          //     $scope.myself.bbcomments = _.findWhere(rdata, {
          //       id: $scope.myself.vk_id
          //     }).bbcomment;
          //   }
          //   _.each($scope.all_apply, function(element, index, list) {
          //     element.bbcomments = _.findWhere(rdata, {
          //       id: element.vk_id
          //     }).bbcomment;
          //   });
          //   $scope.$apply();
          // });

          // $scope.detachments = json.detachments;
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
          //   //формируем запрос сразу для всех нужных id для ВКонтакте
          //   var vk_idsD = []
          //   _.each($scope.detachments, function(element, index, list) {
          //     element.bbcomments = _.findWhere(comment_data, {
          //       id: element.in_id
          //     }).bbcomment;
          //     element.people = element.people.split("$");
          //     vk_idsD = vk_idsD.concat(element.people);
          //   });
          //   getVkData(vk_idsD, ["domain", "photo_50"],
          //     function(response) {
          //       _.each($scope.detachments, function(detachment, index, list) {
          //         _.each(detachment.people, function(person, index_p, list) {
          //           var vk_d = response[person];
          //           if (vk_d) {
          //             detachment.people[index_p] = vk_d;
          //           }
          //         })
          //       })
          //       $scope.$apply();
          //     });
          //   $scope.$apply();

          // });
          // //формируем запрос сразу для всех нужных id для ВКонтакте
          // var vk_ids = []
          // _.each(json.like_h, function(element, index, list) {
          //   vk_ids.push(element.vk_id);
          // });
          // var vk_ids = []
          // if ($scope.myself) {
          //   vk_ids.push($scope.myself.vk_id)
          //   vk_ids.push($scope.myself.like_one)
          //   vk_ids.push($scope.myself.like_two)
          //   vk_ids.push($scope.myself.like_three)
          //   vk_ids.push($scope.myself.dislike_one)
          //   vk_ids.push($scope.myself.dislike_two)
          //   vk_ids.push($scope.myself.dislike_three)
          // }
          // _.each($scope.all_apply, function(element, index, list) {
          //   vk_ids.push(element.vk_id)
          //   vk_ids.push(element.like_one)
          //   vk_ids.push(element.like_two)
          //   vk_ids.push(element.like_three)
          //   vk_ids.push(element.dislike_one)
          //   vk_ids.push(element.dislike_two)
          //   vk_ids.push(element.dislike_three)
          // })
          // getVkData(vk_ids, ["domain", "photo_50"],
          //   function(response) {
          //     $scope.vk_info = response;
          //     // ищем тех, кому нравится данный человек
          //     $scope.adding.vk_likes = [];
          //     _.each(json.like_h, function(element, index, list) {
          //       var vk_d = response[element.vk_id];
          //       _.each(vk_d, function(element2, index, list) {
          //         element[index] = vk_d[index];
          //       })
          //       element.fighter = element.fighter_id;
          //     });
          //     $scope.adding.vk_likes = json.like_h;



          //     if ($scope.myself) {
          //       // ищем инфу для данного человека
          //       var vk_d = response[$scope.myself.vk_id];
          //       _.each(vk_d, function(element2, index, list) {
          //         $scope.myself[index] = vk_d[index];
          //       })

          //       $scope.myself.like_1 = {}
          //       var vk_d = response[$scope.myself.like_one]
          //       _.each(vk_d, function(element2, index, list) {
          //         $scope.myself.like_1[index] = vk_d[index];
          //       })
          //       $scope.myself.like_2 = {}
          //       var vk_d = response[$scope.myself.like_two];
          //       _.each(vk_d, function(element2, index, list) {
          //         $scope.myself.like_2[index] = vk_d[index];
          //       })
          //       $scope.myself.like_3 = {}
          //       var vk_d = response[$scope.myself.like_three];
          //       _.each(vk_d, function(element2, index, list) {
          //         $scope.myself.like_3[index] = vk_d[index];
          //       })

          //       $scope.myself.dislike_1 = {}
          //       var vk_d = response[$scope.myself.dislike_one];
          //       _.each(vk_d, function(element2, index, list) {
          //         $scope.myself.dislike_1[index] = vk_d[index];
          //       })
          //       $scope.myself.dislike_2 = {}
          //       var vk_d = response[$scope.myself.dislike_two];
          //       _.each(vk_d, function(element2, index, list) {
          //         $scope.myself.dislike_2[index] = vk_d[index];
          //       })
          //       $scope.myself.dislike_3 = {}
          //       var vk_d = response[$scope.myself.dislike_three];
          //       _.each(vk_d, function(element2, index, list) {
          //         $scope.myself.dislike_3[index] = vk_d[index];
          //       })
          //     }

          //     //ищем инфу для всех записавшихся людей
          //     _.each($scope.all_apply, function(app_el, index, list) {
          //       var vk_d = response[app_el.vk_id];
          //       _.each(vk_d, function(element2, index, list) {
          //         app_el[index] = vk_d[index];
          //       })

          //       app_el.like_1 = {}
          //       var vk_d = response[app_el.like_one];
          //       _.each(vk_d, function(element2, index, list) {
          //         app_el.like_1[index] = vk_d[index];
          //       })
          //       app_el.like_2 = {}
          //       var vk_d = response[app_el.like_two];
          //       _.each(vk_d, function(element2, index, list) {
          //         app_el.like_2[index] = vk_d[index];
          //       })
          //       app_el.like_3 = {}
          //       var vk_d = response[app_el.like_three];
          //       _.each(vk_d, function(element2, index, list) {
          //         app_el.like_3[index] = vk_d[index];
          //       })

          //       app_el.dislike_1 = {}
          //       var vk_d = response[app_el.dislike_one];
          //       _.each(vk_d, function(element2, index, list) {
          //         app_el.dislike_1[index] = vk_d[index];
          //       })
          //       app_el.dislike_2 = {}
          //       var vk_d = response[app_el.dislike_two];
          //       _.each(vk_d, function(element2, index, list) {
          //         app_el.dislike_2[index] = vk_d[index];
          //       })
          //       app_el.dislike_3 = {}
          //       var vk_d = response[app_el.dislike_three];
          //       _.each(vk_d, function(element2, index, list) {
          //         app_el.dislike_3[index] = vk_d[index];
          //       })
          //     })


          //     $scope.$apply();
          //   });
          // /*конец обращения к ВК*/



          // /*преобразование базовой инфы про смену*/
          // $scope.shift = json.shift
          // $scope.shift.visibility *= 1;
          // $scope.shift.st_date = new Date($scope.shift.start_date);
          // $scope.shift.fn_date = new Date($scope.shift.finish_date);
          // var name = "";
          // var st_month = $scope.shift.st_date.getMonth() * 1 + 1; //нумерация с нуля была
          // var fn_month = $scope.shift.fn_date.getMonth() * 1 + 1;
          // if ((st_month == 10) || (st_month == 11)) {
          //   //октябрь или ноябрь -> осень
          //   name = "Осень";
          // } else if ((st_month == 12) || (st_month == 1)) {
          //   //декабрь или январь -> зима
          //   name = "Зима";
          // } else if ((st_month == 3) || (st_month == 4)) {
          //   //март или апрель -> весна
          //   name = "Весна";
          // } else {
          //   name = "Лето ";
          //   if (fn_month == 6) { //в июне кончается первая смена
          //     name += "1";
          //   } else if (st_month == 6) { //в июне начинается вторая смена (или первая, но её уже обработали)
          //     name += "2";
          //   } else if (st_month == 7) { //в июле начинается третья смена
          //     name += "3";
          //   } else { //осталась четвёртая
          //     name += "4";
          //   }
          // }
          // name += ", " + $scope.shift.fn_date.getFullYear()
          // if ($scope.shift.place) {
          //   name += " (" + $scope.shift.place + ")";
          // }
          // $scope.shift.name = name;
          // $scope.shift.today = new Date();

          // $("a.shift_priv").attr("href", json.prev.mid)
          // $("a.shift_next").attr("href", json.next.mid)
          // if (!json.prev.mid) {
          //   $("a.shift_priv").hide();
          // }

          // if (!json.next.mid) {
          //   $("a.shift_next").hide();
          // }

          // $.getJSON("/own/group_names.json", function(group_json) {
          //   $scope.groups = group_json;
          //   $scope.$apply();
          // });

          // var bbdata = {
          //   bbcode: $scope.shift.comments,
          //   ownaction: "bbcodeToHtml"
          // };
          // $.ajax({
          //   type: "POST",
          //   url: "/standart/markitup/sets/bbcode/parser.php",
          //   dataType: 'text',
          //   global: false,
          //   data: $.param(bbdata)
          // }).done(function(rdata) {
          //   $scope.shift.bbcomments = rdata,
          //     $scope.$apply();
          // });
          // //TODO make works all html. (jquery?)
          // $scope.$apply();
        });
      }
    }, 100);


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
