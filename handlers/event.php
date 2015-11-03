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
			case "get_all_events":get_all_events();
				break;
			case "arhive":arhive();
				break;
			case "get_one_info":get_one_info();
				break;
			case "edit_event":edit_event();
				break;
			case "kill_event":kill_event();
				break;
			case "get_base_and_par":get_base_and_par();
				break;
			case "get_nearest_events": get_nearest_events();
				break;

			case "apply_to_event":apply_to_event();
				break;
			case "delete_apply_from_event": delete_apply_from_event();
				break;

			case "add_to_event_editors": add_to_event_editors();
				break;
			case "delete_editor_from_event": delete_editor_from_event();
				break;

			case "get_events_for_offline": get_events_for_offline();
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
		$res2 = inserter($link, "EventsEvents", array("id" => $result["id"], "contact" => $_POST["contact"], 
			"planning" => ((bool)$_POST["planning"])));
		// записываем редактирующего
		$res3 = inserter($link, "EventsEventsEditors", array("editor" => $_POST["editor"], "event" => $result["id"]));
		// автоматическая запись на мероприятие для человека, создавшего его.
		$res4 = inserter($link, "EventsSupply", array("user" => $_POST["editor"], "event" => $result["id"]));

		// Если человек сказал автоматически записать всех людей с головного
		if ($_POST["parent_id"] && ((bool)$_POST["auto_parent"])) {
			$query = "SELECT ES.user FROM EventsSupply AS ES WHERE event=".$_POST["parent_id"].";";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
				$res = inserter($link, "EventsSupply", array("user" => $line["user"], "event" => $result["id"]));
			}

		}
		mysqli_close($link);

		echo json_encode($result);
	}
}

//get all shifts base info
function get_all_events() {
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
		// поиск мероприятий
		$query = 'SELECT EM.id, EB.name AS base, EM.base_id, EM.name AS EMname, EM.start_date, EM.start_time, EM.finish_date, EM.finish_time, 
		EM.visibility, EM.parent_id AS parent_id, EvB.name AS parent_name, EE.planning
		FROM EventsMain AS EM 
		LEFT JOIN EventsBase AS EB ON EB.id=base_id 
		LEFT JOIN EventsMain AS EvB ON EM.parent_id=EvB.id 
		LEFT JOIN EventsEvents AS EE ON EE.id=EM.id
		WHERE (EM.finish_date >= CURRENT_DATE AND EM.visibility <= '.$_SESSION["current_group"].') 
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

//get arhive events base info
function arhive() {
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
		// поиск мероприятий
		$query = 'SELECT EM.id, EB.name AS base, EM.base_id, EM.name AS EMname, EM.start_date, EM.start_time, EM.finish_date, EM.finish_time, EM.visibility, EM.parent_id AS parent_id, EvB.name AS parent_name 
		FROM EventsMain AS EM 
		LEFT JOIN EventsBase AS EB ON EB.id=base_id 
		LEFT JOIN EventsMain AS EvB ON EM.parent_id=EvB.id 
		WHERE (EM.finish_date < CURRENT_DATE AND EM.start_date >= "' . $_POST["date"] . '"
			AND EM.visibility <= '.$_SESSION["current_group"].') 
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

// инфа об одном мероприятии
function get_one_info() {
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

		// поиск мероприятия
		$query = "SELECT EM.id, EM.base_id, EM.parent_id, EM.name, EM.place, EM.start_date,
		EM.start_time, EM.finish_date, EM.finish_time, EM.visibility, EM.comments, EM.last_updated, 
		EvM.name AS parent_name, EvM.start_date AS parent_date, EE.contact, EE.planning,
		EB.comments AS base_dis, EB.name AS base_name,
		ES.id AS shift_id 
		FROM EventsMain AS EM 
		LEFT JOIN EventsMain AS EvM ON EM.parent_id=EvM.id
		LEFT JOIN EventsEvents AS EE ON EE.id=EM.id
		LEFT JOIN EventsBase AS EB ON EB.id=EM.base_id
		LEFT JOIN EventsShifts AS ES ON ES.id=EM.id
		 WHERE (EM.id='" . $_POST['id'] . "');";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["event"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		// смотрим, кто может редактировать
		$query = "SELECT EEE.editor, UM.first_name, UM.last_name FROM EventsEventsEditors AS EEE
		LEFT JOIN UsersMain AS UM ON EEE.editor=UM.id
		 WHERE event=".$result["event"]["id"].";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["editors"]=array();
		$canEdit = false;
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["editors"], $line);
		}

		$userId = $_SESSION["id"]; //TODO: модифицировать потом
		$result["event"]["editable"] = canEditEvent($link, $userId, $_POST["id"]);;

		// смотрим предыдущее и следующее
		$st = "'" . $result["event"]["start_date"] . "'";
		$condition = "visibility <= " . $_SESSION["current_group"] . " 
		AND start_date > '" . $result["event"]["start_date"] . "' OR 
		(start_date='". $result["event"]["start_date"] ."' AND 
		start_time>'". $result["event"]["start_time"] ."')";
		$query = "SELECT min(id) as mid FROM EventsMain WHERE (".$condition.");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$query = "SELECT max(id) as mid FROM EventsMain WHERE (".$condition.");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		// список дочерних мероприятий
		$query = "SELECT EvM.id, EvM.name, EvM.start_date FROM EventsMain AS EvM WHERE EvM.parent_id='" . $_POST['id'] . "' ORDER BY EvM.start_date;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["children"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["children"], $line);
		}


		// список записавшихся людей
		$query = 'SELECT user FROM EventsSupply WHERE (event='.$result["event"]["id"].');';

		if ((isset($result["event"]["shift_id"])) && (!(is_null($result["event"]["shift_id"])))) {
			$query = "SELECT * FROM (SELECT BN.*, 
			UM.id, UM.uid, UM.first_name, UM.last_name, UM.middle_name
			FROM EventsMain AS EM
			JOIN
			(SELECT ESDP.user, ESR.shift, 100 AS probability, 1 AS isDet
			FROM EventsShiftsDetachmentsPeople AS ESDP
			JOIN EventsShiftsDetachments AS ESD ON ESD.id=ESDP.detachment
			JOIN EventsShiftsRanking AS ESR ON ESR.id=ESD.ranking
			WHERE (ESDP.user IS NOT NULL
			 AND ESR.show_it=1
			)
			GROUP BY ESDP.user, ESR.shift, probability, isDet
			UNION ALL
			SELECT 
				CASE 
					WHEN ESR.id IS NULL THEN ES.user
				END AS user,
				CASE 
					WHEN ESR.id IS NULL THEN ES.event
				END AS shift, 
			ESS.probability, 0 AS isDet
			FROM EventsSupply AS ES
			JOIN EventsSupplyShifts AS ESS ON ES.id=ESS.supply_id
			LEFT JOIN (
			    SELECT * FROM EventsShiftsRanking WHERE show_it=1) AS ESR ON ESR.shift=ES.event
			) AS BN ON BN.shift=EM.id
			LEFT JOIN UsersMain AS UM ON UM.id=BN.user
			) AS AllUsersShiftsConnections
			WHERE shift=".$result["event"]["id"].";";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["appliers"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["appliers"], $line);
		}

		mysqli_close($link);
		// выдаём результат только при наличии прав
		if (($result["event"]["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
			echo json_encode($result);
		}

}

// сохраняет изменения мероприятия
function edit_event() {
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
	$userId = $_SESSION["id"]; //TODO: модифицировать потом
	
	if (canEditEvent($link, $userId, $_POST["id"])) {
		// запись в главную БД мероприятий
		$result = updater($link, "EventsMain", array("id" => $_POST["id"], "base_id" => $_POST["base_id"], 
			"parent_id" => $_POST["parent_id"], "name" => $_POST["name"], "place" => $_POST["place"], 
			"start_date" => $_POST["start_date"], "start_time" => $_POST["start_time"], "finish_date" => $_POST["finish_date"], "finish_time" => $_POST["finish_time"],
			"visibility" => $_POST["visibility"], "comments" => $_POST["comments"]));

		// записываем в мероприятия
		$res2 = updater($link, "EventsEvents", array("id" => $_POST["id"], "contact" => $_POST["contact"],
			"planning" => ($_POST["planning"])));
		mysqli_close($link);
		echo json_encode($res2);
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
	
	if (canEditEvent($link, $_SESSION["id"], $_POST["id"])) {
		//удаляем мероприятие по id и всех потомков
		$result["result"] = deleter($link, "EventsMain", "id=" . $_POST["id"]);;
		mysqli_close($link);
		echo json_encode($result);
	}
}

 // выдаёт всех базовых мероприятий и возможных родителей (событий, которые сами не дети)
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
		$query = 'SELECT id, name, visibility FROM `EventsMain` WHERE parent_id IS NULL AND start_date>=CURRENT_DATE;';
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

		$query = "SELECT id, first_name, last_name, phone FROM UsersMain WHERE uid='" . $_SESSION["uid"] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["me"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		// $result["me"] = $result["me"]["first_name"]." ".$result["me"]["last_name"]." +7".$result["me"]["phone"];
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// выводит 5 ближайших мероприятий
function get_nearest_events() {
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
		$query = 'SELECT id, name, start_date FROM EventsMain 
		WHERE (start_date>=CURRENT_DATE AND visibility<='.$_SESSION["current_group"].')
		ORDER BY start_date LIMIT 0, 5;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["events"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["events"], $line);
		}
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
		$query = 'SELECT * FROM (SELECT EEE.editor FROM EventsEventsEditors AS EEE
		LEFT JOIN EventsMain AS EM ON (EEE.event=EM.id OR EEE.event=EM.parent_id)
		WHERE (EM.id=' . $eventId . ') GROUP BY EEE.editor) AS Etors WHERE Etors.editor='.$userId.';';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$user = mysqli_fetch_array($rt, MYSQL_ASSOC);
		return isset($user);
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

		if (isset($_POST["id"])) {
			if (!(canEditEvent($link, $_SESSION["id"], $_POST["event_id"]) || (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array(
					'result' => canEditEvent($link, $_SESSION["id"], $_POST["event_id"]))
				);
				return;
			}
		} else {
			$_POST["id"] = $_SESSION["id"];
		}

		$result = inserter($link, "EventsSupply", array("user" => $_POST["id"], "event" => $_POST["event_id"]));
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

		if (isset($_POST["id"])) {
			if (!(canEditEvent($link, $_SESSION["id"], $_POST["event_id"]) || (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array(
					'result' => canEditEvent($link, $_SESSION["id"], $_POST["event_id"]))
				);
				return;
			}
		} else {
			$_POST["id"] = $_SESSION["id"];
		}
		$result["result"] = deleter($link, "EventsSupply", "user=".$_POST["id"]." AND event=".$_POST["event_id"]);

		mysqli_close($link);
		echo json_encode($result);
	}
}

// добавляет человека к редакторам
function add_to_event_editors() {
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

		if (isset($_POST["id"])) {
			if (!(canEditEvent($link, $_SESSION["id"], $_POST["event_id"]) || (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array(
					'result' => canEditEvent($link, $_SESSION["id"], $_POST["event_id"]))
				);
				return;
			}
		}

		$result = inserter($link, "EventsEventsEditors", array("editor" => $_POST["id"], "event" => $_POST["event_id"]));
		mysqli_close($link);
		echo json_encode($result);
	}
}

// удаляем возможность редактирования для человека
function delete_editor_from_event() {
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

		if (isset($_POST["id"])) {
			if (!(canEditEvent($link, $_SESSION["id"], $_POST["event_id"]) || (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array(
					'result' => canEditEvent($link, $_SESSION["id"], $_POST["event_id"]))
				);
				return;
			}
		}
		$result["result"] = deleter($link, "EventsEventsEditors", "editor=".$_POST["id"]." AND event=".$_POST["event_id"]);
		mysqli_close($link);
		echo json_encode($result);
	}
}

// выдаёт список мероприятий для доступа к ним из оффлайн
function get_events_for_offline() {
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
		// берём ещё не прошедшие
		$query = 'SELECT EM.id, EB.name AS base, EM.base_id, EM.name AS EMname, EM.start_date, EM.start_time, 
		EM.finish_date, EM.finish_time, EM.place,
		EM.visibility, EM.parent_id AS parent_id, EvB.name AS parent_name, EE.planning
		FROM EventsMain AS EM 
		LEFT JOIN EventsBase AS EB ON EB.id=base_id 
		LEFT JOIN EventsMain AS EvB ON EM.parent_id=EvB.id 
		LEFT JOIN EventsEvents AS EE ON EE.id=EM.id
		WHERE (EM.finish_date >= CURRENT_DATE
			AND EM.visibility <= '.$_SESSION["current_group"].') 
		ORDER BY EM.start_date;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["events"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			$query2 = "SELECT EEE.editor FROM EventsEventsEditors AS EEE
			 WHERE event=".$line["id"].";";
			$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');
			$line["editors"]=array();
			while ($line2 = mysqli_fetch_array($rt2, MYSQL_ASSOC)) {
				array_push($line["editors"], $line2);
			}

			// список записавшихся людей
			$query2 = 'SELECT user FROM EventsSupply WHERE (event='.$line["id"].');';

			if ((isset($line["shift_id"])) && (!(is_null($line["shift_id"])))) {
				$query2 = "SELECT * FROM (SELECT BN.*, 
				UM.id, UM.uid, UM.first_name, UM.last_name, UM.middle_name
				FROM EventsMain AS EM
				JOIN
				(SELECT ESDP.user, ESR.shift, 100 AS probability, 1 AS isDet
				FROM EventsShiftsDetachmentsPeople AS ESDP
				JOIN EventsShiftsDetachments AS ESD ON ESD.id=ESDP.detachment
				JOIN EventsShiftsRanking AS ESR ON ESR.id=ESD.ranking
				WHERE (ESDP.user IS NOT NULL
				 AND ESR.show_it=1
				)
				GROUP BY ESDP.user, ESR.shift, probability, isDet
				UNION ALL
				SELECT 
					CASE 
						WHEN ESR.id IS NULL THEN ES.user
					END AS user,
					CASE 
						WHEN ESR.id IS NULL THEN ES.event
					END AS shift, 
				ESS.probability, 0 AS isDet
				FROM EventsSupply AS ES
				JOIN EventsSupplyShifts AS ESS ON ES.id=ESS.supply_id
				LEFT JOIN (
				    SELECT * FROM EventsShiftsRanking WHERE show_it=1) AS ESR ON ESR.shift=ES.event
				) AS BN ON BN.shift=EM.id
				LEFT JOIN UsersMain AS UM ON UM.id=BN.user
				) AS AllUsersShiftsConnections
				WHERE shift=".$line["id"].";";
			}
			$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');
			$line["appliers"] = array();

			while ($line2 = mysqli_fetch_array($rt2, MYSQL_ASSOC)) {
				if ($line2["user"]+0 ==$_SESSION["id"]+0) {
					$line["me_in"]=True;
				}
				array_push($line["appliers"], $line2);
			}

			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["events"], $line);
			}
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	} else {
		echo json_encode(array("users" => [], "result" => "notAuth"));
	}

}
?>