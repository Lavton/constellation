<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/handlers/helper.php';

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
			case "get_base_and_par":get_base_and_par();
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

// Создаёт новое мероприятие
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

		// записываем в головное
		$result = inserter($link, "EventsMain", array("base_id" => $_POST["base_id"], "parent_id" => $_POST["parent_id"], 
			"name" => $_POST["name"], "place" => $_POST["place"], "start_date" => $_POST["start_date"],
			"start_time" => $_POST["start_time"], "finish_date" => $_POST["finish_date"], "finish_time" => $_POST["finish_time"],
			"visibility" => $_POST["visibility"], "comments" => $_POST["comments"]), True);

		// записываем в мероприятия
		$res2 = inserter($link, "EventsEvents", array("id" => $result["id"], "contact" => $_POST["contact"]));
		// записываем редактирующего
		$res3 = inserter($link, "EventsEventsEditors", array("editor" => $_POST["editor"], "event" => $result["id"]));
		// автоматическая запись на мероприятие для человека, создавшего его.
		$res4 = inserter($link, "EventsSupply", array("user" => $_POST["editor"], "event" => $result["id"]));
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
		$query = 'SELECT EM.id, EB.name AS base, EM.base_id, EM.name AS EMname, EM.start_date, EM.start_time, EM.finish_date, EM.finish_time, EM.visibility, EM.parent_id AS parent_id, EvB.name AS parent_name 
		FROM EventsMain AS EM 
		LEFT JOIN EventsBase AS EB ON EB.id=base_id 
		LEFT JOIN EventsMain AS EvB ON EM.parent_id=EvB.id 
		WHERE (EM.finish_date >= CURRENT_DATE) 
		ORDER BY EM.start_date;';
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
		$query = 'SELECT EM.id, EB.name AS base, EM.base_id, EM.name AS EMname, EM.start_date, EM.start_time, EM.finish_date, EM.finish_time, EM.visibility, EM.parent_id AS parent_id, EvB.name AS parent_name 
		FROM EventsMain AS EM 
		LEFT JOIN EventsBase AS EB ON EB.id=base_id 
		LEFT JOIN EventsMain AS EvB ON EM.parent_id=EvB.id 
		WHERE (EM.finish_date < CURRENT_DATE AND EM.start_date >= "' . $_POST["month"] . '-01") 
		ORDER BY EM.start_date DESC;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: '.$query);
		$result["events"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["events"], $line);
			}
		}
		$result["result"] = "Success";
		$result["qw"] = $query;
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

/* выдаёт всех базовых мероприятий и возможных родителей (событий, которые сами не дети)*/
function get_base_and_par() {
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
		$query = 'SELECT id, name FROM `EventsMain` WHERE parent_id IS NULL AND start_date>=CURRENT_DATE;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["pos_parents"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["pos_parents"], $line);
			}
		}

		$query = 'SELECT id, name, visibility FROM EventsBase WHERE (visibility <= ' . $_SESSION["current_group"] . ');';

		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["eventsBase"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["eventsBase"], $line);
		}

		$query = "SELECT id, first_name, last_name, phone FROM UsersMain WHERE uid='" . $_SESSION["vk_id"] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["me"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		// $result["me"] = $result["me"]["first_name"]." ".$result["me"]["last_name"]." +7".$result["me"]["phone"];
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

		// говорим, что мероприятие обновилось
		$query = "UPDATE events SET lastUpdated=CURRENT_TIMESTAMP WHERE id=".$_POST["event_id"].";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

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

		// говорим, что мероприятие обновилось
		$query = "UPDATE events SET lastUpdated=CURRENT_TIMESTAMP WHERE id=".$_POST["event_id"].";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

		mysqli_close($link);
		echo json_encode($result);
	}

}
?>