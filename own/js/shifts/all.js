'use strict';

if (window.shifts == undefined) {
  window.shifts = {}
}
if (window.shifts.angular_conroller == undefined) {
  window.shifts.angular_conroller = null;
}
if (! window.shifts.all_script ) {
  window.shifts.all_script = true;
  var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {
    if (window.shifts.angular_conroller == null) {
      window.shifts.angular_conroller = angular.module('common_sc_app', [], function($httpProvider)
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
        window.shifts.angular_conroller.controller('shiftsApp', ['$scope', '$http', init_angular_s_c]);
        angular.bootstrap(document, ['common_sc_app']);
        window.shifts.was_init = true;
      }
      // if (window.shifts.was_init && isNaN(parseInt($("table.common-contacts tbody tr").attr("class")))) {
        // angular.bootstrap(document, ['common_sc_app']);
      // }
      clearInterval(intID);
  }
}, 50);
}

/*логика ангулара*/
function init_angular_s_c ($scope, $http) {
  var data =  {action: "all",};
  $http.post('/handlers/shift.php', data).success(function(response) {
    console.log(response);
    $scope.shifts = {}
    window.shifts.scope = $scope
    $scope.shifts.all = response.shifts;
  });
}
