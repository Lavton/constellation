$('#page-container').on('click', ".pre-add-new-event", function() {
  var auto_date = null;
  if (! $(".pre-add-new-event").hasClass("clicked")) {
    $(".add-new-input-w").removeClass("hidden")
    $(".pre-add-new-event").addClass("clicked")
    $(".pre-add-new-event").text("Добавить")
    auto_date = setInterval(function(){
      var start_date = $(".add-new-event-start-date").val();
      var end_date = $(".add-new-event-end-date").val();
      if (end_date == "") {
        if (start_date != "") {
          $(".add-new-event-end-date").val(start_date);
        }
      } else {
        clearInterval(auto_date);
      }
    }, 750);

  } else {
    var name = $(".add-new-event-name").val();
    var start_date = $(".add-new-event-start-date").val();
    var start_time = $(".add-new-event-start-time").val();
    var end_date = $(".add-new-event-end-date").val();
    var end_time = $(".add-new-event-end-time").val();
    debugger;


  }
/*    var data = {user_ids: $(".add-new-fighter-d").val(), fields: ["contacts", "bdate"]}
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
*/});
