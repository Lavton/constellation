/*логика ангулара*/
function init_angular_f_c ($scope, $http) {
  /*инициализация*/
  $scope.fighters = [];
  window.setPeople(function(flag) {
    $scope.fighters = _.chain(window.people)
        .filter(function(person) {return person.isFighter})
        .sortBy(function(person) {return person.id})
        .map(function(person) {return _.clone(person)})
        .value();
    if (flag) {
      $scope.$apply();
    }
  });
  /*конец инициализации*/

  /*получаем информацию по-подробнее*/
  $scope.getMoreInfo = function() {
  /*сначала - данные с сервера*/
    var data =  {action: "all",};
    $http.post('/handlers/user.php', data).success(function(response) {
      _.each($scope.fighters, function(element, index, list) {
        element.photo_100 = "http://vk.com/images/camera_b.gif";
        _.extend(element, _.findWhere(response.users, {id: element.id+""}))
      });
      /*после - данные с ВК*/
      var all_vk_ids = [];
      _.each($scope.fighters, function(element, index, list) {
        all_vk_ids.push(element.vk_id);
      });
      getVkData(all_vk_ids, ["photo_100", "photo_200", "domain"], 
        function(response) {
          _.each($scope.fighters, function(element, index, list) {
            element.photo = response[element.vk_id].photo_100;
          });
          $scope.$apply();
        }
      );
    });
  }

  /*просто изменение формата вывода телефона*/
  $scope.goodView = function(tel) {
    return tel ? "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9] : ""
  }

}


/*добавить нового бойца*/
var id_and_uid = null;
$('#page-container').on('click', ".pre-add-new", function() {
  if (! $(".pre-add-new").hasClass("clicked")) {
    $(".add-new-input-w").removeClass("hidden")
    $(".pre-add-new").addClass("clicked")
    $(".pre-add-new").text("Добавить")
    var data = {action: "get_all_ids"}
    $.ajax({ //TODO: make with angular
      type: "POST",
      url: "/handlers/user.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(response) {
      id_and_uid = response.ids;
      $(".add-new-fighter-id").val(response.ids[response.ids.length-1].id*1+1);
      $(".add-new-fighter-id").addClass("own-valid")
      setInterval(function(){
        var new_val = $(".add-new-fighter-id").val()
        if (_.findWhere(response.ids, {id: new_val+""})) {
          $(".add-new-fighter-id").addClass("own-invalid")
          $(".add-new-fighter-id").removeClass("own-valid")
        } else {
          $(".add-new-fighter-id").addClass("own-valid")
          $(".add-new-fighter-id").removeClass("own-invalid")
        }
      }, 750);
    });
  } else {
    if (! getVkData($(".add-new-fighter-d").val(), ["contacts", "bdate"], 
      function(response) {
        $(".add-new-fighter-d").addClass("own-valid")
        $(".add-new-fighter-d").removeClass("own-invalid")
        if (_.findWhere(id_and_uid, {vk_id: response[$(".add-new-fighter-d").val()].uid+""})) {
          $(".add-new-fighter-d").addClass("own-invalid")
          $(".add-new-fighter-d").removeClass("own-valid")
          return;
        } else {
          $(".add-new-fighter-d").addClass("own-valid")
          $(".add-new-fighter-d").removeClass("own-invalid")
        }

        if ($(".add-new-fighter-d").hasClass("own-valid") && $(".add-new-fighter-id").hasClass("own-valid")) {
          var vk_user = response[$(".add-new-fighter-d").val()];
          var bd = [];
          if (vk_user.bdate){
            vk_user.bdate.split(".");
          }
          if (!bd[0]) {
            bd[0] = "01"
          }
          if (!bd[1]) {
            bd[1] = "01"
          }
          if (!bd[2]) {
            bd[2] = "0001"
          }
          bd = bd[2]+"-"+bd[1]+"-"+bd[0]; 
          var send_data = {
            action: "add_new_fighter", 
            id: $(".add-new-fighter-id").val()*1, 
            vk_id: vk_user.uid+"",
            name: vk_user.first_name,
            surname: vk_user.last_name,
            birthdate: bd,
            year_of_entrance: (new Date()).getFullYear(),
            group_of_rights: 3
          }
          $.ajax({ //TODO: make with angular
            type: "POST",
            url: "/handlers/user.php",
            dataType: "json",
            data:  $.param(send_data)
          }).done(function(response) {
            if (response.result == "Success") {
              window.location="/about/users/"+$(".add-new-fighter-id").val();
            }
          });
        }
      }
    )
    ) {
      $(".add-new-fighter-d").addClass("own-invalid")
      $(".add-new-fighter-d").removeClass("own-valid")
    }
  }
});



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
