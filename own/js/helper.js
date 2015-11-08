/*функции, часто используемые везде, вынесенны сюда*/
/*преобразовать номер телефона в нормальный виж*/
window.goodTelephoneView = function(tel) {
  if (tel) {
    tel += ""
  }
  return tel ? "+7 (" + tel[0] + tel[1] + tel[2] + ") " + tel[3] + tel[4] + tel[5] + "-" + tel[6] + tel[7] + "-" + tel[8] + tel[9] : ""
}

window.checkIfInput = function(type) {
  var input = document.createElement("input");
  input.setAttribute("type", type);
  var notAValidValue = 'not-a-date';
  input.setAttribute("value", notAValidValue);
  return !(input.value == notAValidValue);
}

// форматирует строку вида '2015-10-20' в '20 ноября 2015'
window.formatDate = function(date) {
  if (!date) {
    return "";
  }
  date = new Date(date);
  Number.prototype.toMonthName = function() {
    var month = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
      'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    ];
    return month[this];
  };
  return date.getDate() + " " + date.getMonth().toMonthName() + " " + date.getFullYear();
}

// форматирует timestamp в удобный вид
window.formatTimestamp = function(date) {
  if (!date) {
    return "";
  }
  var time = date.split(" ")[1];
  date = new Date(date.split(" ")[0]);
  Number.prototype.toMonthName = function() {
    var month = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
      'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    ];
    return month[this];
  };
  return date.getDate() + " " + date.getMonth().toMonthName() + " " + date.getFullYear() + " " +
    time;
}

// говорит, число ли строка)
function isNumeric(input) {
  return (input - 0) == input && ('' + input).trim().length > 0;
}

// из разных вариантов ввода телефона делает его цифрами
function getPhone(input) {
  if (!input) {
    return input;
  }
  var out = "";
  _.each(input, function(ch) {
    if (isNumeric(ch)) {
      out += ch;
    }
  })
  if (out.length == 11) {
    var temp = ""
    for (var i = 1; i < out.length; i++) {
      temp += out[i];
    };
    out = temp;
  }
  if (out.length != 10) {
    out = null;
  }
  return out;
}

window.getPhone = getPhone;

// для выбора даты пикер
window.initDatePicker = function($scope, before_in) {
  // инициируем для выбора даты
  _.each($('input.date'), function(self) {
    // если нет встроенной поддержки от браузера
    if (!window.checkIfInput(self.getAttribute("type"))) {
      $(self).pickmeup({
        format: 'Y-m-d',
        hide_on_select: true,
        change: function() {
          return onSetDate(self, before_in);
        }
      });
    }
  });
  $(document).keyup(function(e) {
    if (e.keyCode == 27) {
      $('.date').pickmeup('hide');
    }
  });

  function onSetDate(self2, before_in) {
    if (self2) {
      var path = self2.getAttribute("ng-model").split(".");
      var self = $scope;
      for (var i = 0; i < path.length - 1; i++) {
        self = self[path[i]]
      };
      console.log($(self2).val())
      console.log(path)
      console.log(self[path[path.length - 1]])
      self[path[path.length - 1]] = $(self2).val();
    }
    if (before_in) {
      before_in();
    }
    
    if (self2) {
      $scope.$apply();
    }
    return true;
  }
}

// вернуть куки по имени
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ))
  return matches ? decodeURIComponent(matches[1]) : undefined
}
window.getCookie = getCookie;

/*закачивает базовую информацию о бойцах и кандидатах в localStorage и локальную переменную*/
function setPeople(callback) {
  if ((!(_.isArray(window.people))) || (!window.people.length)) {
    var people = [];

    function supports_html5_storage() {
      try {
        return 'localStorage' in window && window['localStorage'] !== null;
      } catch (e) {
        return false;
      }
    }
    var hasLocal = supports_html5_storage();
    var expire_time = 1000 * 60 * 60 * 24 * 5; // в мс
    if ((!hasLocal) || (hasLocal && !window.localStorage.getItem("people") || (parseInt(window.localStorage.getItem("people_ts")) < (_.now() - expire_time)))) {
      if (hasLocal) {
        window.localStorage.setItem("people_ts", _.now());
      }
      var data = {
        "action": "get_common_inf"
      }
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        vk_ids = [];
        _.each(json.users, function(element, index, list) {
          vk_ids.push(element.uid);
        });
        getVkData(vk_ids, ["photo_50", "domain"],
          function(response) {
            _.each(json.users, function(element, index, list) {
              var user = _.pick(response[element.uid], 'uid', "domain", "photo_50");
              _.extend(user, element);
              user.id = user.id * 1;
              user.uid = user.uid * 1;
              user.isCandidate = Boolean(user.isCandidate * 1);
              user.isFighter = Boolean(user.isFighter * 1);

              // строковое представление понадобится для поиска
              user.IF = user.first_name + " " + user.last_name;
              user.FI = user.last_name + " " + user.first_name;
              user.photo = user.photo_50;
              people.push(user);
            });
            window.people = people;
            if (hasLocal) {
              window.localStorage.setItem("people", JSON.stringify(window.people))
            }
            if (callback) {
              callback(true);
            }
          });
      });
    } else {
      if (hasLocal) {
        console.log("cached")
        window.people = JSON.parse(window.localStorage.getItem("people"))
        if (callback) {
          callback(false);
        }
      }
    }
  } else {
    if (callback) {
      callback(false)
    }
  }
}

window.setPeople = setPeople;

function addPeople(ids, callback) {

  function supports_html5_storage() {
    try {
      return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
      return false;
    }
  }
  var hasLocal = supports_html5_storage();
  if (!Array.isArray(ids)) {
    ids = [ids];
  }
  getVkData(ids, ["photo_50", "domain"],
    function(response) {
      var arr_resp = _.toArray(response)
      window.setPeople(function() {
        arr_resp = _.reject(arr_resp, function(person) {
          if (person) {
            var fnd = _.find(window.people, {
              "domain": person.domain
            })
            return fnd;
          } else {
            return 1;
          }
        })
        _.each(arr_resp, function(element) {
          element = _.pick(element, 'uid', "domain", "first_name", "last_name", "photo_50");
          element.photo = element.photo_50;
          element.IF = element.first_name + " " + element.last_name;
          element.FI = element.last_name + " " + element.first_name;
          element.photo = element.photo_50;
          window.people.push(element);
          if (hasLocal) {
            window.localStorage.setItem("people", JSON.stringify(window.people))
          }

        })
        if (callback) {
          callback(response);
        }
      })
    }
  );
}
window.addPeople = addPeople

function clearPeople() {
  delete window.people;
  window.localStorage.removeItem("people_ts");
  window.localStorage.removeItem("people");
}
window.clearPeople = clearPeople;

/*возвращает или генерит и возвращает человека по id */
window.getPerson = function(uid, callback) {
  window.setPeople(function(flag) {
    var cached = _.find(window.people, function(p) {
      return p.uid * 1 == uid * 1;
    })
    if (cached) {
      if (callback) {
        callback(cached, flag);
      }
    } else {
      window.addPeople([uid], function() {
        var cached = _.find(window.people, function(p) {
          return p.uid * 1 == uid * 1;
        })
        if (cached) {
          if (callback) {
            callback(cached, true);
          }

        }
      })
    }
  })
}

/*блок взаимодействия с ВКонтакте*/

var vk_users = {};
var vk_request_response = {};

/* делает запрос к вконтакте */
function getVkData(ids, fields, callback) {
  /*если мы передали один id, запихнём его в массив*/
  if (!Array.isArray(ids)) {
    ids = [ids];
  }

  if ((_.filter(fields, function(field) {
      return field == "domain";
    }).length) == 0) {
    fields.push("domain")
  }
  /*  var exist = true;
    _.each(ids, function(element, index, list){
      if (vk_request_response[element]) {
        var user = vk_users[vk_request_response[element]];
        if (user) {
          _.each(fields, function(field, index_f, list_f){
            if (!user[field]) {
              exist = false;
            }
          })        
        }
      }
    })
    if (exist) {
        if (callback) {
          callback(result)
        };    
    }
  */

  /*позволяем добавлять адрес типа https://vk.com/lavton */
  var without_vk_com = [];
  _.each(ids, function(element, index_f, list_f) {
    if (element) {
      /*домен всегда в последнем элементе*/
      element += "";
      without_vk_com.push(element.split("vk.com/")[element.split("vk.com/").length - 1])
    }
  })
  var data_vk = {
    "user_ids": _.unique(without_vk_com),
    "fields": fields
  }
  $.ajax({
    type: "GET",
    url: "https://api.vk.com/method/users.get",
    dataType: "jsonp",
    data: $.param(data_vk)
  }).done(function(json) {
    console.log("go to VK.")
    if (json.error == undefined) {
      console.log("Get")
      console.log(json.response);
      for (var i = 0; i < json.response.length; i++) {
        var vk_user = json.response[i];
        /* Если мы спросили фотку, но не получили - ставим заглушку*/
        var leng = _.filter(fields, function(field) {
          return field == "photo_200";
        }).length;
        if ((leng) && (vk_user["photo_200"] == undefined)) {
          vk_user.photo_200 = "http://vk.com/images/camera_a.gif"
        }

        var leng = _.filter(fields, function(field) {
          return field == "photo_100";
        }).length;
        if ((leng) && (vk_user["photo_100"] == undefined)) {
          vk_user.photo_100 = "http://vk.com/images/camera_b.gif"
        }

        var leng = _.filter(fields, function(field) {
          return field == "photo_50";
        }).length;
        if ((leng) && (vk_user["photo_50"] == undefined)) {
          vk_user.photo_50 = "http://vk.com/images/camera_c.gif"
        }

        /* Если такого пользователя ещё не было в списке
        всех пользователей - добавим его.

        И, в любом случае, добавим поля, которых не было раньше.
        */
        var uid = vk_user.uid;
        if (!vk_users[uid]) {
          vk_users[uid] = {}
        }
        _.each(vk_user, function(element, index, list) {
          vk_users[uid][index] = element;
        })
      }

      /*сопоставим ответ и исходный запрос*/
      var result = {};
      _.each(ids, function(element, index, list) {
        if (element) {
          element += ""
          var clear_el = element.split("vk.com/")[element.split("vk.com/").length - 1];
          /*считаем, что передали доменное имя*/
          result[element] = _.findWhere(json.response, {
            "domain": clear_el
          });
          /*если нет - наверно строку вида id1*/
          if (result[element] == undefined) {
            if (clear_el.search("id") == 0) {
              result[element] = _.findWhere(json.response, {
                "uid": clear_el.split("id")[1] * 1
              });
            }
          }
          /*если нет - может чистый id?*/
          if (result[element] == undefined) {
            result[element] = _.findWhere(json.response, {
              "uid": clear_el * 1
            });
          }

          /*Сопоставим запросу uid*/
          if (result[element]) {
            vk_request_response[element] = result[element].uid;
            vk_request_response[result[element].uid] = result[element].uid;
            if (result[element].domain) {
              vk_request_response[result[element].domain] = result[element].uid;
            }
          }
        }
      })
      if (callback) {
        callback(result)
      };
      return true;
    } else {
      console.log("error");
      var result = {};
      _.each(ids, function(element, index, list) {
        /*считаем, что передали доменное имя*/
        result[element] = undefined;
      });
      if (callback) {
        callback(result)
      };
      return false;
    }
  });

}

// сохраняет мероприятия в кеш для доступа к ним оффлайн
window.getEventsToOffline = function(force) {
  console.log("events loaded")
  if (force || ((!(_.isArray(window.events))) || (!window.events.length))) {
    var events = [];

    function supports_html5_storage() {
      try {
        return 'localStorage' in window && window['localStorage'] !== null;
      } catch (e) {
        return false;
      }
    }
    var hasLocal = supports_html5_storage();
    if (force || ((!hasLocal) || (hasLocal && !window.localStorage.getItem("events")))) {
      var data = {
        "action": "get_events_for_offline"
      }
      $.ajax({
        type: "POST",
        url: "/handlers/event.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        var comments = []
        _.each(json.events, function(element, index, list) {
          comments.push({
            id: element.id,
            comment: element.comments
          });
        });
        var bbdata = {
          bbcode: comments,
          ownaction: "bbcodesToHtml"
        };
        $.ajax({
          type: "POST",
          url: "/standart/markitup/sets/bbcode/parser.php",
          dataType: 'json',
          global: false,
          data: $.param(bbdata)
        }).done(function(rdata) {
          _.each(json.events, function(element, index, list) {
            element.comments = _.findWhere(rdata, {
              id: element.id + ""
            }).bbcomment;
          });
          window.events = json.events;
          if (hasLocal) {
            window.localStorage.setItem("events", JSON.stringify(window.events))
          }
        })

      });
    } else {
      if (hasLocal) {
        console.log("cached")
        window.events = JSON.parse(window.localStorage.getItem("events"))
      }
    }
  } else {
  }
}
// при каждой перезагрузке подгружаем и это
window.getEventsToOffline(true)

window.clearEventsOffline = function() {
  delete window.events;
  window.localStorage.removeItem("events_ts");
  window.localStorage.removeItem("events");
}
