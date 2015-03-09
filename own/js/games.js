'use strict';
if (window.games == undefined) {
  window.games = {}
}
if (window.games.angular_conroller == undefined) {
  window.games.angular_conroller = null;
}
  var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {
    if (window.games.angular_conroller == null) {
      window.games.angular_conroller = angular.module('game_app', [], function($httpProvider)
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
      window.games.angular_conroller.controller('gameApp', ['$scope', '$http', init_angular_game_c]);
      angular.bootstrap(document, ['game_app']);
      window.games.was_init = true;
    }
    clearInterval(intID);
  }
}, 50);

/*логика ангулара*/
function init_angular_game_c ($scope, $http) {
  console.log("Hi "+ new Date());
  $scope.hello = "hello"
}
