<html>
<head>
  <?php
  /*убираем инфу с сессии и возвращаемся на страницу логина*/
  session_start();
  $_SESSION = array();
    setcookie ("vk_id", "", time() - 10, "/");
    setcookie ("hash", "", time() - 10, "/");
    setcookie ("photo", "", time() - 10, "/");
    setcookie ("current_group", "", time() - 10,"/");
    $_COOKIE = array();
   session_destroy();


  ?>
  <meta http-equiv="Refresh" content="0; URL=/login">
</head>
<body>
<div id="after-js-container">
  <script>
    window.location = "/login";
  </script>
</div>
</body>
</html>