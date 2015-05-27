/*функции, часто используемые везде, вынесенны сюда*/

/*блок взаимодействия с ВКонтакте*/

var vk_users = {};
var vk_request_response = {};

/* делает запрос к вконтакте */
function getVkData (ids, fields, callback) {
  /*если мы передали один id, запихнём его в массив*/
  if (!Array.isArray(ids)) {
    ids = [ids];
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

	var data_vk = {"user_ids": ids, "fields": fields}
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
        /*считаем, что передали доменное имя*/
        result[element] = _.findWhere(json.response, {"domain": element});
        /*если нет - наверно строку вида id1*/
        if (result[element] == undefined) {
          if (element.search("id") == 0) {
            result[element] = _.findWhere(json.response, {"uid": element.split("id")[1]*1});
          }
        }
        /*если нет - может чистый id?*/
        if (result[element] == undefined) {
          result[element] = _.findWhere(json.response, {"uid": element*1});
        }

        /*Сопоставим запросу uid*/
        if (result[element]) {
          vk_request_response[element] = result[element].uid;
          vk_request_response[result[element].uid] = result[element].uid;
          if (result[element].domain) {
            vk_request_response[result[element].domain] = result[element].uid;
          }
        }
      })

      if (callback) {
        callback(result)
      };
      return true;
    } else {
      console.log("error");
      return false;
    }
  });

}