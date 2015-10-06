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
			case "get_base_events": get_base_events();
				break;
			case "add_base_event": add_base_event();
				break;
		}
	}
}

// упрощает вставку
function inserter($link, $table, $data) {
	$names = array();
	$values = array();

	foreach ($data as $key => $value) {
		array_push($names, $key);
		array_push($values, "'" . $value . "'");
	}
	foreach ($values as $key => $value) {
		if ($value == "''") {
			$values[$key] = "NULL";
		}
	}
	$names = implode(", ", $names);
	$values = implode(", ", $values);
	$query = "INSERT INTO ".$table." (" . $names . ") VALUES (" . $values . ");";
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$result = array();
	$result["result"] = "Success";
	return $result;
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
		$query = "SELECT shifts.id, shifts.place, shifts.start_date, shifts.finish_date, shifts.time_name, guess_shift.vk_id FROM shifts, guess_shift WHERE (shifts.finish_date >= CURRENT_DATE AND guess_shift.shift_id=shifts.id) ORDER BY start_date;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shifts"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["shifts"], $line);
		}
		echo json_encode($result);
	}
}


// получаем список базовых мероприятий
function get_base_events() {
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
		$query = "SELECT * FROM EventsBase;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["events"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["events"], $line);
		}
		echo json_encode($result);
	}
}

// добавляет новое базовое мероприятие
function add_base_event() {
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
		$result = inserter($link, "EventsBase", array("name" => $_POST["name"], "visibility" => $_POST["visibility"], "comments" => $_POST["comments"]));
	}
	echo json_encode($result);
}

?>