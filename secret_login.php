<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | логин</title>
  <?php include( 'own/templates/header.php'); ?>
</head>

<body>
  <?php include( 'own/templates/menu.php'); require_once $_SERVER['DOCUMENT_ROOT'].'/own/passwords.php'; ?>
  <div id="page-container">
    <div>Вы используете секретную страницу логина. Она секретна! пароль у Антона<br>
      <form id="form-local"> Ссылку на вашу страницу ВК: <input id="vk-local"> <br>
      Введите пароль: <input type="password" id="pass-local"><br>
        <input type="submit" value="Войти">
      </form>
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
    <script type="text/javascript">
    $("#form-local").submit(function(){
      event.preventDefault();
      var login = $("#vk-local").val();
      var pass = $("#pass-local").val();
      getVkData(login, ["photo_50"], 
        function(response) {
          console.log("resp", response)
          response = response[login];
          var data = {
            "action": "local_login",
            "uid": response.uid,
            "photo_rec": response.photo_50,
            "password": pass
          }
          $.ajax({
            type: "POST",
            url: "/handlers/login.php",
            dataType: "json",
            data: $.param(data)
          }).done(function(json) {
            console.log(json, data)
            window.location = "/";
          });
        })
      console.log(login, pass)
    })
    </script>

  </div>
</body>

</html>