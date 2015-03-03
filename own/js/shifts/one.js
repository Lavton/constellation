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
          window.shifts.one_angular_conroller = angular.module('one_sc_app', [], function($httpProvider)
          { //магия, чтобы PHP понимал запрос
            // Используем x-www-form-urlencoded Content-Type
            $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
           
            // Переопределяем дефолтный transformRequest в $http-сервисе
            $httpProvider.defaults.transformRequest = [function(data)
            {
              var param = function(obj)
              {
                var query = '';
                var name, value, fullSubName, subValue, innerObj, i;
                
                for(name in obj)
                {
                  value = obj[name];
                  
                  if(value instanceof Array)
                  {
                    for(i=0; i<value.length; ++i)
                    {
                      subValue = value[i];
                      fullSubName = name + '[' + i + ']';
                      innerObj = {};
                      innerObj[fullSubName] = subValue;
                      query += param(innerObj) + '&';
                    }
                  }
                  else if(value instanceof Object)
                  {
                    for(subName in value)
                    {
                      subValue = value[subName];
                      fullSubName = name + '[' + subName + ']';
                      innerObj = {};
                      innerObj[fullSubName] = subValue;
                      query += param(innerObj) + '&';
                    }
                  }
                  else if(value !== undefined && value !== null)
                  {
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

    $("#page-container").on("focusout", "input.vk-domain", function() {
      console.log("out");
      get_vk()
    });



    function get_vk(callback) {
      var data_vk = {user_ids: $scope.shift.domain, fields: ["photo_200", "domain"]}
      $.ajax({
        type: "GET",
        url: "https://api.vk.com/method/users.get",
        dataType: "jsonp",
        data:  $.param(data_vk)
      }).done(function(json2) {
        console.log("Получили с VK")
        if ((json2 !== undefined) && (json2.error == undefined)) {
         var user_vk = json2.response[0];
        }
        if (user_vk == undefined) {
          user_vk = {photo_200: "http://vk.com/images/camera_b.gif",
            domain: json.user.vk_id,
            uid: 0
          }
          
        }
        console.log(user_vk);
        $scope.shift.domain = user_vk.domain
        $scope.shift.photo_200 = user_vk.photo_200;
        $scope.shift.vk_id = user_vk.uid;

        $scope.$apply();
        if (callback) {
          callback();
        }
      });
    }
  }
}

//TODO phone input