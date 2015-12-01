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

  <div id="page-container" >
    <?php
check_session();
session_start();
if (isset($_GET["id"])) {
	include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/users/one_user.html';
}

/*не смотрим конкретный профиль*/
if (!isset($_GET["id"])) {
	/*если не кандидат, то нельзя посмотреть людей в отряде*/
	if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
		?>
    Сожалеем, но незарегистрированным пользователям нельзя просматривать список отряда. <br>
    Зарегистрируйтесь или <a href="/about/command_staff">свяжитесь с нами</a>

          <?php
} else {
		/*иначе - смотри людей)*/
		include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/users/all_users.html';
	}
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
