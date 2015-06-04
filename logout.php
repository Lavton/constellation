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
    function supports_html5_storage() {
    try {
      return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
      }
    }
    if (supports_html5_storage()) {
        delete window.people;
  window.localStorage.removeItem("people_ts");
  window.localStorage.removeItem("people");
    }
    window.location = "/login";
  </script>
</div>
</body>
</html>