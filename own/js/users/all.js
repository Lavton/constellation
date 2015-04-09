'use strict';

if (window.fighters == undefined) {
  window.fighters = {}
}
if (window.fighters.angular_conroller == undefined) {
  window.fighters.angular_conroller = null;
}
function loadScript(url, callback)
{

    // Adding the script tag to the head as suggested before
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    // Then bind the event to the callback function.
    // There are several events for cross browser compatibility.
    script.onreadystatechange = callback;
    script.onload = callback;

    // Fire the loading
    if ($("#footer-js script[src='"+url+"']")[0] == undefined) {
      document.getElementById("footer-js").appendChild(script);  
    } else {
      callback();
    }
}

if (! window.fighters.all_script ) {
  window.fighters.all_script = true;
var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {

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
    var data = {user_ids: $(".add-new-fighter-d").val(), fields: ["contacts", "bdate"]}
    $.ajax({ //TODO: make with angular
      type: "GET",
      url: "https://api.vk.com/method/users.get",
      dataType: "jsonp",
      data:  $.param(data)
    }).done(function(vk_response) {
      if (vk_response.response) {
        $(".add-new-fighter-d").addClass("own-valid")
        $(".add-new-fighter-d").removeClass("own-invalid")
        if (_.findWhere(id_and_uid, {vk_id: vk_response.response[0].uid+""})) {
          $(".add-new-fighter-d").addClass("own-invalid")
          $(".add-new-fighter-d").removeClass("own-valid")
        } else {
          $(".add-new-fighter-d").addClass("own-valid")
          $(".add-new-fighter-d").removeClass("own-invalid")
        }

      } else {
        $(".add-new-fighter-d").addClass("own-invalid")
        $(".add-new-fighter-d").removeClass("own-valid")
      }

      //добавляем нового пользователя лишь если данные корректны
      if ($(".add-new-fighter-d").hasClass("own-valid") && $(".add-new-fighter-id").hasClass("own-valid")) {
        var vk_user = vk_response.response[0];
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
    });
  }
});

$('#page-container').on('click', ".get-all", function() {
  /*двойное назначение кнопки:
  Если не нажимали - добавляет таблицу со всеми бойцами и проч.*/
  if (! $(".get-all").hasClass("clicked")) {
      $(".get-all").text("ААА! УБЕРИТЕ ИХ!!!")
      $(".search_wrap").removeClass("hidden");
      $(".search_wrap input").prop("autofocus", true);

      // $(".vCard-start").addClass("unclick");
      $(".vCard-start").show("slow");
      $("table.common-contacts").removeClass("hidden");
      if (window.fighters.angular_conroller == null) {
        loadScript("/standart/js/checklist-model.js", function() {
          window.fighters.angular_conroller = angular.module('common_c_app', ["checklist-model"], function($httpProvider) { //магия, чтобы PHP понимал запрос
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
          angular.bootstrap(document, ['common_c_app']);
          window.fighters.was_init = true;
        });
      }
      if (window.fighters.was_init && isNaN(parseInt($("table.common-contacts tbody tr").attr("class")))) {
        angular.bootstrap(document, ['common_c_app']);
      }
    

  /*Если нажимали - скрывает таблицу*/
  } else {
    $(".get-all").text("а можно всех посмотреть?");
    if ($(".vCard-start").hasClass("clicked")) {
      $(".vCard-start").trigger("click");
    }
  
    $(".vCard-start").hide("slow");
    $(".search_wrap input").prop("autofocus", false);
    $(".search_wrap").addClass("hidden");
    setTimeout(function(){
      $("table.common-contacts").addClass("hidden");
    },1000)
  }
  
    $(".get-all").toggleClass("clicked");

});
      clearInterval(intID);
  }
}, 50);
}

/*логика ангулара*/
function init_angular_f_c ($scope, $http) {

  /*выбрать всех*/
  $scope.checkAll = function() {
    $scope.fighters.selected_f = angular.copy($scope.fighters);
    $(".vCard-get").prop('disabled', false);
  };
  /*убрать выбор всех*/
  $scope.uncheckAll = function() {
    $scope.fighters.selected_f = [];
    $(".vCard-get").prop('disabled', true);
  };

  /*аякс запрос методом ангулара*/
  var data =  {action: "all",};
  $http.post('/handlers/user.php', data).success(function(response) {
    window.fighters.scope = $scope
    $scope.fighters = response.users;
    $scope.fighters.selected_f = [];
    $scope.hidden_inputs = "hidden";
    $scope.hidden_ids = "";
    _.each($scope.fighters, function(element, index, list) {
      element.fi = element.name + " " + element.surname;
    });
  });

  /*либо появляются чекбоксы, либо исчезают*/
  $scope.toggleChecking = function() {
    /*Если не нажимали - делает видимыми кнопки для экспорта vCard*/
    if (!$(".vCard-start").hasClass("clicked")) {
      $(".vCard-start").text("глаза мои б никого не видели!");
      $(".vCard-get").prop('disabled', true);
      $(".vCard-get-all").show("slow");
      $(".vCard-get-none").show("slow");
      $(".vCard-get").show("slow");
      $scope.hidden_inputs = "";
      $scope.hidden_ids = "hidden";
      /*второе назначение - скрыть всё.*/
    } else {
      $(".vCard-start").html("Кого хотите посмотреть?");
      
      $(".vCard-get").hide("slow");
      $scope.hidden_inputs = "hidden";
      $scope.hidden_ids = "";
      if ($(".vCard-get").hasClass("clicked")) {
        setTimeout(function(){ //магия, без которой не работает
          $(".vCard-get").trigger("click");
          $(".vCard-get-all").hide();
          $(".vCard-get-none").hide();
          $(".search_wrap").show();
        },500)
      } else {
        $(".vCard-get-all").hide("slow");
        $(".vCard-get-none").hide("slow");
      }
    }
    $(".vCard-start").toggleClass("clicked");
  }

  /*при клике на чекбокс нужно проверить, есть ли хотя бы 1 выбранный*/
  $scope.checkClicked = function() {
    if ($scope.fighters.selected_f.length > 0) {
      $(".vCard-get").prop('disabled', false);
    } else {
      $(".vCard-get").prop('disabled', true);
    }
  }

  /*просто изменение формата вывода телефона*/
  $scope.goodView = function(tel) {
    return tel ? "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9] : ""
  }


  /*показать выбранных бойцов*/
  $scope.showSelected = function() {
    if (!$(".vCard-get").hasClass("clicked")){
      $scope.fighters.has_second = true;
      $(".vCard-get").text("Скрыть контакты");
      $(".vCard-creater").show("slow");
      $(".vCard-get-all").hide("slow");
      $(".vCard-get-none").hide("slow");
      $(".search_wrap").hide("slow");
      $("table.common-contacts").addClass("hidden")
      $("table.direct-contacts").removeClass("hidden")
      if ($scope.fighters.selected_f.photo_100 == undefined) {
        _.each($scope.fighters.selected_f, function(element, index, list) {
          element.photo_100 = "http://vk.com/images/camera_b.gif";
        });
        var ids = []
        _.each($scope.fighters.selected_f, function(element, index, list) {
          ids.push(element.id*1);
        });
        var data = {action: "get_full_info", ids: ids}
        $http.post('/handlers/user.php', data).success(function(response) {
          var user_ids = [];
          _.each($scope.fighters.selected_f, function(element, index, list) {
            var this_fighter = _.findWhere(response.users, {id: element.id});
            element.second_name = this_fighter.second_name;
            element.phone = this_fighter.phone;
            element.second_phone = this_fighter.second_phone;
            element.email = this_fighter.email;
            element.vk_id = this_fighter.vk_id;
            user_ids.push(element.vk_id);
          });

          var data2 = {user_ids: user_ids, fields: ["photo_100", "photo_200", "domain"]}
          $.ajax({ //TODO: make with angular
            type: "GET",
            url: "https://api.vk.com/method/users.get",
            dataType: "jsonp",
            data:  $.param(data2)
          }).done(function(vk_response) {
            setTimeout(function(){
            _.each($scope.fighters.selected_f, function(element, index, list) {
              var this_fighter = _.findWhere(vk_response.response, {uid: element.vk_id*1});
              element.vk_domain = this_fighter ? this_fighter.domain : ("id"+element.vk_id);
              element.photo_100 = this_fighter ? this_fighter.photo_100 : element.photo_100;
            });
            $scope.$apply();
            }, 10);

            /*генерим vCard*/
            $scope.makeCard = function() { //TODO make code more clear
              var json = response
              var json2 = vk_response
              var card = "";
              _.each(json.users, function(element, index, list) {
                var this_card = "";
                this_card += "BEGIN:VCARD\n";
                this_card += "VERSION:3.0\n";
                     
                this_card += "FN:" + element.name + " " + element.surname + "\n";
                this_card += "N:" + element.surname;
                if ((element.maiden_name != null)  && ($scope.fighters.has_second)) {
                  this_card += ","+element.maiden_name;
                }
                this_card += ";"+element.name+";";
                if ((element.second_name != null) && ($scope.fighters.has_second)) {
                  this_card += element.second_name;
                }
                this_card +=";;\n";
                
                var vk_data=_.findWhere(json2.response, {domain: element.vk_id});
                if (vk_data != undefined) { //TODO: not works
                  this_card += "PHOTO;VALUE=uri:"+vk_data.photo_200+"\n";
                }

                if (element.birthdate != null) {
                  this_card += "BDAY:"+element.birthdate+"\n";
                }

                if (element.phone != null) {
                  var tel = element.phone;
                  this_card += "TEL;TYPE=MAIN:"+
                  "+7-"+tel[0]+tel[1]+tel[2]+"-"+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+tel[8]+tel[9]+
                  "\n";
                }
                if (element.second_phone != null) {
                  var tel = element.second_phone;
                  this_card += "TEL;TYPE=CELL:"+
                  "+7-"+tel[0]+tel[1]+tel[2]+"-"+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+tel[8]+tel[9]+
                  "\n";
                }

                if (element.email != null) {
                  this_card += "EMAIL;TYPE=INTERNET:"+element.email+"\n";
                }

                if ($(".vCard-category").val() != "") {
                  this_card += "CATEGORIES:"+$(".vCard-category").val()+"\n";
                }
                this_card += "END:VCARD\n\n";
                card += this_card;
              });

              /*запись полученной строки в файл*/
              var blob = new Blob([card], {type: "text/plain;charset=utf-8"});
              saveAs(blob, "contacts.vcf");
            }

          });
        });
      }
    } else {
      $(".vCard-get").text("Показать контакты");
      $(".vCard-creater").hide("slow")
      $("table.common-contacts").removeClass("hidden")
      $("table.direct-contacts").addClass("hidden")
      $(".vCard-get-all").show("slow");
      $(".vCard-get-none").show("slow");
      $(".search_wrap").show("slow");
      $scope.fighters.selected_f = []
    }
    $(".vCard-get").toggleClass("clicked");
  }

/*если нажать ESC на 'Search' - буквы удаляться*/
$('#page-container').on('keyup', "input.search", function(event) {
  if (event.keyCode == 27) {
    $scope.$apply('query=""')
  }
});

}
