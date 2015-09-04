<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) {
		//Checks if action value exists
		$action = $_POST["action"];
		switch ($action) {
			case "add_new_event":add_new_event();
				break;
			case "all":get_all();
				break;
			case "arhive":arhive();
				break;
			case "get_one_info":get_one_info();
				break;
			case "set_new_data":set_new_data();
				break;
			case "kill_event":kill_event();
				break;
			case "get_reproduct":get_reproduct();
				break;
			case "getMe":getMe();
				break;

			case "apply_to_event":apply_to_event();
				break;
			case "delete_apply_from_event": delete_apply_from_event();
				break;
		}
	}
}

function add_new_event() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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

		$names = array();
		$values = array();

		//find editor
		$query = 'SELECT id FROM fighters where vk_id=\'' . $_SESSION["vk_id"] . '\';';
		$result = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result = mysqli_fetch_array($result, MYSQL_ASSOC);
		array_push($names, "editor");
		array_push($values, $result["id"]);

		array_push($names, "name");
		array_push($values, "'" . $_POST["name"] . "'");

		array_push($names, "start_time");
		array_push($values, "'" . $_POST["start_time"] . "'");

		array_push($names, "end_time");
		array_push($values, "'" . $_POST["end_time"] . "'");

		array_push($names, "visibility");
		array_push($values, "'3'");

		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO events (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		$query = "select max(id) as id FROM events;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["id"] = $line["id"];


		// человек, создавший мероприятие всегда считается записавшимся на него
		$names = array();
		$values = array();
		array_push($names, "vk_id");
		array_push($values, "'" . $_SESSION["vk_id"] . "'");
		array_push($names, "event_id");
		array_push($values, "'" . $result["id"] . "'");
		foreach ($values as $key => $value) {
			if ($value == "''") {
				$values[$key] = "NULL";
			}
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO guess_event (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);

		echo json_encode($result);
	}
}

//get all shifts base info
function get_all() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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
		// поиск мероприятий
		$query = 'SELECT id, parent_id, name, start_time, end_time, visibility FROM events WHERE (end_time >= CURRENT_TIMESTAMP) ORDER BY start_time;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["events"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["events"], $line);
			}
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

//get arhive events base info
function arhive() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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
		// поиск мероприятий
		$query = 'SELECT * FROM events WHERE (end_time < CURRENT_TIMESTAMP AND start_time >= "' . $_POST["month"] . '-01 00:00:00") ORDER BY start_time;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["events"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["events"], $line);
			}
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// инфа об одном мероприятии
function get_one_info() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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

		// поиск мероприятия
		$query = "SELECT * FROM events WHERE id='" . $_POST['id'] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["event"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$st = "'" . $result["event"]["start_time"] . "'";
		$query = "SELECT min(id) as mid FROM events where visibility <= " . $_SESSION["current_group"] . " AND start_time > " . $st . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$query = "SELECT max(id) as mid FROM events where visibility <= " . $_SESSION["current_group"] . " AND start_time < " . $st . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		/*поиск родительского мероприятия*/
		if (isset($result["event"]["parent_id"])) {
			$query = "SELECT id, name FROM events where visibility <= " . $_SESSION["current_group"] . " AND id=" . $result["event"]["parent_id"] . ";";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["parent_event"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "SELECT id, name, surname FROM fighters WHERE id='" . $result["event"]["editor"] . "' ORDER BY id;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["event"]["editor_user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "SELECT id FROM fighters WHERE vk_id='" . $_SESSION["vk_id"] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$userId = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$userId = $userId["id"];

		$result["event"]["editable"] = canEditEvent($link, $userId, $_POST["id"]);

		// список записавшихся людей
		$query = 'SELECT * FROM guess_event WHERE (event_id='.$result["event"]["id"].');';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["event"]["users"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["event"]["users"], $line);
		}


		if (!isset($result["event"]["id"])) {
			$result = Array();
		}

		mysqli_close($link);
		echo json_encode($result);
	}
}

function set_new_data() {
	check_session();
	session_start();
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

	$query = "SELECT id FROM fighters WHERE vk_id='" . $_SESSION["vk_id"] . "';";
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$userId = mysqli_fetch_array($rt, MYSQL_ASSOC);
	$userId = $userId["id"];

	if (canEditEvent($link, $userId, $_POST["id"])) {
		$names = array();
		$values = array();

		array_push($names, "contact");
		array_push($values, "'" . $_POST["contact"] . "'");

		array_push($names, "parent_id");
		if ($_POST["parent_id"] == 0) {
			array_push($values, "NULL");
		} else {
			array_push($values, "'" . $_POST["parent_id"] . "'");
		}

		if (isset($_POST["name"])) {
			array_push($names, "name");
			array_push($values, "'" . $_POST["name"] . "'");
		}
		array_push($names, "place");
		array_push($values, "'" . $_POST["place"] . "'");
		if (isset($_POST["start_time"])) {
			array_push($names, "start_time");
			array_push($values, "'" . $_POST["start_time"] . "'");
		}
		if (isset($_POST["end_time"])) {
			array_push($names, "end_time");
			array_push($values, "'" . $_POST["end_time"] . "'");
		}
		if (isset($_POST["visibility"])) {
			array_push($names, "visibility");
			array_push($values, "'" . $_POST["visibility"] . "'");
		}
		array_push($names, "comments");
		array_push($values, "'" . $_POST["comments"] . "'");
		$conc = array();
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
		$conc = implode(", ", $conc);
		$query = "UPDATE events SET " . $conc . " WHERE id='" . $_POST['id'] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	} else {
		echo json_encode(Array('result' => 'Fail'));
	}
}

function kill_event() {
	check_session();
	session_start();
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
	$query = "SELECT id FROM fighters WHERE vk_id='" . $_SESSION["vk_id"] . "';";
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$userId = mysqli_fetch_array($rt, MYSQL_ASSOC);
	$userId = $userId["id"];

	if (canEditEvent($link, $userId, $_POST["id"])) {

		//удаляем мероприятие по id и всех потомков
		$query = "DELETE FROM events WHERE (id=" . $_POST["id"] . " OR parent_id=" . $_POST["id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

/* выдаёт всех возможных родителей - события, которые сами не дети*/
function get_reproduct() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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
		// поиск мероприятий
		$query = 'SELECT id, name, start_time, end_time FROM events WHERE (visibility <= ' . $_POST["visibility"] . ' AND parent_id IS NULL AND end_time >= CURRENT_TIMESTAMP AND start_time >= \'' . $_POST["end_time"] . '\') ORDER BY start_time;';

		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["pos_parents"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["pos_parents"], $line);
			}
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}

}

// контакты себя любимого
function getMe() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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
		// поиск мероприятий
		$query = "SELECT name, surname, vk_id, phone, second_phone FROM fighters WHERE vk_id='" . $_SESSION["vk_id"] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["me"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}

}

// проверяет, может ли человек редактировать мероприятие
function canEditEvent($link, $userId, $eventId) {
	if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) {
		return true;
	} elseif (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) {
		$query = 'SELECT id, parent_id, editor FROM events WHERE (id=' . $eventId . ');';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$event = mysqli_fetch_array($rt, MYSQL_ASSOC);
		/*Если создал мероприятие*/
		if ($event["editor"] * 1 == $userId * 1) {
			return true;
		}

		if (isset($event["parent_id"])) {
			$query = 'SELECT id, editor FROM events WHERE (id=' . $event["parent_id"] . ');';
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			$event = mysqli_fetch_array($rt, MYSQL_ASSOC);
			/*Если создал родительское мероприятие*/
			if ($event["editor"] * 1 == $userId * 1) {
				return true;
			}
		}
		return false;
	} else {
		return false;
	}
}

// записывает человека на мероприятие
function apply_to_event() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"])) {
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

		if (isset($_POST["vk_id"])) {
			$query = "SELECT id FROM fighters WHERE vk_id='" . $_SESSION["vk_id"] . "';";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			$userId = mysqli_fetch_array($rt, MYSQL_ASSOC);
			$userId = $userId["id"];

			if (!(canEditEvent($link, $userId, $_POST["event_id"]) || (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array(
					'result' => canEditEvent($link, $_SESSION["vk_id"], $_POST["event_id"]))
				);
				return;
			}
		} else {
			$_POST["vk_id"] = $_SESSION["vk_id"];
		}

		$names = array();
		$values = array();
		array_push($names, "vk_id");
		array_push($values, "'" . $_POST["vk_id"] . "'");
		if (isset($_POST["event_id"])) {
			array_push($names, "event_id");
			array_push($values, "'" . $_POST["event_id"] . "'");
		}
		foreach ($values as $key => $value) {
			if ($value == "''") {
				$values[$key] = "NULL";
			}
		}

		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO guess_event (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}


// удаляет участие
function delete_apply_from_event() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"])) {
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

		if (isset($_POST["vk_id"])) {
			$query = "SELECT id FROM fighters WHERE vk_id='" . $_SESSION["vk_id"] . "';";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			$userId = mysqli_fetch_array($rt, MYSQL_ASSOC);
			$userId = $userId["id"];

			if (!(canEditEvent($link, $userId, $_POST["event_id"]) || (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array(
					'result' => canEditEvent($link, $_SESSION["vk_id"], $_POST["event_id"]))
				);
				return;
			}
		} else {
			$_POST["vk_id"] = $_SESSION["vk_id"];
		}

		$names = array();
		$values = array();
		array_push($names, "vk_id");
		array_push($values, "'" . $_POST["vk_id"] . "'");
		if (isset($_POST["event_id"])) {
			array_push($names, "event_id");
			array_push($values, "'" . $_POST["event_id"] . "'");
		}
		foreach ($values as $key => $value) {
			if ($value == "''") {
				$values[$key] = "NULL";
			}
		}

		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "DELETE FROM guess_event WHERE (event_id=" . $_POST["event_id"] . " && vk_id=" . $_POST["vk_id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}

}
?>