<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/handlers/helper.php';

if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) {
		//Checks if action value exists
		$action = $_POST["action"];
		switch ($action) {
			case "all_shifts":all_shifts();
				break;
			case "all_people":all_people();
				break;
			case "arhive":arhive();
				break;

			case "get_one_info":get_one_info();
				break;
			case "get_one_info_name":get_one_info_name();
				break;
			case "get_one_info_shift":get_one_info_shift();
				break;
			case "get_one_info_people":get_one_info_people();
				break;
			case 'get_one_info_adding':get_one_info_adding();
				break;
			case 'get_one_detach_info':get_one_detach_info();
				break;

			case 'edit_shift':edit_shift();
				break;
			case "kill_shift":kill_shift();
				break;
			case "add_new_shift":add_new_shift();
				break;

			case "apply_to_shift":apply_to_shift();
				break;
			case "edit_appliing":edit_appliing();
				break;
			case "del_from_shift":del_from_shift();
				break;

			case "add_detachment":add_detachment();
				break;
			case "del_detach_shift":del_detach_shift();
				break;
			case "edit_detachment":edit_detachment();
				break;

			case "publish_rank":publish_rank();
				break;
			case "remove_rank":remove_rank();
				break;

			case "set_children":set_children();
				break;
		}
	}
}

//get all shifts base info
function all_shifts() {
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
		// поиск смены
		$query = 'SELECT EM.id, EM.place, EM.start_date, EM.finish_date, EM.name, EM.visibility 
			FROM EventsMain AS EM JOIN EventsShifts AS ES ON EM.id=ES.id 
			WHERE (EM.finish_date >= CURRENT_DATE AND EM.visibility<='.$_SESSION["current_group"].')
			 ORDER BY EM.start_date;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shifts"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			/*сколько людей записалось*/
			$queryt = 'SELECT SUM(1) as sm FROM guess_shift WHERE shift_id=' . $line["id"] . ';';
			$rt_p = mysqli_query($link, $queryt) or die('Запрос не удался: ');
			$line["common"] = mysqli_fetch_array($rt_p, MYSQL_ASSOC);
			$line["common"] = $line["common"]["sm"];
			/*cколько бойцов записалось*/
			$queryt = 'SELECT SUM(1) as sm FROM guess_shift WHERE (shift_id=' . $line["id"] . ' AND fighter_id IS NOT NULL);';
			$rt_p = mysqli_query($link, $queryt) or die('Запрос не удался: ');
			$line["common_f"] = mysqli_fetch_array($rt_p, MYSQL_ASSOC);
			$line["common_f"] = $line["common_f"]["sm"];

			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["shifts"], $line);
			}
		}

		/*выведем сводную таблицу по людям*/
		$query = 'SELECT uid, shift_id, fighter_id, probability FROM `guess_shift` WHERE shift_id IN (SELECT id from shifts WHERE (start_date >= CURRENT_DATE AND visibility <= ' . $_SESSION["current_group"] . '));';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["people"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			/*группируем по людям сразу*/
			if (!isset($result["people"][$line["uid"]])) {
				$result["people"][$line["uid"]] = array();
			}
			array_push($result["people"][$line["uid"]], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*все люди, которые поедут на предстоящие смены*/
function all_people() {
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
		// поиск смены
		$query = 'SELECT guess_shift.uid, guess_shift.shift_id, guess_shift.fighter_id, guess_shift.probability, shifts.time_name, shifts.place FROM guess_shift, shifts WHERE ((shifts.start_date >= CURRENT_DATE) AND (shifts.id=guess_shift.shift_id)  AND (shifts.visibility <= ' . $_SESSION["current_group"] . '));';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["people"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			/*группируем по людям сразу*/
			if (!isset($result["people"][$line["uid"]])) {
				$result["people"][$line["uid"]] = array();
			}
			array_push($result["people"][$line["uid"]], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*архивные смены*/
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
		// поиск смены
		$query = 'SELECT id, time_name, place, start_date, finish_date, visibility FROM shifts WHERE (finish_date < CURRENT_DATE AND start_date>="' . $_POST["year"] . '-01-01") ORDER BY start_date;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shifts"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["shifts"], $line);
			}
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}

}

/*depricated*/
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

		// поиск смены
		$query = "SELECT * FROM shifts WHERE id='" . $_POST['id'] . "' ORDER BY id;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shift"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$st = "'" . $result["shift"]["start_date"] . "'";
		$query = "SELECT min(id) as mid FROM shifts where visibility <= " . $_SESSION["current_group"] . " AND (start_date > " . $st . " OR (start_date = " . $st . " AND id > " . $_POST['id'] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "SELECT max(id) as mid FROM shifts where visibility <= " . $_SESSION["current_group"] . " AND (start_date < " . $st . " OR (start_date = " . $st . " AND id < " . $_POST['id'] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$_POST["uid"] = $_SESSION["uid"];
		$query = "SELECT uid, fighter_id FROM guess_shift where (shift_id=" . $_POST["id"] . " AND (like_one=" . $_POST["uid"] . " OR like_two=" . $_POST["uid"] . " OR like_three=" . $_POST["uid"] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["like_h"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["like_h"], $line);
		}

		$query = "SELECT * FROM guess_shift where (uid=" . $_POST["uid"] . " AND shift_id=" . $_POST["id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["myself"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
			$query = "SELECT * FROM guess_shift where (uid!=" . $_POST["uid"] . " AND shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
		} else {
			$query = "SELECT uid, shift_id, fighter_id, probability, social, profile, min_age, max_age, comments, cr_time FROM guess_shift where (uid!=" . $_POST["uid"] . " AND shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["all_apply"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["all_apply"], $line);
		}

		$query = "SELECT in_id, people, comments FROM detachments WHERE (shift_id='" . $_POST['id'] . "' AND ranking IS NULL) ORDER BY in_id;";
		if (isset($_POST["edit"])) {
			$query = "SELECT in_id, people, comments, ranking FROM detachments WHERE (shift_id='" . $_POST['id'] . "' AND ranking IS NOT NULL) ORDER BY in_id;";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["detachments"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["detachments"], $line);
		}
		if (($result["shift"]["visibility"] + 0) > ($_SESSION["current_group"] + 0)) {
			$result = array();
		}
		$result["qw"] = $query;
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// возвращает нужные для имени смены вещи
function get_one_info_name() {
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

		// поиск смены
		$query = "SELECT id, name, finish_date, place FROM EventsMain WHERE (id='" . $_POST['id'] . "' AND visibility <= " . $_SESSION["current_group"] . " );";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shift_name"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);

	}
}

// возвращает инфу по смене
function get_one_info_shift() {
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

		// поиск смены
		$query = "SELECT * FROM EventsMain WHERE (id='" . $_POST['id'] . "' AND visibility <= " . $_SESSION["current_group"] . " );";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shift"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$st = "'" . $result["shift"]["start_date"] . "'";
		$query = "SELECT min(EM.id) AS mid FROM EventsMain AS EM JOIN EventsShifts AS ES ON ES.id=EM.id WHERE visibility <= " . $_SESSION["current_group"] . " AND (start_date > " . $st . " OR (start_date = " . $st . " AND EM.id > " . $_POST['id'] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "SELECT max(EM.id) AS mid FROM EventsMain AS EM JOIN EventsShifts AS ES ON ES.id=EM.id WHERE visibility <= " . $_SESSION["current_group"] . " AND (start_date < " . $st . " OR (start_date = " . $st . " AND EM.id < " . $_POST['id'] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		if (($result["shift"]["visibility"] + 0) > ($_SESSION["current_group"] + 0)) {
			$result = array();
		}

		mysqli_close($link);
		echo json_encode($result);
	}
}

/*информация по записавшимся людям на смену*/
function get_one_info_people() {
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

		$_POST["uid"] = $_SESSION["uid"];

		$query = "SELECT ES.id, ES.user, ES.event, ESS.probability, ESS.min_age, ESS.max_age,
		ESS.comments FROM EventsSupply AS ES JOIN EventsSupplyShifts AS ESS ON ES.id=ESS.supply_id 
		WHERE (ES.user=".$_SESSION["id"]." AND ES.event=".$_POST["id"].");";

		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["myself"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		if (isset($result["myself"])) {
			$query = "SELECT * FROM EventsSupplyShiftsLikes WHERE supply_id=".$result["myself"]["id"].";";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			$result["myself"]["likes"] = array();
			while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
				array_push($result["myself"]["likes"], $line);
			}

		}

		if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == COMMAND_STAFF))) {
			$query = "SELECT ES.id, ES.user, ES.event, ESS.probability, ESS.min_age, ESS.max_age,
			ESS.comments FROM EventsSupply AS ES JOIN EventsSupplyShifts AS ESS ON ES.id=ESS.supply_id 
			WHERE (ES.event=".$_POST["id"].") ORDER BY last_updated DESC;";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			$result["all_apply"] = array();
			while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
				$query2 = "SELECT * FROM EventsSupplyShiftsLikes WHERE supply_id=".$line["id"].";";
				$rt2 = mysqli_query($link, $query2) or die('Запрос не удался: ');
				$line["likes"] = array();
				while ($line2 = mysqli_fetch_array($rt2, MYSQL_ASSOC)) {
					array_push($line["likes"], $line2);
				}
				array_push($result["all_apply"], $line);
			}
		} else {
			$query = "SELECT ES.id, ES.user, ES.event, ESS.probability, ESS.min_age, ESS.max_age 
			FROM EventsSupply AS ES JOIN EventsSupplyShifts AS ESS ON ES.id=ESS.supply_id 
			WHERE (ES.event=".$_POST["id"].") ORDER BY last_updated DESC;";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			$result["all_apply"] = array();
			while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
				$line["likes"] = array();
				array_push($result["all_apply"], $line);
			}
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// получает нужную инфу для добавления. А именно, кому нравится человек
function get_one_info_adding() {
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
		$_POST["uid"] = $_SESSION["uid"];
		$query = "SELECT uid, fighter_id FROM guess_shift where (shift_id=" . $_POST["id"] . " AND (like_one=" . $_POST["uid"] . " OR like_two=" . $_POST["uid"] . " OR like_three=" . $_POST["uid"] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["like_h"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["like_h"], $line);
		}
		$query = "SELECT uid FROM guess_shift where (uid=" . $_POST["uid"] . " AND shift_id=" . $_POST["id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["myself"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		if (($result["shift"]["visibility"] + 0) > ($_SESSION["current_group"] + 0)) {
			$result = array();
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*получаем расстановку(и)*/
function get_one_detach_info() {
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

		// поиск смены
		$query = "SELECT time_name, place, finish_date, visibility FROM shifts WHERE id='" . $_POST['id'] . "' ORDER BY id;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shift"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "SELECT uid, shift_id  FROM guess_shift where (shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["all_apply"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["all_apply"], $line);
		}
		$query = "SELECT in_id, people, children_num, children_dis, comments FROM detachments WHERE (shift_id='" . $_POST['id'] . "' AND ranking IS NULL) ORDER BY in_id;";
		if (isset($_POST["edit"]) && ($_POST["edit"] == true)) {
			$query = "SELECT in_id, people, children_num, children_dis, comments, ranking FROM detachments WHERE (shift_id='" . $_POST['id'] . "' AND ranking IS NOT NULL) ORDER BY in_id;";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["detachments"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["detachments"], $line);
		}
		if (($result["shift"]["visibility"] + 0) > ($_SESSION["current_group"] + 0)) {
			$result = array();
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// изменения по смене
function edit_shift() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) {
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
		$result = updater($link, "EventsMain", array("id" => $_POST["id"], 
			"place" => $_POST["place"], "name" => $_POST["name"], "start_date" => $_POST["start_date"],
			"finish_date" => $_POST["finish_date"], "visibility" => $_POST["visibility"],
			"comments" => $_POST["comments"]));
		mysqli_close($link);
		echo json_encode($result);
	} else {
		echo json_encode(Array('result' => 'Fail'));
	}
}

// удаляет смену
function kill_shift() {
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

		$result = deleter($link, "EventsMain", "id=".$_POST["id"]);
		mysqli_close($link);
		echo json_encode($result);
	}
}

//добавляет cмену
function add_new_shift() {
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

		$names = array();
		$values = array();
		if (isset($_POST["start_date"])) {
			array_push($names, "start_date");
			array_push($values, "'" . $_POST["start_date"] . "'");
		}
		if (isset($_POST["finish_date"])) {
			array_push($names, "finish_date");
			array_push($values, "'" . $_POST["finish_date"] . "'");
		}
		if (isset($_POST["time_name"])) {
			array_push($names, "time_name");
			array_push($values, "'" . $_POST["time_name"] . "'");
		}

		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO shifts (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		$query = "select max(id) as id FROM shifts;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["id"] = $line["id"];
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*запись на смену*/
function apply_to_shift() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"])) {
		if (isset($_POST["smbdy"])) {
			if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array('result' => "Fail"));
				return;
			}
		} else {
			$_POST["smbdy"] = $_SESSION["id"];
		}
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

		$result = inserter($link, "EventsSupply", array("user" => $_POST["smbdy"], 
			"event" => $_POST["id"]), True);
		$res2 = inserter($link, "EventsSupplyShifts", array("supply_id" => $result["id"],
			"probability" => $_POST["probability"], "min_age" => $_POST["min_age"],
			"max_age" => $_POST["max_age"], "comments" => $_POST["comments"]));
		$result["lks"] = array();
		foreach ($_POST["likes"] as $key => $value) {
			$res = inserter($link, "EventsSupplyShiftsLikes", array("supply_id" => $result["id"],
				"other" => $value, "pole" => "1"));
			array_push($result["lks"], $res["qw"]);
		}
		foreach ($_POST["dislikes"] as $key => $value) {
			$res = inserter($link, "EventsSupplyShiftsLikes", array("supply_id" => $result["id"],
				"other" => $value, "pole" => "-1"));
			array_push($result["lks"], $res["qw"]);
		}

		mysqli_close($link);
		echo json_encode($result);
	}
}


// удаляет заявку на смену
function del_from_shift() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"])) {
		if (isset($_POST["id"])) {
			if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array('result' => "Fail"));
				return;
			}
		} else {
			$_POST["user"] = $_SESSION["id"];
		}
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
		$result = deleter($link, "EventsSupply", "user=".$_POST["user"]." AND event=".$_POST["event"]);
		mysqli_close($link);
		echo json_encode($result);
	}
}

function edit_appliing() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"])) {
		if (isset($_POST["uid"])) {
			if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array('result' => "Fail"));
				return;
			}
		} else {
			$_POST["uid"] = $_SESSION["uid"];
		}
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
		if (isset($_POST["uid"])) {
			array_push($names, "uid");
			array_push($values, "'" . $_POST["uid"] . "'");
		}
		if (isset($_POST["shift_id"])) {
			array_push($names, "shift_id");
			array_push($values, "'" . $_POST["shift_id"] . "'");
		}
		if (isset($_POST["probability"])) {
			array_push($names, "probability");
			array_push($values, "'" . $_POST["probability"] . "'");
		}
		if (isset($_POST["social"])) {
			array_push($names, "social");
			array_push($values, "'" . $_POST["social"] . "'");
		}
		if (isset($_POST["profile"])) {
			array_push($names, "profile");
			array_push($values, "'" . $_POST["profile"] . "'");
		}

		if (isset($_POST["min_age"])) {
			array_push($names, "min_age");
			array_push($values, "'" . $_POST["min_age"] . "'");
		}
		if (isset($_POST["max_age"])) {
			array_push($names, "max_age");
			array_push($values, "'" . $_POST["max_age"] . "'");
		}

		array_push($names, "like_one");
		array_push($values, "'" . $_POST["like_one"] . "'");
		array_push($names, "like_two");
		array_push($values, "'" . $_POST["like_two"] . "'");
		array_push($names, "like_three");
		array_push($values, "'" . $_POST["like_three"] . "'");
		array_push($names, "dislike_one");
		array_push($values, "'" . $_POST["dislike_one"] . "'");
		array_push($names, "dislike_two");
		array_push($values, "'" . $_POST["dislike_two"] . "'");
		array_push($names, "dislike_three");
		array_push($values, "'" . $_POST["dislike_three"] . "'");
		foreach ($values as $key => $value) {
			if ($value == "''") {
				$values[$key] = "NULL";
			}
		}

		if (isset($_POST["comments"])) {
			array_push($names, "comments");
			array_push($values, "'" . $_POST["comments"] . "'");
		}
		array_push($names, "cr_time");
		array_push($values, "'" . date('Y-m-d H:i:s', time()) . "'");
		// cначала проверим, боец ли этот человек
		$query = "SELECT id FROM fighters where uid=" . $_POST["uid"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		if (isset($line["id"])) {
			array_push($names, "fighter_id");
			array_push($values, "'" . $line["id"] . "'");
		}
		$conc = array();
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
		$conc = implode(", ", $conc);
		$query = "UPDATE guess_shift SET " . $conc . " WHERE (uid='" . $_POST['uid'] . "' AND shift_id=" . $_POST["shift_id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}

}

function add_detachment() {
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

		$names = array();
		$values = array();
		if (isset($_POST["shift_id"])) {
			array_push($names, "shift_id");
			array_push($values, "'" . $_POST["shift_id"] . "'");
		}
		if (isset($_POST["people"])) {
			array_push($names, "people");
			array_push($values, "'" . $_POST["people"] . "'");
		}
		if (isset($_POST["comments"])) {
			array_push($names, "comments");
			array_push($values, "'" . $_POST["comments"] . "'");
		}
		if (isset($_POST["ranking"])) {
			array_push($names, "ranking");
			array_push($values, "'" . $_POST["ranking"] . "'");
		}

		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO detachments (" . $names . ") VALUES (" . $values . ");";
		// $result["qw"] = $query;
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$query = "SELECT MAX(in_id) AS in_id FROM detachments";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["in_id"] = $line["in_id"];
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

function del_detach_shift() {
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
		$query = "DELETE FROM detachments WHERE (in_id=" . $_POST["in_id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

function edit_detachment() {
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
		$names = array();
		$values = array();
		if (isset($_POST["people"])) {
			array_push($names, "people");
			array_push($values, "'" . $_POST["people"] . "'");
		}
		array_push($names, "comments");
		array_push($values, "'" . $_POST["comments"] . "'");
		$conc = array();
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
		$conc = implode(", ", $conc);
		$query = "UPDATE detachments SET " . $conc . " WHERE (in_id='" . $_POST['in_id'] . "');";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		$result["qw"] = $query;
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*публикует расстановку*/
function publish_rank() {
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
		$query = "SELECT 1 FROM detachments WHERE (ranking IS NULL AND shift_id=" . $_POST["shift_id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		if (is_null($line)) {
			$query = "UPDATE detachments SET ranking=NULL WHERE (ranking=" . $_POST["rank_id"] . " AND shift_id=" . $_POST["shift_id"] . ");";
			$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
			$result["result"] = "Success";
		} else {
			$result["result"] = "Fail";
		}
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*убирает расстановку для редактирования*/
function remove_rank() {
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
		$query = "SELECT (MAX(ranking)+1) AS MR FROM detachments WHERE shift_id=" . $_POST["shift_id"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		if (is_null($line["MR"])) {
			$line["MR"] = 1;
		}
		$query = "UPDATE detachments SET ranking=" . $line["MR"] . " WHERE (ranking IS NULL AND shift_id=" . $_POST["shift_id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		// $result["qw"] = $query;
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*вставляет информацию про детей в отряде*/
function set_children() {
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
		$names = array();
		$values = array();
		array_push($names, "children_num");
		array_push($values, "'" . $_POST["children_num"] . "'");

		array_push($names, "children_dis");
		array_push($values, "'" . $_POST["children_dis"] . "'");
		foreach ($values as $key => $value) {
			if ($value == "''") {
				$values[$key] = "NULL";
			}
		}
		$conc = array();
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
		$conc = implode(", ", $conc);
		$query = "UPDATE detachments SET " . $conc . " WHERE (in_id='" . $_POST['in_id'] . "');";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}
?>