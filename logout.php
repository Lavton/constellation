<html>
<head>
  <meta http-equiv="Refresh" content="0; URL=/login">
  <?php
	session_start();
	$_SESSION = array();
	// @unset($_COOKIE[session_name()]);
	 session_destroy();

	  exit();
  ?>
</head>
<body>

</body>
</html>