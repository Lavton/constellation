<html>
<head>
  <meta http-equiv="Refresh" content="0; URL=/login">
  <?php
  /*убираем инфу с сессии и возвращаемся на страницу логина*/
	session_start();
	$_SESSION = array();
	// @unset($_COOKIE[session_name()]);
	 session_destroy();

	  // exit();
  ?>
</head>
<body>
<div id="after-js-container">
  <script>
    window.location = "/login";
  </script>
</div>
</body>
</html>