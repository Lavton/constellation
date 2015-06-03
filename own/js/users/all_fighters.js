(function(){
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
      $scope.app2 = _.after(2, $scope.$apply);
      var data =  {action: "all",};
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data:  $.param(data)
      }).done(function(response) {
        _.each($scope.fighters, function(element, index, list) {
          element.photo_100 = "http://vk.com/images/camera_b.gif";
          _.extend(element, _.findWhere(response.users, {id: element.id+""}))
        });
        $scope.app2();
      });
      /*после - данные с ВК*/
      var all_vk_ids = [];
      _.each($scope.fighters, function(element, index, list) {
        all_vk_ids.push(element.uid);
      });
      getVkData(all_vk_ids, ["photo_100", "photo_200", "domain"], 
        function(response) {
          _.each($scope.fighters, function(element, index, list) {
            element.photo = response[element.uid].photo_100;
          });
          $scope.app2();
        }
      );
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
  var state = window.state.about.users.fighters.all;
  window.init_ang("fightersApp", init_angular_f_c, "all-figh");
  state.controller = "fightersApp";
  state.init_f = init_angular_f_c;
  state.element = "all-figh";
})();