//send ajax on changing radio
if (!(window.own_profile_script)) {
  window.own_profile_script = true;
$("#page-container").on('change', 'input[type=radio][name=group_r]', function() {
    data =  {new_group: this.value, action: "change_group"};
    $.ajax({
      type: "POST",
      url: "/handlers/user.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(json) {
      if (json.result == "Success") {
        /*всплывающая надпись, что всё ОК*/
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
        console.log(json)
      } else {
        alert("No.");
      }
    }).fail(function() {
      alert("Fail.");
    });
});
}

function get_own_info() {
  if (window.fighters == undefined) {
    window.fighters = {}
  }
  window.fighters.own_angular_conroller = null;
  var fid=window.location.href.split("/")
  var userid=0 //TODO сделать тут нормально!

  if (! window.fighters.own_script ) {
    window.fighters.own_script = true;
  var intID = setInterval(function(){
      var fid=window.location.href.split("/")
      var userid=0
    if ((typeof(angular) !== "undefined") && (userid != "users")) {
        if (window.fighters.own_angular_conroller == null) {
          window.fighters.own_angular_conroller = angular.module('own_c_app', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
          window.fighters.own_angular_conroller.controller('ownFighterApp', ['$scope', '$http', '$locale', init_angular_o_f_c]);
          angular.bootstrap(document, ['own_c_app']);
          window.fighters.was_init_own = true;
        
        } else {
          angular.bootstrap(document, ['own_c_app']);
        }
        clearInterval(intID);
    }
  }, 50);
  } else {
     angular.bootstrap(document, ['own_c_app']);
  }
  /*логика ангулара*/
  function init_angular_o_f_c ($scope, $http, $locale) {
    $locale.id = 'ru-ru' //TODO make it works(
    $scope.goodView = function(tel) {
      return tel ? "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9] : ""
    }
    $scope.id = userid;
    $scope.fighter = {};
    $scope.editPerson = function() {
      $(".user-info").toggleClass("hidden");
      $(".user-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.fighter); 
    };
    $(".user-info").removeClass("hidden")
     var inthrefID = setInterval(function(){
      var fid=window.location.href.split("/")
      var userid=0
      if (userid != "users") {
        clearInterval(inthrefID);
        var data = {action: "get_own_info", id: userid}
        // debugger;
        $scope.fighter.photo_200 = "http://vk.com/images/camera_b.gif"
        $.ajax({
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data:  $.param(data)
        }).done(function(json) {
          // console.log(json);
          $scope.fighter = json.user;
          $scope.fighter.year_of_entrance = 1 * $scope.fighter.year_of_entrance;
          $scope.fighter.group_of_rights = 1 * $scope.fighter.group_of_rights;
          $.getJSON("/own/group_names.json", function(group_json){
            $scope.groups = group_json;
            $scope.$apply();
          });
          $scope.fighter.domain = "id"+$scope.fighter.vk_id
          $scope.$apply();
            
          get_vk();
        });
      }
    }, 100);

    $scope.submit = function() {
      get_vk(function() {
      var data =  angular.copy($scope.fighter);
      data.action = "set_new_data"
      data.id = 0;
      _.each(data, function(element, index, list){
        if (!element) {
          data[index] = null;
        }
      })
      $http.post('/handlers/user.php', data).success(function(response) {
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
        console.log(response)
      });      
      });
    }
    $scope.resetInfo = function() {
      $scope.fighter = angular.copy($scope.master);
    }


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