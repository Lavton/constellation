/*функции, часто используемые везде, вынесенны сюда*/

/*закачивает базовую информацию о бойцах и кандидатах в localStorage и локальную переменную*/
function setPeople(callback) {
  window.people = [];
  function supports_html5_storage() {
  try {
    return 'localStorage' in window && window['localStorage'] !== null;
  } catch (e) {
      return false;
    }
  }
  var hasLocal = supports_html5_storage();
    var expire_time = 1000*60*60*24; // в мс
    if ((!hasLocal) || (hasLocal && !window.localStorage.getItem("people_ts") || (parseInt(window.localStorage.getItem("people_ts")) < (_.now() - expire_time)))) {
      if (hasLocal) {
        window.localStorage.setItem("people_ts", _.now());
      }
      var data = {"action": "get_common_inf"}
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data:  $.param(data)
      }).done(function(json) {
        vk_ids = [];
        _.each(json.candidats, function(element, index, list) {
          vk_ids.push(element.uid);
        });
        _.each(json.fighters, function(element, index, list) {
          vk_ids.push(element.uid);
        });
        getVkData(vk_ids, ["photo_50", "domain"], 
        function(response) {
          _.each(response, function(element, index, list) {
            /*получаем инфу о человеке*/
            var user = _.pick(element, 'uid', "domain", "first_name", "last_name", "photo_50");
            /*дописываем специфичную для кандидата*/
            var special = _.findWhere(json.candidats, {uid: user.uid+""})
            if (special) {
              user.isFighter = false;
              user.id = special.id*1;
            }
            /*дописываем инфу как бойцов. (перезаписываем значения по умолчанию)*/
            var special = _.findWhere(json.fighters, {uid: user.uid+""})
            if (special) {
              user.isFighter = true;
              user.id = special.id*1;
              user.first_name = special.first_name;
              user.last_name = special.last_name;
            }

            /*строковое представление понадобится для поиска*/
            user.IF = user.first_name + " " + user.last_name;
            user.FI = user.last_name + " " + user.first_name;
            user.photo = user.photo_50;
            window.people.push(user);
          });
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
}

window.setPeople = setPeople;
// setPeople(function() {console.log(_.now()-tl)});
function clearPeople() {
  delete window.people;
  window.localStorage.removeItem("people_ts");
  window.localStorage.removeItem("people");
}
/*блок взаимодействия с ВКонтакте*/

var vk_users = {};
var vk_request_response = {};

/* делает запрос к вконтакте */
function getVkData (ids, fields, callback) {
  /*если мы передали один id, запихнём его в массив*/
  if (!Array.isArray(ids)) {
    ids = [ids];
  }

  if ((_.filter(fields, function(field){ return field == "domain"; }).length) == 0) {
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
  _.each(ids, function(element, index_f, list_f){
    if (element) {
      /*домен всегда в последнем элементе*/
      element += "";
      without_vk_com.push(element.split("vk.com/")[element.split("vk.com/").length-1])
    }
  })   
	var data_vk = {"user_ids": _.unique(without_vk_com), "fields": fields}
  $.ajax({
    type: "GET",
    url: "https://api.vk.com/method/users.get",
    dataType: "jsonp",
    data:  $.param(data_vk)
  }).done(function(json) {
    console.log("go to VK.")
    if (json.error == undefined) {
      console.log("Get")
      // console.log(json.response);
      for (var i = 0; i < json.response.length; i++) {
        var vk_user = json.response[i];
        /* Если мы спросили фотку, но не получили - ставим заглушку*/
        var leng = _.filter(fields, function(field){ return field == "photo_200"; }).length;
        if ((leng) && (vk_user["photo_200"] == undefined)) {
          vk_user.photo_200 = "http://vk.com/images/camera_a.gif"
        }

        var leng = _.filter(fields, function(field){ return field == "photo_100"; }).length;
        if ((leng) && (vk_user["photo_100"] == undefined)) {
          vk_user.photo_100 = "http://vk.com/images/camera_b.gif"
        }

        var leng = _.filter(fields, function(field){ return field == "photo_50"; }).length;
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
        _.each(vk_user, function(element, index, list){
          vk_users[uid][index] = element;
        })
      }

      /*сопоставим ответ и исходный запрос*/
      var result = {};
      _.each(ids, function(element, index, list){
        if (element) {
          element += ""
          var clear_el = element.split("vk.com/")[element.split("vk.com/").length-1];
          /*считаем, что передали доменное имя*/
          result[element] = _.findWhere(json.response, {"domain": clear_el});
          /*если нет - наверно строку вида id1*/
          if (result[element] == undefined) {
            if (element.search("id") == 0) {
              result[element] = _.findWhere(json.response, {"uid": clear_el.split("id")[1]*1});
            }
          }
          /*если нет - может чистый id?*/
          if (result[element] == undefined) {
            result[element] = _.findWhere(json.response, {"uid": clear_el*1});
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
      _.each(ids, function(element, index, list){
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