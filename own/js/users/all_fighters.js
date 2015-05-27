/*логика ангулара*/
function init_angular_f_c ($scope, $http) {
  /*инициализация*/
  /*сначала - данные с сервера*/
  var data =  {action: "all",};
  $http.post('/handlers/user.php', data).success(function(response) {
    $scope.fighters = response.users;
    _.each($scope.fighters, function(element, index, list) {
      element.fi = element.name + " " + element.surname;
      element.vk_domain = "id"+element.vk_id;
      element.photo_100 = "http://vk.com/images/camera_b.gif";
    });
    /*после - данные с ВК*/
    var all_vk_ids = [];
    _.each($scope.fighters, function(element, index, list) {
      all_vk_ids.push(element.vk_id);
    });
    getVkData(all_vk_ids, ["photo_100", "photo_200", "domain"], 
      function(response) {
        _.each($scope.fighters, function(element, index, list) {
          element.vk_domain = response[element.vk_id].domain;
          element.photo_100 = response[element.vk_id].photo_100;
        });
        $scope.$apply();
      }
    );
  });
  /*конец инициализации*/

  /*просто изменение формата вывода телефона*/
  $scope.goodView = function(tel) {
    return tel ? "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9] : ""
  }

}




'use strict';
/*магия, чтобы ангулар нормально работал*/
if (window.fighters == undefined) {
  window.fighters = {}
}
if (window.fighters.angular_conroller == undefined) {
  window.fighters.angular_conroller = null;
}
  var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {
    if ((window.location.pathname == "/about/users") && (window.fighters.angular_conroller == null)) {
      if (window.fighters.angular_conroller == null) {      
        window.fighters.angular_conroller = angular.module('common_fc_app', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
        window.fighters.angular_conroller.controller('fightersApp', ['$scope', '$http', init_angular_f_c]);
        angular.bootstrap(document, ['common_fc_app']);
        window.fighters.was_init = true;
      }
    }
    clearInterval(intID);
  }
}, 50);
