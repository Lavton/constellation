function get_user_info (userid) {
	data = {action: "get_one_info", id: userid}
    $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data:  $.param(data)
      }).done(function(json) {
        console.log(json);
        var fio = json.user.name+ " ";
        if (json.user.second_name != null) {
          fio += json.user.second_name + " ";
        }
        fio += json.user.surname + " ";
        if (json.user.maiden_name != null) {
          fio += "("+json.user.maiden_name+") ";
        }

        $(".user-info h2").text(fio);


        data_vk = {user_ids: json.user.vk_id, fields: ["photo_200", "domain"]}
        $.ajax({
          type: "GET",
          url: "https://api.vk.com/method/users.get",
          dataType: "jsonp",
          data:  $.param(data_vk)
        }).done(function(json2) {
          var user_vk = json2.response[0];
          if (user_vk == undefined) {
            user_vk = {photo_200: "http://vk.com/images/camera_b.gif",
              domain: json.user.vk_id,
              uid: 0
            }
            console.log(element.surname + ' ' + element.vk_id);
          }
          console.log(user_vk);
          $(".user-info .ava").attr('src', user_vk.photo_200);
          var link = $(".user-info .info-str li.vk")
          link.removeClass('hidden').
            html(link.html() + "<a target='_blank' href='//vk.com/"+
                              user_vk.domain+"'>vk.com/"+user_vk.domain+"</a>")

          if (json.user.phone != null) {
            link = $(".user-info .info-str li.phone-first")
            var tel = json.user.phone;
            tel = "<a href='tel:+7"+tel+"'> "+
            "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9]+
            "</a>";
            link.removeClass('hidden').html(link.html() + tel)
          }
          if (json.user.second_phone != null) {
            var tel = json.user.second_phone;
            tel = "<a href='tel:+7"+tel+"'> "+
            "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9]+
            "</a>";
            link = $(".user-info .info-str li.phone-second")
            link.removeClass('hidden').html(link.html() + tel)
          }


          if (json.user.email != null) {
            var email = json.user.email;
            email = "<a href='mailto:"+email+"'>"+
              email+
              "</a>";
            link = $(".user-info .info-str li.email")
            link.removeClass('hidden').html(link.html() + email)
          }


          if (json.user.birthdate != null) {
            var birthdate = json.user.birthdate;
            link = $(".user-info .info-str li.birthdate")
            link.removeClass('hidden').html(link.html() + birthdate)
          }


          if (json.user.year_of_entrance != null) {
            var year_of_entrance = json.user.year_of_entrance;
            link = $(".user-info .info-str li.year_of_entrance")
            link.removeClass('hidden').html(link.html() + year_of_entrance)
          }


          if (json.user.group_of_rights != null) {
            var group_of_rights = json.user.group_of_rights;
            link = $(".user-info .info-str li.group_of_rights")
            link.removeClass('hidden').html(link.html() + group_of_rights)
          }


        }).fail(function(json) {
        console.log("Fail");
       });

      }).fail(function(json) {
        console.log("Fail");
      });
      
}