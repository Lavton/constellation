'use strict';
if (window.shifts == undefined) {
  window.shifts = {}
}
if (window.shifts.angular_conroller == undefined) {
  window.shifts.angular_conroller = null;
}
  var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {
    if ((window.location.pathname == "/events/shifts") && (window.shifts.angular_conroller == null)) {
      $('#page-container').on('click', ".pre-add-s-new", function() {
        var today = new Date();
        var start_date = today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate();
        var finish_date = today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate();
        var data = {
          action: "add_new_shift", 
          start_date: start_date,
          finish_date: finish_date
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/shift.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            window.location="/events/shifts/"+response.id;
          }
        });
      });
      if (window.shifts.angular_conroller == null) {      
        window.shifts.angular_conroller = angular.module('common_sc_app', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
        window.shifts.angular_conroller.controller('shiftsApp', ['$scope', '$http', init_angular_s_c]);
        angular.bootstrap(document, ['common_sc_app']);
        window.shifts.was_init = true;
      }
    }
    clearInterval(intID);
  }
}, 50);

/*логика ангулара*/
function init_angular_s_c ($scope, $http) {
  var data =  {action: "all",};
  $http.post('/handlers/shift.php', data).success(function(response) {
    $scope.shifts = {}
    var today = new Date();
    window.shifts.scope = $scope
    $scope.shifts.all = response.shifts;
    console.log(response);
    $scope.shifts.people = response.people;
    $scope.shifts.actual = [];
    $scope.shifts.future = [];
    $scope.Object = Object;
    $scope.shifts.max_arhive = ((new Date()).getFullYear())*1;
    $scope.shifts.arhive_year = $scope.shifts.max_arhive;
    _.each($scope.shifts.all, function(element, index, list) {
      element.st_date = new Date(element.start_date);
      element.fn_date = new Date(element.finish_date);
      var name = "";
      var st_month = element.st_date.getMonth()*1 + 1;//нумерация с нуля была
      var fn_month = element.fn_date.getMonth()*1 + 1;
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
      name += ", " + element.fn_date.getFullYear()
      if (element.place) {
        name += " (" + element.place + ")";
      }
      element.name = name;
      if (element.st_date > today) {
        $scope.shifts.future.push(element);
      } else {
        $scope.shifts.actual.push(element);
      }
    }); /*end _.each*/
    var vk_ids = [];
    _.each(response.people, function(element, index, list) {
      vk_ids.push(index);
    });
    getVkData(vk_ids, ["photo_50", "domain"], 
      function(vk_response) {
        _.each(response.people, function(element, index, list) {
          _.each(vk_response[index], function(j_element, j_index, list) {
            element[0][j_index] = j_element;
          });

          /*имя смены тут же записываем:*/
          _.each(element, function(sh_element, sh_index, list) {
            sh_element["shift_name"] = _.findWhere($scope.shifts.all, {id: sh_element.shift_id}).name;
          });
        });
      $scope.$apply();
      }
    ); /*end vk*/
  }); /*end http*/
  $scope.get_arhive = function(year) {
    var data =  {action: "arhive", year: year};
    $http.post('/handlers/shift.php', data).success(function(response) {
      var today = new Date();
      $scope.shifts.all = response.shifts;
      $scope.shifts.prev = [];
      _.each($scope.shifts.all, function(element, index, list) {
        element.st_date = new Date(element.start_date);
        element.fn_date = new Date(element.finish_date);
        var name = "";
        var st_month = element.st_date.getMonth()*1 + 1;//нумерация с нуля была
        var fn_month = element.fn_date.getMonth()*1 + 1;
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
        name += ", " + element.fn_date.getFullYear()
        if (element.place) {
          name += " (" + element.place + ")";
        }
        element.name = name;
        if (element.fn_date < today) {
          $scope.shifts.prev.push(element);
        } 
      });
    });

  }
}
