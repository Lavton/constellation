$('#page-container').on('click', ".pre-add-new-event", function() {
  var auto_date = null;
  if (! $(".pre-add-new-event").hasClass("clicked")) {
    $(".add-new-input-w").removeClass("hidden")
    $(".pre-add-new-event").addClass("clicked")
    $(".pre-add-new-event").text("Добавить")
    auto_date = setInterval(function(){
      var start_date = $(".add-new-event-start-date").val();
      var end_date = $(".add-new-event-end-date").val();
      if (end_date == "") {
        if (start_date != "") {
          $(".add-new-event-end-date").val(start_date);
        }
      } else {
        clearInterval(auto_date);
      }
    }, 750);

  } else {
    var name = $(".add-new-event-name").val();
    var start_date = $(".add-new-event-start-date").val();
    var start_time = $(".add-new-event-start-time").val();
    var end_date = $(".add-new-event-end-date").val();
    var end_time = $(".add-new-event-end-time").val();
    // debugger;
    var send_data = {
      action: "add_new_event", 
      name: name,
      start_time: start_date+ " "+start_time+":00",
      end_time: end_date+ " "+end_time+":00"
    }
    $.ajax({ //TODO: make with angular
      type: "POST",
      url: "/handlers/event.php",
      dataType: "json",
      data:  $.param(send_data)
    }).done(function(response) {
      if (response.result == "Success") {
        console.log("yeah!")
        // window.location="/about/users/"+$(".add-new-fighter-id").val();
      }
    });
  }
});


'use strict';
if (window.events == undefined) {
  window.events = {}
}
if (window.events.angular_conroller == undefined) {
  window.events.angular_conroller = null;
}
  var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {
    if ((window.location.pathname == "/events/") && (window.events.angular_conroller == null)) {
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
            window.location="/events/events/"+response.id;
          }
        });
      });
      if (window.events.angular_conroller == null) {      
        window.events.angular_conroller = angular.module('common_sc_app', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
        window.events.angular_conroller.controller('eventsApp', ['$scope', '$http', init_angular_s_c]);
        angular.bootstrap(document, ['common_sc_app']);
        window.events.was_init = true;
      }
    }
    clearInterval(intID);
  }
}, 50);

/*логика ангулара*/
function init_angular_s_c ($scope, $http) {
  var data =  {action: "all",};
  $http.post('/handlers/shift.php', data).success(function(response) {
    $scope.events = {}
    var today = new Date();
    window.events.scope = $scope
    $scope.events.all = response.events;
    $scope.events.prev = [];
    $scope.events.actual = [];
    $scope.events.future = [];
    _.each($scope.events.all, function(element, index, list) {
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
        $scope.events.prev.push(element);
      } else if (element.st_date > today) {
        $scope.events.future.push(element);
      } else {
        $scope.events.actual.push(element);
      }
    });
  });
}
