<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/own/passwords.php';
$link = mysqli_connect(
	Passwords::$db_host, /* Хост, к которому мы подключаемся */
	Passwords::$db_user, /* Имя пользователя */
	Passwords::$db_pass, /* Используемый пароль */
	Passwords::$db_name); /* База данных для запросов по умолчанию */

	if (!$link) {
		printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error());
		exit;
	}
	$query = 'DELETE FROM UsersMain WHERE 1;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$query = 'DELETE FROM EventsMain WHERE 1;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');


	$link->set_charset("utf8");
	$query = 'SELECT * FROM achievements;';
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		$names = array();
		$values = array();
		if (isset($line["id"])) {
			array_push($values, "'" . $line["id"] . "'");
			array_push($names, "id");
		}
		if (isset($line["fighter_id"])) {
			array_push($values, "'" . $line["fighter_id"] . "'");
			array_push($names, "fighter");
		}
		if (isset($line["start_year"])) {
			array_push($values, "'" . $line["start_year"] . "'");
			array_push($names, "start_year");
		}
		if (isset($line["finish_year"])) {
			array_push($values, "'" . $line["finish_year"] . "'");
			array_push($names, "finish_year");
		}
		if (isset($line["achiev"])) {
			array_push($values, "'" . $line["achiev"] . "'");
			array_push($names, "achievement");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query2 = "INSERT INTO UsersFightersAchievements (" . $names . ") VALUES (" . $values . ");";
		$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');
	}


?>