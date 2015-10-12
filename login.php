<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | логин</title>
  <?php include( 'own/templates/header.php'); ?>
</head>

<body>
  <?php include( 'own/templates/menu.php'); ?>
  <div id="page-container">
    <div id="auth_wrapper">
      <div id="vk_auth"></div>
    </div>
    <br>
    <br>
    <hr>
    <div><em>Если вы <u><b>КАНДИДАТ</b></u>, и ещё этого не делали - запишите своё отчество, телефон и дату рождения в поля ниже.
    <br><br> После заполнения формы нажмите <u>"войти через ВКонтакте"</u> и ваши данные будут отправлены <br>
Если вы указали что-то неправильно, попросите комсостав, чтобы он исправил данные. <br>

    </em>
      <strong>Отчество:</strong>
      <input class="second_n">
      <br>
      <strong>Телефон:</strong> +7
      <input type="number" size="10" class="tel_num"> <em>(Цифрами)</em>
      <br>
      <strong>Дата рождения:</strong>
      <input type="date" class="brday"> <i> В формате 'гггг-мм-дд'</i>
      <br>
    </div>
  </div>
  <?php include( 'own/templates/footer.php'); ?>
  <div id="after-js-container">
    <script type="text/javascript" src="//vk.com/js/api/openapi.js"></script>
    <script type="text/javascript">
    window.clearPeople();

    /*при ajax загрузке не всегда опенАПИ к этому моменту подгружается.
      Ждём, пока это не произойдёт в цикле.*/
    var intID = setInterval(function() {
      if (typeof VK !== "undefined") {
        VK.init({
          apiId: 4602552
        });
        VK.Widgets.Auth("vk_auth", {
          width: "300px",
          onAuth: function(data) {
            var odata = _.pick(data, 'uid', 'hash', 'first_name', 'last_name', 'photo_rec');
            odata.action = "vk_auth";

            if (($(".tel_num").val()) && ($(".brday").val())) {
              if (confirm("отчество: " + $(".second_n").val() + "\nтелефон: " + window.goodTelephoneView($(".tel_num").val()) + "\nдень рождения: " + $(".brday").val())) {

                var cand_data = {
                  "action": "own_add_candidate",
                  "vk_id": data.uid,
                  "second_name": $(".second_n").val(),
                  "phone": $(".tel_num").val(),
                  "birthdate": $(".brday").val()
                }
                console.log(cand_data)
                $.ajax({
                  type: "POST",
                  url: "/handlers/user.php",
                  dataType: "json",
                  data: $.param(cand_data)
                }).done(function(post_json) {
                  $.ajax({
                    type: "POST",
                    url: "/handlers/login.php",
                    dataType: "json",
                    data: $.param(odata)
                  }).done(function(json) {
                    console.log(json)
                    window.location = "/";
                  });
                });
              }
            } else {
              console.log("NOOOOOOOO", odata)
              $.ajax({
                type: "POST",
                url: "/handlers/login.php",
                dataType: "json",
                data: $.param(odata)
              }).done(function(json) {
                console.log(json)
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