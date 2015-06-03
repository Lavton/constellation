(function () {

  window.visibilities = {
    "1": "Незарегистрированный пользователь",
    "2": "Кандидат",
    "3": "Боец",
    "4": "Старик отряда",
    "5": "Экс-комсостав",
    "6": "Комсостав",
    "7": "Администратор"
  }

  window.state = {
    "about": {
      "users": {
        "fighters": {
          "all": {
            "js": ["/own/js/users/all_fighters.js"],
            "was_loaded": false,
            "title": "бойцы | отряд в лицах | СПО \"СОзвездие\""
          }
        },
        "candidats": {
          "all": {
            "js": ["/own/js/users/all_candidats.js"],
            "was_loaded": false,
            "title": "кандидаты | отряд в лицах | СПО \"СОзвездие\""
          }
        }
      }
    }
  }
  window.locs = [
    [ 
    /\/about\/candidats/ , window.state.about.users.candidats.all
    ],
    [
    /\/about\/users/ , window.state.about.users.fighters.all
    ]
  ]
	window.angular_inits = {};

	var angular_conroller = angular.module('constellation', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
    window.init_ang = function(controller, init_f, el) {
      angular_conroller.controller(controller, ['$scope', '$http', init_f]);
      angular.bootstrap(document.getElementById(el), ['constellation']);
    }
})();