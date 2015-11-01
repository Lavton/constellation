<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/handlers/helper.php';
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
			case "edit_base_event": edit_base_event();
				break;
			case "delete_base_event": delete_base_event();
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
		$query = 'SELECT EM.id, EM.place, EM.start_date, EM.finish_date, EM.name FROM EventsMain AS EM
		JOIN EventsShifts AS ES ON ES.id=EM.id  ORDER BY EM.start_date DESC;';
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
		$ids = join(',',$_POST["ids"]);
		// люди
		$query = "SELECT * FROM (SELECT BN.*, 
		EM.name, EM.place, EM.start_date, EM.finish_date, EM.visibility,
		UM.uid, UM.first_name, UM.last_name, UM.middle_name
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
		WHERE 
		(shift IN ($ids));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["people"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["people"], $line);
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

		// ДР людей
		$query = "SELECT UM.id, UM.uid, UM.first_name, UM.last_name, UM.birthdate FROM UsersMain AS UM";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["people"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["people"], $line);
		}


		// предстоящие смены. Если ДР выходит на них - отображаем это
		// SELECT EM.id, EM.place, EM.start_date, EM.finish_date, EM.name,
		$query = "

		shifts.id, shifts.place, shifts.start_date, shifts.finish_date, shifts.time_name, guess_shift.vk_id FROM shifts, guess_shift WHERE (shifts.finish_date >= CURRENT_DATE AND guess_shift.shift_id=shifts.id) ORDER BY start_date;";
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
		$result = inserter($link, "EventsBase", array("name" => $_POST["name"], "visibility" => $_POST["visibility"], "comments" => $_POST["comments"]), True);
	}
	echo json_encode($result);
}

// редактирует существующее базовое мероприятие
function edit_base_event() {
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
		$result = updater($link, "EventsBase", array("id" => $_POST["id"], "name" => $_POST["name"], "visibility" => $_POST["visibility"], "comments" => $_POST["comments"]));
	}
	echo json_encode($result);
}

// удаляет базовое мероприятие
function delete_base_event() {
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
		$result["result"] = deleter($link, "EventsBase", "id=" . $_POST["id"]);
		mysqli_close($link);
		echo json_encode($result);	
	}
}
?>