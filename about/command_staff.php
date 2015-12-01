<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
?>

</head>
<body>
  <?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/menu.php';
?>

  <div id="page-container">
    <?php
check_session();
session_start();
// не смотрим конкретный профиль
if (!isset($_GET["id"])) {
		include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/users/command_staff.html';
}
?>
  </div><!-- page-container -->
<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/footer.php';
?>
<div id="after-js-container">
</div>
</body>
</html>
