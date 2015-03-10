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
      if ((typeof(angular) !== "undefined") && (shiftid != "users")) {
        if (window.shifts.one_angular_conroller == null) {
          window.shifts.one_angular_conroller = angular.module('one_sc_app', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
    console.log("hello")
    $scope.id = shiftid;
    $scope.shift = {};
    $scope.adding = {};
    $scope.adding.vk_likes = {};


    $scope.editShiftInfo = function() {
      $(".shift-info").toggleClass("hidden");
      $(".shift-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.shift); 
    };
    $(".shift-info").removeClass("hidden")
     var inthrefID = setInterval(function(){
      var fid=window.location.href.split("/")
      var shiftid=fid[fid.length-1] //TODO сделать тут нормально!
      if (shiftid != "users") {
        clearInterval(inthrefID);
        var data = {action: "get_one_info", id: shiftid}
        console.log(data + " " + shiftid + " " + fid)
        // debugger;
        $scope.shift.photo_200 = "http://vk.com/images/camera_b.gif"
        $.ajax({
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(json) {
          console.log("Получили с сервера "+shiftid + " "+window.location.href)
          console.log(json);
          $scope.myself = json.myself;
          var vk_ids = []
          _.each(json.like_h, function(element, index, list) {
            vk_ids.push(element.vk_id);
          });
          vk_ids.push($scope.myself.vk_id)
          vk_ids.push($scope.myself.like_one)
          vk_ids.push($scope.myself.like_two)
          vk_ids.push($scope.myself.like_three)
          vk_ids.push($scope.myself.dislike_one)
          vk_ids.push($scope.myself.dislike_two)
          vk_ids.push($scope.myself.dislike_three)
          var data_vk = {user_ids: vk_ids, fields: ["domain", "photo_50"]}
          $.ajax({
            type: "GET",
            url: "https://api.vk.com/method/users.get",
            dataType: "jsonp",
            data:  $.param(data_vk)
          }).done(function(response) {
            console.log("get vk like ", response.response);
            $scope.vk_info = response.response;
            $scope.adding.vk_likes = [];
            _.each(json.like_h, function(element, index, list) {
              var vk_d = _.findWhere($scope.vk_info, {uid: element.vk_id*1});
              _.each(vk_d, function(element2, index, list){
                element[index] = vk_d[index];
              })
              element.fighter = element.fighter_id;
            });
            $scope.adding.vk_likes = json.like_h;

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

            $scope.$apply();
            console.log($scope.adding.vk_likes)
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
            console.log("hide prev");
          }

          if (!json.next.mid) {
            $("a.shift_next").hide();
            console.log("hide next");
          }

          $.getJSON("/own/group_names.json", function(group_json){
            $scope.groups = group_json;
            $scope.$apply();
          });
          $scope.$apply();
        });
      }
    }, 100);
    $scope.guessAdd = function() {
      if (confirm("Записаться на смену?")) {
        var data = $scope.adding;
        data.action = "apply_to_shift";
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
            console.log(json)
            var saved = $(".saved");
            $(saved).stop(true, true);
            $(saved).fadeIn("slow");
            $(saved).fadeOut("slow");
            window.location.href = window.location.href;
          });

        });
        console.log("submite")
      }
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
      console.log(data)
      $http.post('/handlers/shift.php', data).success(function(response) {
        console.log(response)
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });      
      console.log("submite")

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