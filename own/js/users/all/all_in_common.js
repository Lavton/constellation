/*зарегать нового человека*/
var allPeople = {};
allPeople.addNewPerson = function(actId, actPost, initSel, path) {
  var id_and_uid = null;
  $('#page-container').on('click', ".pre-add-new", function() {
    if (!$(initSel + " .pre-add-new").hasClass("clicked") || (!$(initSel + " .add-new-d").val())) {
      $(".add-new-input-w").removeClass("hidden")
      $(".pre-add-new").addClass("clicked")
      $(".pre-add-new").text("Добавить")
      var data = {
        action: actId
      }
      $.ajax({ //TODO: make with angular
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(response) {
        if ((response.ids).length == 0) {
          (response.ids).push({
            "id": 0,
            "vk_id": "0"
          });
        }
        id_and_uid = response.ids;
        $(initSel + " .add-new-id").val(response.ids[response.ids.length - 1].id * 1 + 1);
        $(initSel + " .add-new-id").addClass("own-valid")
        setInterval(function() {
          var new_val = $(initSel + " .add-new-id").val()
          if (_.findWhere(response.ids, {
              id: new_val + ""
            })) {
            $(initSel + " .add-new-id").addClass("own-invalid")
            $(initSel + " .add-new-id").removeClass("own-valid")
          } else {
            $(initSel + " .add-new-id").addClass("own-valid")
            $(initSel + " .add-new-id").removeClass("own-invalid")
          }
        }, 750);
      });
    } else {
      if (!getVkData($(initSel + " .add-new-d").val(), ["contacts", "bdate"],
          function(response) {
            $(initSel + " .add-new-d").addClass("own-valid")
            $(initSel + " .add-new-d").removeClass("own-invalid")
            console.log(response)
            if (_.findWhere(id_and_uid, {
                vk_id: response[$(initSel + " .add-new-d").val()].uid + ""
              })) {
              $(initSel + " .add-new-d").addClass("own-invalid")
              $(initSel + " .add-new-d").removeClass("own-valid")
              return;
            } else {
              $(initSel + " .add-new-d").addClass("own-valid")
              $(initSel + " .add-new-d").removeClass("own-invalid")
            }

            if ($(initSel + " .add-new-d").hasClass("own-valid") && $(initSel + " .add-new-id").hasClass("own-valid")) {
              var vk_user = response[$(initSel + " .add-new-d").val()];
              var bd = [];
              if (vk_user.bdate) {
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
              bd = bd[2] + "-" + bd[1] + "-" + bd[0];
              var send_data = {
                action: actPost,
                id: $(initSel + " .add-new-id").val() * 1,
                vk_id: vk_user.uid + "",
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
                data: $.param(send_data)
              }).done(function(response) {
                if (response.result == "Success") {
                  window.clearPeople();
                  window.location = path + $(initSel + " .add-new-id").val();
                }
              });
            }
          }
        )) {
        $(initSel + " .add-new-d").addClass("own-invalid")
        $(initSel + " .add-new-d").removeClass("own-valid")
      }
    }

  });
}

/*получить больше информации с сервера*/
allPeople.moreFromServer = function(action, $scope, people) {
  var data = {
    "action": action,
  };
  $scope.app2 = _.after(2, $scope.$apply);
  $.ajax({
    type: "POST",
    url: "/handlers/user.php",
    dataType: "json",
    data: $.param(data)
  }).done(function(response) {
    _.each(people, function(element, index, list) {
      element.photo_100 = "http://vk.com/images/camera_b.gif";
      _.extend(element, _.findWhere(response.users, {
        id: element.id + ""
      }))
    });

    /* Если мы нашли несоответствие между закешированной версией и той, которую получили*/
    if (response.users.length != people.length) {
      window.clearPeople()
      window.setPeople(function() {
        $scope.app2()
      })
    } else {
      $scope.app2();
    }

  });

}

/*получить больше информации с VK*/
allPeople.moreFromVK = function(people, $scope) {
  var all_vk_ids = [];
  _.each(people, function(element, index, list) {
    all_vk_ids.push(element.uid);
  });
  getVkData(all_vk_ids, ["photo_100", "photo_200", "domain"],
    function(response) {
      _.each(people, function(element, index, list) {
        element.photo = response[element.uid].photo_100;
      });
      $scope.app2();
    }
  );
}
