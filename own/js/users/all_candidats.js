/*логика ангулара*/
function init_angular_cand_c ($scope, $http) {

  /*инициализация*/
  $scope.candidats = [];
  window.setPeople(function(flag) {
    $scope.candidats = _.chain(window.people)
        .filter(function(person) {return person.isFighter==false})
        .sortBy(function(person) {return person.id})
        .map(function(person) {return _.clone(person)})
        .value();
      console.log($scope.candidats)
    if (flag) {
      $scope.$apply();
    }
  });
  /*конец инициализации*/

  /*получаем информацию по-подробнее*/
  $scope.getMoreInfo = function() {
  /*сначала - данные с сервера*/
    var data =  {action: "all_candidats",};
    $http.post('/handlers/user.php', data).success(function(response) {
      _.each($scope.candidats, function(element, index, list) {
        element.photo_100 = "http://vk.com/images/camera_b.gif";
        _.extend(element, _.findWhere(response.users, {id: element.id+""}))
      });
      /*после - данные с ВК*/
      var all_vk_ids = [];
      _.each($scope.candidats, function(element, index, list) {
        all_vk_ids.push(element.vk_id);
      });
      getVkData(all_vk_ids, ["photo_100", "photo_200", "domain"], 
        function(response) {
          _.each($scope.candidats, function(element, index, list) {
            element.photo = response[element.vk_id].photo_100;
          });
          $scope.$apply();
        }
      );
    });
  }

  /*инициализация*/
  /*сначала - данные с сервера*/
  // var data =  {action: "all_candidats",};
  // $http.post('/handlers/user.php', data).success(function(response) {
  //   $scope.candidats = response.users;
  //   _.each($scope.candidats, function(element, index, list) {
  //     element.fi = element.name + " " + element.surname;
  //     element.vk_domain = "id"+element.vk_id;
  //     element.photo_100 = "http://vk.com/images/camera_b.gif";
  //   });
  //   /*после - данные с ВК*/
  //   var all_vk_ids = [];
  //   _.each($scope.candidats, function(element, index, list) {
  //     all_vk_ids.push(element.vk_id);
  //   });
  //   getVkData(all_vk_ids, ["photo_100", "photo_200", "domain"], 
  //     function(response) {
  //       _.each($scope.candidats, function(element, index, list) {
  //         element.vk_domain = response[element.vk_id].domain;
  //         element.photo_100 = response[element.vk_id].photo_100;
  //         element.last_name = response[element.vk_id].last_name;
  //         element.first_name = response[element.vk_id].first_name;
  //       });
  //       $scope.$apply();
  //     }
  //   );
  // });
  /*конец инициализации*/

  /*просто изменение формата вывода телефона*/
  $scope.goodView = function(tel) {
    return tel ? "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9] : ""
  }

}


/*добавить нового кандидата*/
var id_and_uid = null;
$('#page-container').on('click', ".pre-add-new-cand", function() {
  if (! $(".pre-add-new-cand").hasClass("clicked")) {
    $(".add-new-input-cand-w").removeClass("hidden")
    $(".pre-add-new-cand").addClass("clicked")
    $(".pre-add-new-cand").text("Добавить")
    var data = {action: "get_all_candidats_ids"}
    $.ajax({ //TODO: make with angular
      type: "POST",
      url: "/handlers/user.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(response) {
      if ((response.ids).length == 0) {
        (response.ids).push({"id": 0, "vk_id": "0"});
      }
      id_and_uid = response.ids;
      $(".add-new-candidate-id").val(response.ids[response.ids.length-1].id*1+1);
      $(".add-new-candidate-id").addClass("own-valid")
      setInterval(function(){
        var new_val = $(".add-new-candidate-id").val()
        if (_.findWhere(response.ids, {id: new_val+""})) {
          $(".add-new-candidate-id").addClass("own-invalid")
          $(".add-new-candidate-id").removeClass("own-valid")
        } else {
          $(".add-new-candidate-id").addClass("own-valid")
          $(".add-new-candidate-id").removeClass("own-invalid")
        }
      }, 750);
    });
  } else {
    console.log($(".add-new-candidate-d").val());
    if (! getVkData($(".add-new-candidate-d").val(), ["contacts", "bdate"], 
      function(response) {
        $(".add-new-candidate-d").addClass("own-valid")
        $(".add-new-candidate-d").removeClass("own-invalid")
        if (_.findWhere(id_and_uid, {vk_id: response[$(".add-new-candidate-d").val()].uid+""})) {
          $(".add-new-candidate-d").addClass("own-invalid")
          $(".add-new-candidate-d").removeClass("own-valid")
          return;
        } else {
          $(".add-new-candidate-d").addClass("own-valid")
          $(".add-new-candidate-d").removeClass("own-invalid")
        }

        if ($(".add-new-candidate-d").hasClass("own-valid") && $(".add-new-candidate-id").hasClass("own-valid")) {
          var vk_user = response[$(".add-new-candidate-d").val()];
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
            action: "add_new_candidate", 
            id: $(".add-new-candidate-id").val()*1, 
            vk_id: vk_user.uid+"",
            birthdate: bd,
          }
          $.ajax({ //TODO: make with angular
            type: "POST",
            url: "/handlers/user.php",
            dataType: "json",
            data:  $.param(send_data)
          }).done(function(response) {
            if (response.result == "Success") {
              window.location="/about/candidats/"+$(".add-new-candidate-id").val();
            }
          });
        }
      }
    )
    ) {
      $(".add-new-candidate-d").addClass("own-invalid")
      $(".add-new-candidate-d").removeClass("own-valid")
    }
  }
});



'use strict';
/*магия, чтобы ангулар нормально работал*/
if (window.candidats == undefined) {
  window.candidats = {}
}
if (window.candidats.angular_conroller == undefined) {
  window.candidats.angular_conroller = null;
}
  var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {
    if ((window.location.pathname == "/about/candidats") && (window.candidats.angular_conroller == null)) {
      if (window.candidats.angular_conroller == null) {      
        window.candidats.angular_conroller = angular.module('common_candc_app', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
        window.candidats.angular_conroller.controller('candidatsApp', ['$scope', '$http', init_angular_cand_c]);
        angular.bootstrap(document, ['common_candc_app']);
        window.candidats.was_init = true;
      }
    }
    clearInterval(intID);
  }
}, 50);
