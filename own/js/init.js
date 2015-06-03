(function() {

  window.visibilities = {
    "1": "UNREG",
    "2": "CANDIDATE",
    "3": "FIGHTER",
    "4": "OLD_FIGHTER",
    "5": "EX_COMMAND_STAFF",
    "6": "COMMAND_STAFF",
    "7": "ADMIN"
  }
  window.groups = {
    "UNREG": {
      "num": 1,
      "rus": "Незарегистрированный пользователь",
    },
    "CANDIDATE": {
      "num": 2,
      "rus": "Кандидат",
    },
    "FIGHTER": {
      "num": 3,
      "rus": "Боец",
    },
    "OLD_FIGHTER": {
      "num": 4,
      "rus": "Старик отряда",
    },
    "EX_COMMAND_STAFF": {
      "num": 5,
      "rus": "Экс-комсостав",
    },
    "COMMAND_STAFF": {
      "num": 6,
      "rus": "Комсостав",
    },
    "ADMIN": {
      "num": 7,
      "rus": "Администратор",
    }
  }

  window.state = {
    "about": {
      "users": {
        "fighters": {
          "all": {
            "js": [
              "/own/js/users/all_in_common.js",
              "/own/js/users/all_fighters.js",
            ],
            "was_loaded": false,
            "title": "бойцы | отряд в лицах | СПО \"СОзвездие\""
          },
          "one": {
            "js": [
              "/own/js/users/one_fighter.js",
            ],
            "was_loaded": false,
            "title": "бойцы | отряд в лицах | СПО \"СОзвездие\""

          }
        },
        "candidats": {
          "all": {
            "js": [
              "/own/js/users/all_in_common.js",
              "/own/js/users/all_candidats.js",
            ],
            "was_loaded": false,
            "title": "кандидаты | отряд в лицах | СПО \"СОзвездие\""
          }
        }
      }
    }
  }

  window.jsFiles = {
    "/own/js/users/all_candidats.js": false,
    "/own/js/users/all_fighters.js": false,
    "/own/js/users/all_in_common.js": false,
    "/own/js/users/one_fighter.js": false,
  }
  window.locs = [
    [
      /^\/about\/candidats$/, window.state.about.users.candidats.all
    ],
    [
      /^\/about\/users$/, window.state.about.users.fighters.all
    ],
    [
      /^\/about\/users\/[1-9][0-9]*$/, window.state.about.users.fighters.one
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
        for (name in obj) {
          value = obj[name];
          if (value instanceof Array) {
            for (i = 0; i < value.length; ++i) {
              subValue = value[i];
              fullSubName = name + '[' + i + ']';
              innerObj = {};
              innerObj[fullSubName] = subValue;
              query += param(innerObj) + '&';
            }
          } else if (value instanceof Object) {
            for (subName in value) {
              subValue = value[subName];
              fullSubName = name + '[' + subName + ']';
              innerObj = {};
              innerObj[fullSubName] = subValue;
              query += param(innerObj) + '&';
            }
          } else if (value !== undefined && value !== null) {
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

  window.current_group = getCookie("current_group") * 1 || 1
})();