<html>
<head></head>
<body>
<?php
	session_start();
	$_SESSION = array();
	// @unset($_COOKIE[session_name()]);
	 session_destroy();
?>
<script>
	window.location = "/login";
</script>
</body>
</html>