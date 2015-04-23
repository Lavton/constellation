'use strict';
function get_shift(shiftid) {
  if (window.shifts == undefined) {
    window.shifts = {}
  }
  window.shifts.one_angular_conroller = null;
  var fid=window.location.href.split("/")
  var shiftid=fid[fid.length-1] //TODO сделать тут нормально!

  if (! window.shifts.one_script ) {
    window.shifts.one_script = true;
  var intID = setInterval(function(){
      var fid=window.location.href.split("/")
      var shiftid=fid[fid.length-1] //TODO сделать тут нормально!
      if ((typeof(angular) !== "undefined") && (shiftid != "shifts")) {
        if (window.shifts.one_angular_conroller == null) {
          window.shifts.one_angular_conroller = angular.module('one_sc_app', ["ngSanitize"], function($httpProvider) { //магия, чтобы PHP понимал запрос
            // Используем x-www-form-urlencoded Content-Type
            $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
            // Переопределяем дефолтный transformRequest в $http-сервисе
            $httpProvider.defaults.transformRequest = [function(data) {
              var param = function(obj) {
                var query = '';
                var name, value, fullSubName, subValue, innerObj, i;
                for(name in obj) {
                  value = obj[name];
                  if(value instanceof Array) {
                    for(i=0; i<value.length; ++i) {
                      subValue = value[i];
                      fullSubName = name + '[' + i + ']';
                      innerObj = {};
                      innerObj[fullSubName] = subValue;
                      query += param(innerObj) + '&';
                    }
                  } else if(value instanceof Object) {
                    for(subName in value) {
                      subValue = value[subName];
                      fullSubName = name + '[' + subName + ']';
                      innerObj = {};
                      innerObj[fullSubName] = subValue;
                      query += param(innerObj) + '&';
                    }
                  } else if(value !== undefined && value !== null) {
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
                  }
                }              
                return query.length ? query.substr(0, query.length - 1) : query;
              };
              return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
            }];
          });
          //запускаем ангулар
          window.shifts.one_angular_conroller.controller('oneShiftApp', ['$scope', '$http', '$locale', init_angular_o_s_c]);
          angular.bootstrap(document, ['one_sc_app']);
          window.shifts.was_init_one = true;
        
        } else {
          angular.bootstrap(document, ['one_sc_app']);
        }
        clearInterval(intID);
    }
  }, 50);
  } else {
     angular.bootstrap(document, ['one_sc_app']);
  }
  /*логика ангулара*/
  function init_angular_o_s_c ($scope, $http, $locale) {
    $scope.id = shiftid;
    $scope.shift = {};
    $scope.adding = {};
    $scope.adding.vk_likes = {};


    $scope.editShiftInfo = function() {
      $(".shift-info").toggleClass("hidden");
      $(".shift-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.shift); 
    };

    $scope.deleteGuess = function(delid) {
      if (confirm("удалить заявку?")) {
        var data = {};
        data.action = "del_from_shift";
        data.shift_id = $scope.shift.id;
        if (delid) {
          data.vk_id = delid;
        }
        _.each(data, function(element, index, list){
          if (!element) {
            data[index] = null;
          }
        })
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(json) {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        });
      }
    }

    $scope.editGuess = function(who, is_smbdy) {
      if ($scope.show_add) {
        $scope.tableToAdd();
      }
      $scope.show_edit = true;
      var paste_data =  angular.copy(who);
      if (is_smbdy) {
        $scope.adding.smbdy=paste_data.domain;
      }
      $scope.adding.prob = paste_data.probability;
      $scope.adding.soc = paste_data.social > 1;
      $scope.adding.nonsoc = paste_data.social%2 ? true : false;
      $scope.adding.prof = paste_data.profile > 1;
      $scope.adding.nonprof = paste_data.profile%2 ? true : false;
      $scope.adding.min_age = paste_data.min_age;
      $scope.adding.max_age = paste_data.max_age;
      $scope.adding.like1 = paste_data.like_1.domain;
      $scope.adding.like2 = paste_data.like_2.domain;
      $scope.adding.like3 = paste_data.like_3.domain;
      $scope.adding.dislike1 = paste_data.dislike_1.domain;
      $scope.adding.dislike2 = paste_data.dislike_2.domain;
      $scope.adding.dislike3 = paste_data.dislike_3.domain;
      $scope.adding.comments = paste_data.comments;
    }

    $scope.tableToAdd = function() {
      if ($scope.show_add){
        $(".show_button").text("Записаться на смену")
      } else {
        $(".show_button").text("Скрыть запись")
      }
      $scope.show_add = !$scope.show_add;
      $scope.show_edit = false;
    }
      $(".shift-info").removeClass("hidden")
      var inthrefID = setInterval(function(){
      var fid=window.location.href.split("/")
      var shiftid=fid[fid.length-1] //TODO сделать тут нормально!
      if (shiftid != "shifts") {
        clearInterval(inthrefID);
        var data = {action: "get_one_info", id: shiftid}
        $scope.shift.photo_200 = "http://vk.com/images/camera_b.gif"
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(json) {
          $scope.myself = json.myself;
          $scope.all_apply = json.all_apply;
          $scope.detachments = json.detachments;
          var comments = [];
          if (json.myself) {
            comments.push({id: json.myself.vk_id, comment: json.myself.comments});
          }
          _.each(json.all_apply, function(element, index, list) {
            comments.push({id: element.vk_id, comment: element.comments});
          });
          var bbdata =  {bbcode: comments, ownaction: "bbcodesToHtml"};
          $.ajax({
            type: "POST",
            url: "/markitup/sets/bbcode/parser.php",
            dataType: 'json',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            if ($scope.myself) {
              $scope.myself.bbcomments = _.findWhere(rdata, {id: $scope.myself.vk_id}).bbcomment;
            }
            _.each($scope.all_apply, function(element, index, list) {
              element.bbcomments = _.findWhere(rdata, {id: element.vk_id}).bbcomment;
            });
            $scope.$apply();
          });

          $scope.detachments = json.detachments;
          var comments = []
          _.each(json.detachments, function(element, index, list) {
            comments.push({id: element.in_id, comment: element.comments});
          });
          //формируем запрос сразу для всех нужных id
          var vk_ids = []
          var bbdata =  {bbcode: comments, ownaction: "bbcodesToHtml"};
          $.ajax({
            type: "POST",
            url: "/markitup/sets/bbcode/parser.php",
            dataType: 'json',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            _.each($scope.detachments, function(element, index, list) {
              element.bbcomments = _.findWhere(rdata, {id: element.in_id}).bbcomment;
              element.people = element.people.split("$");
              vk_ids.concat(element.people);
            });
            $scope.$apply();
          });

          _.each(json.like_h, function(element, index, list) {
            vk_ids.push(element.vk_id);
          });
          if ($scope.myself) {
            vk_ids.push($scope.myself.vk_id)
            vk_ids.push($scope.myself.like_one)
            vk_ids.push($scope.myself.like_two)
            vk_ids.push($scope.myself.like_three)
            vk_ids.push($scope.myself.dislike_one)
            vk_ids.push($scope.myself.dislike_two)
            vk_ids.push($scope.myself.dislike_three)
          }
          _.each($scope.all_apply, function(element, index, list){
            vk_ids.push(element.vk_id)
            vk_ids.push(element.like_one)
            vk_ids.push(element.like_two)
            vk_ids.push(element.like_three)
            vk_ids.push(element.dislike_one)
            vk_ids.push(element.dislike_two)
            vk_ids.push(element.dislike_three)            
          })

          var data_vk = {user_ids: _.unique(vk_ids), fields: ["domain", "photo_50"]}
          $.ajax({
            type: "GET",
            url: "https://api.vk.com/method/users.get",
            dataType: "jsonp",
            data:  $.param(data_vk)
          }).done(function(response) {
            $scope.vk_info = response.response;
            // ищем тех, кому нравится данный человек
            $scope.adding.vk_likes = [];
            _.each(json.like_h, function(element, index, list) {
              var vk_d = _.findWhere($scope.vk_info, {uid: element.vk_id*1});
              _.each(vk_d, function(element2, index, list){
                element[index] = vk_d[index];
              })
              element.fighter = element.fighter_id;
            });
            $scope.adding.vk_likes = json.like_h;

            _.each($scope.detachments, function(detachment, index, list){
              _.each(detachment.people, function(person, index_p, list){
                var vk_d = _.findWhere($scope.vk_info, {uid: person*1});
                if (vk_d) {
                  detachment.people[index_p] = vk_d;
                }
              })

              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.vk_id*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself[index] = vk_d[index];
              })
              $scope.myself[index] = vk_d[index];
            })

            if ($scope.myself) {
              // ищем инфу для данного человека
              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.vk_id*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself[index] = vk_d[index];
              })

              $scope.myself.like_1 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.like_one*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself.like_1[index] = vk_d[index];
              })
              $scope.myself.like_2 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.like_two*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself.like_2[index] = vk_d[index];
              })
              $scope.myself.like_3 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.like_three*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself.like_3[index] = vk_d[index];
              })

              $scope.myself.dislike_1 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.dislike_one*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself.dislike_1[index] = vk_d[index];
              })
              $scope.myself.dislike_2 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.dislike_two*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself.dislike_2[index] = vk_d[index];
              })
              $scope.myself.dislike_3 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: $scope.myself.dislike_three*1});
              _.each(vk_d, function(element2, index, list){
                $scope.myself.dislike_3[index] = vk_d[index];
              })
            }

            //ищем инфу для всех записавшихся людей
            _.each($scope.all_apply, function(app_el, index, list){
              var vk_d = _.findWhere($scope.vk_info, {uid: app_el.vk_id*1});
              _.each(vk_d, function(element2, index, list){
                app_el[index] = vk_d[index];
              })

              app_el.like_1 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: app_el.like_one*1});
              _.each(vk_d, function(element2, index, list){
                app_el.like_1[index] = vk_d[index];
              })
              app_el.like_2 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: app_el.like_two*1});
              _.each(vk_d, function(element2, index, list){
                app_el.like_2[index] = vk_d[index];
              })
              app_el.like_3 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: app_el.like_three*1});
              _.each(vk_d, function(element2, index, list){
                app_el.like_3[index] = vk_d[index];
              })

              app_el.dislike_1 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: app_el.dislike_one*1});
              _.each(vk_d, function(element2, index, list){
                app_el.dislike_1[index] = vk_d[index];
              })
              app_el.dislike_2 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: app_el.dislike_two*1});
              _.each(vk_d, function(element2, index, list){
                app_el.dislike_2[index] = vk_d[index];
              })
              app_el.dislike_3 = {}
              var vk_d = _.findWhere($scope.vk_info, {uid: app_el.dislike_three*1});
              _.each(vk_d, function(element2, index, list){
                app_el.dislike_3[index] = vk_d[index];
              })
            })


            $scope.$apply();
          });


          $scope.shift = json.shift
          $scope.shift.visibility *= 1;
          $scope.shift.st_date = new Date($scope.shift.start_date);
          $scope.shift.fn_date = new Date($scope.shift.finish_date);
          var name = "";
          var st_month = $scope.shift.st_date.getMonth()*1 + 1;//нумерация с нуля была
          var fn_month = $scope.shift.fn_date.getMonth()*1 + 1;
          if ((st_month == 10) || (st_month == 11)) {
          //октябрь или ноябрь -> осень
            name = "Осень";
          } else if ((st_month == 12) || (st_month == 1)) { 
          //декабрь или январь -> зима
            name = "Зима";
          } else if ((st_month == 3) || (st_month == 4)) { 
          //март или апрель -> весна
            name = "Весна";
          } else {
            name = "Лето ";
            if (fn_month == 6) { //в июне кончается первая смена
              name += "1";
            } else if (st_month == 6) { //в июне начинается вторая смена (или первая, но её уже обработали)
              name += "2";
            } else if (st_month == 7) { //в июле начинается третья смена
              name += "3";
            } else { //осталась четвёртая
              name += "4";
            }
          }
          name += ", " + $scope.shift.fn_date.getFullYear()
          if ($scope.shift.place) {
            name += " (" + $scope.shift.place + ")";
          }
          $scope.shift.name = name;
          $scope.shift.today = new Date();

          $("a.shift_priv").attr("href", json.prev.mid)
          $("a.shift_next").attr("href", json.next.mid)
          if (!json.prev.mid) {
            $("a.shift_priv").hide();
          }

          if (!json.next.mid) {
            $("a.shift_next").hide();
          }

          $.getJSON("/own/group_names.json", function(group_json){
            $scope.groups = group_json;
            $scope.$apply();
          });

          var bbdata =  {bbcode: $scope.shift.comments, ownaction: "bbcodeToHtml"};
          $.ajax({
            type: "POST",
            url: "/markitup/sets/bbcode/parser.php",
            dataType: 'text',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            $scope.shift.bbcomments = rdata,
            $scope.$apply();
          });
          //TODO make works all html. (jquery?)
          $scope.$apply();
        });
      }
    }, 100);


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
        _.each(data, function(element, index, list){
          if (!element) {
            data[index] = null;
          }
        })
        data.shift_id = $scope.shift.id;
        data.social = data.soc*1+data.nonsoc*2;
        data.profile = data.prof*1+data.nonprof*2;
        var data_vk = {user_ids: [data.smbdy, data.like1, data.like2, data.like3, data.dislike1, data.dislike2, data.dislike3], fields: ["domain"]}
        $.ajax({
          type: "GET",
          url: "https://api.vk.com/method/users.get",
          dataType: "jsonp",
          data:  $.param(data_vk)
        }).done(function(response) {
          if(data.smbdy) {
            data.vk_id = _.findWhere(response.response, {domain: data.smbdy}).uid
          }
          if(data.like1) {
            data.like_one = _.findWhere(response.response, {domain: data.like1}).uid
          }
          if(data.like2) {
            data.like_two = _.findWhere(response.response, {domain: data.like2}).uid
          }
          if(data.like3) {
            data.like_three = _.findWhere(response.response, {domain: data.like3}).uid
          }

          if(data.dislike1) {
            data.dislike_one = _.findWhere(response.response, {domain: data.dislike1}).uid
          }
          if(data.dislike2) {
            data.dislike_two = _.findWhere(response.response, {domain: data.dislike2}).uid
          }
          if(data.dislike3) {
            data.dislike_three = _.findWhere(response.response, {domain: data.dislike3}).uid
          }
          $.ajax({
            type: "POST",
            url: "/handlers/shift.php",
            dataType: "json",
            data:  $.param(data)
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

    $scope.killappsShift = function() {
      if (confirm("Точно удалить все заявки на поездку? (сама смена не удалиться)")) {
        var data = {};
        data.action = "del_from_shift";
        data.shift_id = $scope.shift.id;
        _.each(data, function(element, index, list){
          if (!element) {
            data[index] = null;
          }
        })
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(json) {
        });
        _.each($scope.all_apply, function(element, index, list){
          var data = {};
          data.action = "del_from_shift";
          data.shift_id = $scope.shift.id;
          data.vk_id = element.vk_id;
          _.each(data, function(element, index, list){
            if (!element) {
              data[index] = null;
            }
          })
          $.ajax({
            type: "POST",
            url: "/handlers/shift.php",
            dataType: "json",
            data:  $.param(data)
          }).done(function(json) {
          });
        })
      }
       var lnk = document.createElement("a");
       lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          
          $("#page-container").append(lnk);
          $(lnk).trigger("click");
    }

    $scope.submit = function() {
          $scope.shift.st_date = new Date($scope.shift.start_date);
          $scope.shift.fn_date = new Date($scope.shift.finish_date);
          var name = "";
          var st_month = $scope.shift.st_date.getMonth()*1 + 1;//нумерация с нуля была
          var fn_month = $scope.shift.fn_date.getMonth()*1 + 1;
          if ((st_month == 10) || (st_month == 11)) {
          //октябрь или ноябрь -> осень
            name = "Осень";
          } else if ((st_month == 12) || (st_month == 1)) { 
          //декабрь или январь -> зима
            name = "Зима";
          } else if ((st_month == 3) || (st_month == 4)) { 
          //март или апрель -> весна
            name = "Весна";
          } else {
            name = "Лето ";
            if (fn_month == 6) { //в июне кончается первая смена
              name += "1";
            } else if (st_month == 6) { //в июне начинается вторая смена (или первая, но её уже обработали)
              name += "2";
            } else if (st_month == 7) { //в июле начинается третья смена
              name += "3";
            } else { //осталась четвёртая
              name += "4";
            }
          }
          name += ", " + $scope.shift.fn_date.getFullYear()
          if ($scope.shift.place) {
            name += " (" + $scope.shift.place + ")";
          }
          $scope.shift.name = name;

      var data =  angular.copy($scope.shift);
      data.action = "set_new_data"
      _.each(data, function(element, index, list){
        if (!element) {
          data[index] = null;
        }
      })

          var bbdata =  {bbcode: $scope.shift.comments, ownaction: "bbcodeToHtml"};
          $.ajax({
            type: "POST",
            url: "/markitup/sets/bbcode/parser.php",
            dataType: 'text',
            global: false,
            data: $.param(bbdata)
          }).done(function(rdata) {
            $scope.shift.bbcomments = rdata,
            $scope.$apply();
          });


      $http.post('/handlers/shift.php', data).success(function(response) {
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });      
    }

    $scope.detachment = [];
    $scope.newdetachment = {
      people: ["", ],
      comments: ""

    };
    $scope.newdetachment.fieldKeys = [];

    $scope.newdetachment.setFieldKeys = function() {
        var keys = [];
        for (var i = ($scope.newdetachment.people).length - 1; i >= 0; i--) {
          keys.push(i);
        };
        $scope.newdetachment.fieldKeys = keys;
    }
    $scope.newdetachment.setFieldKeys();

    $scope.addNewPersonDetach = function() {
      $scope.newdetachment.people.push("");
      $scope.newdetachment.setFieldKeys();
    }

    $scope.addDetachment = function() {
      if ($scope.add_det) {
        $(".addDetachment").text("добавить отряд в расстановку")
      } else {
        $(".addDetachment").text("Скрыть добавление")
      }
      $scope.add_det = !$scope.add_det;      
    }

    $scope.addDetachmentSubmit = function() {
      var data_vk = {user_ids: $scope.newdetachment.people, fields: ["domain"]}
          $.ajax({
            type: "GET",
            url: "https://api.vk.com/method/users.get",
            dataType: "jsonp",
            data:  $.param(data_vk)
          }).done(function(response) {
            _.each(response.response, function(element, index, list){
              var idxxx = -1;
              _.find($scope.newdetachment.people, function(p, pind){if (p==element.domain) {idxxx = pind}; return p==element.domain})
              if (idxxx != -1) {
               $scope.newdetachment.people[idxxx] = element.uid;
              }
            })
            var new_people = [];
            for (var i=0; i < $scope.newdetachment.people.length; i++) {
              if ($scope.newdetachment.people[i]) {
                new_people.push($scope.newdetachment.people[i])
              }
            };
            var data = {
              comments: $scope.newdetachment.comments,
              people: new_people.join("$"),
              action: "add_detachment",
              shift_id: $scope.shift.id
            }
            $.ajax({ //TODO: make with angular
              type: "POST",
              url: "/handlers/shift.php",
              dataType: "json",
              data:  $.param(data)
            }).done(function(json) {
              var lnk = document.createElement("a");
              lnk.setAttribute("class", "ajax-nav")
              $(lnk).attr("href", window.location.href);
              $("#page-container").append(lnk);
              $(lnk).trigger("click")
            });
            $scope.$apply();
          });
    }

    $scope.editDetachment = function(key) {
      $scope.edit_detachment = $scope.detachments[key];
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
          data:  $.param(data)
        }).done(function(json) {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        });
      }      
    }

    $scope.deleteDetachment = function(key) {
      if (confirm("удалить отряд?")) {
        var data = {};
        data.action = "del_detach_shift";
        data.in_id = $scope.detachments[key].in_id;
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(json) {
          var lnk = document.createElement("a");
          lnk.setAttribute("class", "ajax-nav")
          $(lnk).attr("href", window.location.href);
          $("#page-container").append(lnk);
          $(lnk).trigger("click")
        });
      }
    }

    $scope.resetInfo = function() {
      $scope.shift = angular.copy($scope.master);
    }

    $scope.killShift = function() {
      var fid=window.location.href.split("/")
      var shiftid=fid[fid.length-1] //TODO сделать тут нормально!
      if (confirm("Точно удалить смену со всей информацией?")) {
        var data = {action: "kill_shift", id: shiftid}
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            window.location="/";
          }
        });
      }
    }
  }
}

//TODO phone input