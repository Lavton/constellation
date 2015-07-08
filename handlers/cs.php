<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) {
		//Checks if action value exists
		$action = $_POST["action"];
		switch ($action) {
			case "shifts":shifts();
				break;
			case "get_people": get_people(); 
				break;
			case "get_birthdays": get_birthdays();
				break;
		}
	}
}

//отображает список смен для дальнейшего выбора действий
function shifts() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
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
		$link->set_charset("utf8");
		// поиск смены
		$query = 'SELECT id, place, start_date, finish_date, time_name FROM shifts ORDER BY start_date DESC;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shifts"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			/*сколько людей записалось*/
			array_push($result["shifts"], $line);
		}
	}
	echo json_encode($result);
}

// все люди, записавшиеся или едущие на смену.
function get_people() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
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
		$link->set_charset("utf8");
		// люди
		$ids = join(',',$_POST["ids"]);
		// возможно поедут
		$query = "SELECT vk_id, probability, shift_id FROM guess_shift WHERE (shift_id IN ($ids));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["guesses"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["guesses"], $line);
		}

		// уже съездили
		$query = "SELECT people, shift_id FROM detachments WHERE (shift_id IN ($ids) AND ranking IS NULL);";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["detachments"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["detachments"], $line);
		}
		echo json_encode($result);
	}
}

// ищем дни рождения
function get_birthdays() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
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
		$link->set_charset("utf8");

		// ДР бойцов
		$query = "SELECT id, vk_id, name, surname, birthdate FROM fighters";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["fighters"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["fighters"], $line);
		}

		// ДР кандидатов
		$query = "SELECT id, vk_id, birthdate FROM candidats";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["candidats"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["candidats"], $line);
		}

		// предстоящие смены. Если ДР выходит на них - отображаем это
		$query = "SELECT id, place, start_date, finish_date, time_name FROM shifts WHERE (finish_date >= CURRENT_DATE) ORDER BY start_date;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shifts"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["shifts"], $line);
		}
		echo json_encode($result);
	}
}
?>