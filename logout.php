<html>
<head>
  <?php
  /*убираем инфу с сессии и возвращаемся на страницу логина*/
  session_start();
  $_SESSION = array();
  if (isset($_SERVER['HTTP_COOKIE'])) {
      $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
      foreach($cookies as $cookie) {
          $parts = explode('=', $cookie);
          $name = trim($parts[0]);
          setcookie($name, '', time()-1000);
          setcookie($name, '', time()-1000, '/');
      }
  }
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