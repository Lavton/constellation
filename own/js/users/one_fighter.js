'use strict';
function get_user_info(userid) {
  if (window.fighters == undefined) {
    window.fighters = {}
  }
  window.fighters.one_angular_conroller = null;

  if (! window.fighters.one_script ) {
    window.fighters.one_script = true;
  var intID = setInterval(function(){
    if (typeof(angular) !== "undefined") {
        if (window.fighters.one_angular_conroller == null) {
          window.fighters.one_angular_conroller = angular.module('one_c_app', [], function($httpProvider)
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
          window.fighters.one_angular_conroller.controller('oneFighterApp', ['$scope', '$http', '$locale', init_angular_o_f_c]);
          angular.bootstrap(document, ['one_c_app']);
          window.fighters.was_init_one = true;
        
        } else {
          angular.bootstrap(document, ['one_c_app']);
        }
        clearInterval(intID);
    }
  }, 50);
  } else {
     angular.bootstrap(document, ['one_c_app']);
  }

  /*логика ангулара*/
  function init_angular_o_f_c ($scope, $http, $locale) {
    console.log("hello")
    $locale.id = 'ru-ru' //TODO make it works(
    $scope.goodView = function(tel) {
      return tel ? "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9] : ""
    }

    $scope.fighter = {};
    $scope.editPerson = function() {
      $(".user-info").toggleClass("hidden");
      $(".user-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.fighter); 
    };
    $(".user-info").removeClass("hidden")
    var data = {action: "get_one_info", id: userid}
    $scope.fighter.photo_200 = "http://vk.com/images/camera_b.gif"
    $.ajax({
      type: "POST",
      url: "/handlers/user.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(json) {
      console.log(json);
      $scope.fighter = json.user;
      $scope.fighter.year_of_entrance = 1 * $scope.fighter.year_of_entrance;
      $scope.fighter.group_of_rights = 1 * $scope.fighter.group_of_rights;
      console.log("start")
      $.getJSON("/own/group_names.json", function(group_json){
        $scope.groups = group_json;
        $scope.$apply();
      });
      $scope.fighter.domain = "id"+$scope.fighter.vk_id
      $scope.$apply();
        
      get_vk();

    });

    $scope.submit = function() {
      get_vk(function() {
      var data =  angular.copy($scope.fighter);
      data.vk_id = ""+data.vk_id;
      data.action = "set_new_data"
      _.each(data, function(element, index, list){
        if (!element) {
          data[index] = null;
        }
      })
      console.log(data)
      $http.post('/handlers/user.php', data).success(function(response) {
        console.log(response)
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      });      
      console.log("submite")
      });
    }
    $scope.resetInfo = function() {
      $scope.fighter = angular.copy($scope.master);
    }

    $scope.killFighter = function() {
      if (confirm("Точно удалить профиль?")) {
        var data = {action: "kill_fighter", id: userid}
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            window.location="/";
          }
        });
      }
    }

    $("#page-container").on("focusout", "input.vk-domain", function() {
      console.log("out");
      get_vk()
    });



    function get_vk(callback) {
      var data_vk = {user_ids: $scope.fighter.domain, fields: ["photo_200", "domain"]}
      $.ajax({
        type: "GET",
        url: "https://api.vk.com/method/users.get",
        dataType: "jsonp",
        data:  $.param(data_vk)
      }).done(function(json2) {
        if ((json2 !== undefined) && (json2.error == undefined)) {
         var user_vk = json2.response[0];
        }
        if (user_vk == undefined) {
          user_vk = {photo_200: "http://vk.com/images/camera_b.gif",
            domain: json.user.vk_id,
            uid: 0
          }
          
        }
        console.log(user_vk);
        $scope.fighter.domain = user_vk.domain
        $scope.fighter.photo_200 = user_vk.photo_200;
        $scope.fighter.vk_id = user_vk.uid;

        $scope.$apply();
        if (callback) {
          callback();
        }
      });
    }
  }
}

//TODO phone input