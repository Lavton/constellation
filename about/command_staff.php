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
/*настройки своего профиля*/
if (isset($_GET["id"]) && $_GET["id"] == 0) {
	include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/users/own.php';

	/*смотрим на чужой профиль (доступно >=бойцам)*/
} elseif (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
	include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/users/one_candidate.html';
	/*не боец попытался посмотреть профиль*/
} elseif (isset($_GET["id"])) {
	echo "Access denied";
	include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/footer.php';
	echo '</div></body>
        </html>';
	exit();
}
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
