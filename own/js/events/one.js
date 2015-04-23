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
          window.events.one_angular_conroller.controller('oneEventApp', ['$scope', '$http', '$locale', init_angular_o_e_c]);
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
  /*логика ангулара*/
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
      console.log("send")
      $.ajax({
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data:  $.param(data)
      }).done(function(json) {
        debugger;
          //TODO make works all html. (jquery?)
          $scope.$apply();
        });
      }
    }, 100);
  }
}