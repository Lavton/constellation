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
              "/standart/js/jquery.hideseek.js",
              "/own/js/vk_input.js",
              "/own/js/users/all/all_in_common.js",
              "/own/js/users/all/all_fighters.js",
            ],
            "title": "бойцы | отряд в лицах | СПО \"СОзвездие\"",
            "loc": /^\/about\/users$/
          },
          "one": {
            "js": [
              "/standart/js/jquery.hideseek.js",
              "/own/js/vk_input.js",
              "/own/js/users/one/one_fighter.js",
            ],
            "title": "бойцы | отряд в лицах | СПО \"СОзвездие\"",
            "loc": /^\/about\/users\/[1-9][0-9]*$/
          }
        },
        "candidats": {
          "all": {
            "js": [
              "/standart/js/jquery.hideseek.js",
              "/own/js/vk_input.js",
              "/own/js/users/all/all_in_common.js",
              "/own/js/users/all/all_candidats.js",
            ],
            "title": "кандидаты | отряд в лицах | СПО \"СОзвездие\"",
            "loc": /^\/about\/candidats$/
          },
          "one": {
            "js": [
              "/standart/js/jquery.hideseek.js",
              "/own/js/vk_input.js",
              "/own/js/users/one/one_candidate.js",
            ],
            "title": "кандидаты | отряд в лицах | СПО \"СОзвездие\"",
            "loc": /^\/about\/candidats\/[1-9][0-9]*$/
          }
        }
      },
      "glossary": {
        "js": [
          "/own/js/on_click_show.js",
          "/own/js/stro.js"
        ],
        "title": "об отрядах | СПО \"СОзвездие\"",
        "loc": /^\/about\/glossary$/
      },
      "history": {
        "js": [],
        "title": "история | СПО \"СОзвездие\"",
        "loc": /^\/about\/history$/
      }
    },
    "method": {
      "index": {
        "js": [],
        "title": "методическая база | СПО \"СОзвездие\"",
        "loc": /^\/method\/$/
      },
      "camod": {
        "js": [
          "/own/js/on_click_show.js",
          "/own/js/camod.js"
        ],
        "title": "чемодан вожатого | методическая база | СПО \"СОзвездие\"",
        "loc": /^\/method\/musthave$/
      }
    },
    "events": {
      "shifts": {
        "all": {
          "js": [
            "/own/js/shifts/all/all.js",
            "/own/js/shifts/all/all_people.js",
            "/own/js/shifts/all/all_archive_and_new.js"
          ],
          "title": "смены | СПО \"СОзвездие\"",
          "loc": /^\/events\/shifts$/
        },
        "one": {
          "js": [
            "/standart/js/jquery.hideseek.js",
            "/own/js/vk_input.js",
            "/own/js/shifts/one_shift_common.js",
            "/own/js/shifts/one_shift_people.js",
            "/own/js/shifts/one_shift_add_apply.js"
            // "/own/js/shifts/one.js",
          ],
          "title": "смены | СПО \"СОзвездие\"",
          "loc": /^\/events\/shifts\/[1-9][0-9]*$/
        }

      }
    }
  }

  /*поместим все js файлы и пути отдельно*/
  window.jsFiles = {}
  window.locs = []
  var fin_state = [window.state]
  var fin_state_ind = 0;
  while (fin_state_ind < fin_state.length) {
    if (fin_state[fin_state_ind].js == undefined) {
      _.each(fin_state[fin_state_ind], function(element) {
        fin_state.push(element);
      })
    } else {
      _.each(fin_state[fin_state_ind].js, function(element) {
        window.jsFiles[element] = false;
      })
      window.locs.push([fin_state[fin_state_ind].loc, fin_state[fin_state_ind]])
    }
    fin_state_ind += 1
  }


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

  window.registerInit = function(init_f) {
    var locat = window.location.pathname;
    var script_date = _.find(window.locs, function(loc) {
      return loc[0].test(locat);
    })
    if (script_date) {
      script_date.push(init_f);
    }
  }

  window.current_group = getCookie("current_group") * 1 || 1
})();
