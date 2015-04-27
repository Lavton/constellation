'use strict';
function get_event(eventid) {
  if (window.events == undefined) {
    window.events = {}
  }
  window.events.one_angular_conroller = null;
  var fid=window.location.href.split("/")
  var eventid=fid[fid.length-1] //TODO сделать тут нормально!

  if (! window.events.one_script ) {
    window.events.one_script = true;
  var intID = setInterval(function(){
      var fid=window.location.href.split("/")
      var eventid=fid[fid.length-1] //TODO сделать тут нормально!
      if ((typeof(angular) !== "undefined") && (eventid != "events")) {
        if (window.events.one_angular_conroller == null) {
          window.events.one_angular_conroller = angular.module('one_eo_app', ["ngSanitize"], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
          window.events.one_angular_conroller.controller('oneEventApp', ['$scope', '$http', init_angular_o_e_c]);
          angular.bootstrap(document, ['one_eo_app']);
          window.events.was_init_one = true;
        
        } else {
          angular.bootstrap(document, ['one_eo_app']);
        }
        clearInterval(intID);
    }
  }, 50);
  } else {
     angular.bootstrap(document, ['one_eo_app']);
  }
  // логика ангулара
  function init_angular_o_e_c ($scope, $http, $locale) {
    $scope.id = eventid;
    $scope.event = {};
    $(".event-info").removeClass("hidden")
    var inthrefID = setInterval(function(){
    var fid=window.location.href.split("/")
    var eventid=fid[fid.length-1] //TODO сделать тут нормально!
    if (eventid != "events") {
      clearInterval(inthrefID);
      var data = {action: "get_one_info", id: eventid}
      $.ajax({
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data:  $.param(data)
      }).done(function(json) {
        $scope.event = json.event;
        $scope.event.visibility = $scope.event.visibility*1;
        $scope.parent_event = json.parent_event;
        var bbdata =  {bbcode: $scope.event.comments, ownaction: "bbcodeToHtml"};
        $.ajax({
          type: "POST",
          url: "/markitup/sets/bbcode/parser.php",
          dataType: 'text',
          global: false,
          data: $.param(bbdata)
        }).done(function(rdata) {
          $scope.event.bbcomments = rdata,
          $scope.$apply();
        });
        // debugger;
          //TODO make works all html. (jquery?)

        $("a.event_priv").attr("href", json.prev.mid)
        $("a.event_next").attr("href", json.next.mid)
        if (!json.prev.mid) {
          $("a.event_priv").hide();
        }
        if (!json.next.mid) {
          $("a.event_next").hide();
        }

          $scope.$apply();
        });
      }
    }, 100);
    $.getJSON("/own/group_names.json", function(group_json){
      $scope.groups = group_json;
      $scope.$apply();
    });

    $scope.editEventInfo = function(flag) {
      $(".event-info").toggleClass("hidden");
      $(".event-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.event); 

      if (flag) { // начинаем редактировать
        var spl =$scope.event.start_time.split(" ");
        $scope.event.start_date=spl[0];
        var time = spl[1].split(":");
        $scope.event.start_ttime=time[0]+":"+time[1];

        spl =$scope.event.end_time.split(" ");
        $scope.event.end_date=spl[0];
        $scope.event.end_ttime=spl[1];
        var time = spl[1].split(":");
        $scope.event.end_ttime=time[0]+":"+time[1];

        //Если ещё не редактировали до этого, найдём все мероприятия в кандидаты в родители
        if (!$scope.pos_parents) {
          var data = {action: "get_reproduct", visibility: $scope.event.visibility, end_time:$scope.event.end_time}
          $http.post('/handlers/event.php', data).success(function(response) {
            $scope.pos_parents = [];
            if (response.pos_parents) {
              $scope.pos_parents = response.pos_parents;
            }
            $scope.pos_parents.push({id: null, name: "--нет--"})
        });
         
        }
      } 
    }
    $scope.resetInfo = function() {
      $scope.shift = angular.copy($scope.master);
    }

    $scope.submit = function() {
      $scope.event.start_time = $scope.event.start_date+" "+$scope.event.start_ttime+":00";
      $scope.event.end_time = $scope.event.end_date+" "+$scope.event.end_ttime+":00";
      var data =  angular.copy($scope.event);
      data.action = "set_new_data"
      _.each(data, function(element, index, list){
        if (!element) {
          data[index] = null;
        }
      })
      if (!$scope.parent_event) {
        $scope.parent_event = {}
      }
      $scope.parent_event.id = $scope.event.parent_id;
      $scope.parent_event.name = _.findWhere($scope.pos_parents, {id: $scope.event.parent_id}).name;

      var bbdata =  {bbcode: $scope.event.comments, ownaction: "bbcodeToHtml"};
      $.ajax({
        type: "POST",
        url: "/markitup/sets/bbcode/parser.php",
        dataType: 'text',
        global: false,
        data: $.param(bbdata)
      }).done(function(rdata) {
        $scope.event.bbcomments = rdata,
        $scope.$apply();
      });

      $http.post('/handlers/event.php', data).success(function(response) {
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });
    }

    $scope.killEvent = function() {
      var fid=window.location.href.split("/")
      var shiftid=fid[fid.length-1] //TODO сделать тут нормально!
      if (confirm("Точно удалить мероприятие со всей информацией?")) {
        var data = {action: "kill_event", id: eventid}
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/event.php",
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