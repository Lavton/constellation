<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | логин</title>
  <?php
    include('own/templates/header.php');
  ?>


</head>
<body>
  <?php
    include('own/templates/menu.php');
  ?>

  <div id="page-container">
    <div id="auth_wrapper"><div id="vk_auth"></div></div>
  </div>
<br><br><hr>
<div><em>Если вы кандидат, и ещё этого не делали - запишите свой телефон и дату рождения в поля ниже
<br> Если вы указали что-то неправильно, попросите комсостав, чтобы он исправил данные. И уже после этого войдите через ВКонтакте <br>
</em>
<strong>Телефон:</strong> +7 <input type="number" size="10" class="tel_num"> <em>(Цифрами)</em><br>
<strong>Дата рождения:</strong> <input type="date" class="brday"><br> 
</div>
<?php
  include('own/templates/footer.php');
?>
<div id="after-js-container">
  <script type="text/javascript" src="//vk.com/js/api/openapi.js"></script>

  <script type="text/javascript">
  /*при ajax загрузке не всегда опенАПИ к этому моменту подгружается.
  Ждём, пока это не произойдёт в цикле.*/
    var intID = setInterval(function(){
      if (typeof VK !== "undefined") {
        VK.init({apiId: 4602552});
        VK.Widgets.Auth("vk_auth", {width: "300px", onAuth: function(data) {
          var odata = _.pick(data, 'uid', 'hash', 'first_name', 'last_name', 'photo_rec');
          odata.action = "vk_auth";

          if (($(".tel_num").val() != "") && ($(".brday").val() != "")) {
            var cand_data = {
              "action": "own_add_candidate",
              "vk_id": data.uid,
              "phone": $(".tel_num").val(),
              "birthdate": $(".brday").val()
            }
            console.log(cand_data)
            $.ajax({
              type: "POST",
              url: "/handlers/user.php",
              dataType: "json",
              data:  $.param(cand_data)
            }).done(function(post_json) {
              $.ajax({
                type: "POST",
                url: "/handlers/login.php",
                dataType: "json",
                data:  $.param(odata)
              }).done(function(json) {
                window.location = "/";
              });
            });
          } else {          
            $.ajax({
              type: "POST",
              url: "/handlers/login.php",
              dataType: "json",
              data:  $.param(odata)
            }).done(function(json) {
              window.location = "/";
            });
          }
        }
       });
        clearInterval(intID);
      }
    }, 50);
  </script>
</div>
</body>
</html>
