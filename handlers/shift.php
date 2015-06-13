<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
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

			case 'set_new_data':set_new_data();
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
		$query = 'SELECT id, place, start_date, finish_date, time_name, visibility FROM shifts WHERE (finish_date >= CURRENT_DATE) ORDER BY start_date;';
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
		$query = 'SELECT vk_id, shift_id, fighter_id, probability FROM `guess_shift` WHERE shift_id IN (SELECT id from shifts WHERE (start_date >= CURRENT_DATE AND visibility <= ' . $_SESSION["current_group"] . '));';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["people"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			/*группируем по людям сразу*/
			if (!isset($result["people"][$line["vk_id"]])) {
				$result["people"][$line["vk_id"]] = array();
			}
			array_push($result["people"][$line["vk_id"]], $line);
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
		$query = 'SELECT guess_shift.vk_id, guess_shift.shift_id, guess_shift.fighter_id, guess_shift.probability, shifts.time_name, shifts.place FROM guess_shift, shifts WHERE ((shifts.start_date >= CURRENT_DATE) AND (shifts.id=guess_shift.shift_id)  AND (shifts.visibility <= ' . $_SESSION["current_group"] . '));';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["people"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			/*группируем по людям сразу*/
			if (!isset($result["people"][$line["vk_id"]])) {
				$result["people"][$line["vk_id"]] = array();
			}
			array_push($result["people"][$line["vk_id"]], $line);
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

		$_POST["vk_id"] = $_SESSION["vk_id"];
		$query = "SELECT vk_id, fighter_id FROM guess_shift where (shift_id=" . $_POST["id"] . " AND (like_one=" . $_POST["vk_id"] . " OR like_two=" . $_POST["vk_id"] . " OR like_three=" . $_POST["vk_id"] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["like_h"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["like_h"], $line);
		}

		$query = "SELECT * FROM guess_shift where (vk_id=" . $_POST["vk_id"] . " AND shift_id=" . $_POST["id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["myself"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
			$query = "SELECT * FROM guess_shift where (vk_id!=" . $_POST["vk_id"] . " AND shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
		} else {
			$query = "SELECT vk_id, shift_id, fighter_id, probability, social, profile, min_age, max_age, comments, cr_time FROM guess_shift where (vk_id!=" . $_POST["vk_id"] . " AND shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
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

/*возвращает нужные для имени смены вещи*/
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
		$query = "SELECT id, time_name, finish_date, place FROM shifts WHERE (id='" . $_POST['id'] . "' AND visibility <= " . $_SESSION["current_group"] . " );";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shift_name"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);

	}
}

/*возвращает инфу по смене*/
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
		$query = "SELECT * FROM shifts WHERE (id='" . $_POST['id'] . "' AND visibility <= " . $_SESSION["current_group"] . " );";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shift"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$st = "'" . $result["shift"]["start_date"] . "'";
		$query = "SELECT min(id) as mid FROM shifts where visibility <= " . $_SESSION["current_group"] . " AND (start_date > " . $st . " OR (start_date = " . $st . " AND id > " . $_POST['id'] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "SELECT max(id) as mid FROM shifts where visibility <= " . $_SESSION["current_group"] . " AND (start_date < " . $st . " OR (start_date = " . $st . " AND id < " . $_POST['id'] . "));";
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

		$_POST["vk_id"] = $_SESSION["vk_id"];

		$query = "SELECT * FROM guess_shift where (vk_id=" . $_POST["vk_id"] . " AND shift_id=" . $_POST["id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["myself"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["myself"]["likes"] = Array($result["myself"]["like_one"], $result["myself"]["like_two"], $result["myself"]["like_three"]);
		$result["myself"]["dislikes"] = Array($result["myself"]["dislike_one"], $result["myself"]["dislike_two"], $result["myself"]["dislike_three"]);

		if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == COMMAND_STAFF))) {
			$query = "SELECT * FROM guess_shift where (shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
		} else {
			$query = "SELECT vk_id, shift_id, fighter_id, probability, social, profile, min_age, max_age, cr_time FROM guess_shift where (shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["all_apply"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			$line["likes"] = Array($line["like_one"], $line["like_two"], $line["like_three"]);
			$line["dislikes"] = Array($line["dislike_one"], $line["dislike_two"], $line["dislike_three"]);
			array_push($result["all_apply"], $line);
		}

		if (($result["shift"]["visibility"] + 0) > ($_SESSION["current_group"] + 0)) {
			$result = array();
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*получает нужную инфу для добавления. А именно, кому нравится человек*/
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
		$_POST["vk_id"] = $_SESSION["vk_id"];
		$query = "SELECT vk_id, fighter_id FROM guess_shift where (shift_id=" . $_POST["id"] . " AND (like_one=" . $_POST["vk_id"] . " OR like_two=" . $_POST["vk_id"] . " OR like_three=" . $_POST["vk_id"] . "));";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["like_h"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["like_h"], $line);
		}
		$query = "SELECT vk_id FROM guess_shift where (vk_id=" . $_POST["vk_id"] . " AND shift_id=" . $_POST["id"] . ");";
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

		$query = "SELECT vk_id, shift_id  FROM guess_shift where (shift_id=" . $_POST["id"] . ") ORDER BY cr_time DESC;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["all_apply"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["all_apply"], $line);
		}
		$query = "SELECT in_id, people, comments FROM detachments WHERE (shift_id='" . $_POST['id'] . "' AND ranking IS NULL) ORDER BY in_id;";
		if (isset($_POST["edit"]) && ($_POST["edit"] == true)) {
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
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

/*изменения по смене*/
function set_new_data() {
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

		$names = array();
		$values = array();
		if (isset($_POST["place"])) {
			array_push($names, "place");
			array_push($values, "'" . $_POST["place"] . "'");
		}
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
		$query = "UPDATE shifts SET " . $conc . " WHERE id='" . $_POST['id'] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
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

		//удаляем все foreign keys
		$query = "DELETE FROM detachments WHERE shift_id=" . $_POST["id"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$query = "DELETE FROM guess_shift WHERE shift_id=" . $_POST["id"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');

		//удаляем смену по id
		$query = "DELETE FROM shifts WHERE id=" . $_POST["id"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
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
		if (isset($_POST["vk_id"])) {
			if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array('result' => "Fail"));
				return;
			}
		} else {
			$_POST["vk_id"] = $_SESSION["vk_id"];
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
		if (isset($_POST["vk_id"])) {
			array_push($names, "vk_id");
			array_push($values, "'" . $_POST["vk_id"] . "'");
		}
		if (isset($_POST["shift_id"])) {
			array_push($names, "shift_id");
			array_push($values, "'" . $_POST["shift_id"] . "'");
		}
		if (isset($_POST["prob"])) {
			array_push($names, "probability");
			array_push($values, "'" . $_POST["prob"] . "'");
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

		array_push($names, "comments");
		array_push($values, "'" . $_POST["comments"] . "'");
		foreach ($values as $key => $value) {
			if ($value == "''") {
				$values[$key] = "NULL";
			}
		}

		// cначала проверим, боец ли этот человек
		$query = "SELECT id FROM fighters where vk_id=" . $_POST["vk_id"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		if (isset($line["id"])) {
			array_push($names, "fighter_id");
			array_push($values, "'" . $line["id"] . "'");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO guess_shift (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		// $result["qw"] = $query;
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

function del_from_shift() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"])) {
		if (isset($_POST["vk_id"])) {
			if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array('result' => "Fail"));
				return;
			}
		} else {
			$_POST["vk_id"] = $_SESSION["vk_id"];
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
		$query = "DELETE FROM guess_shift WHERE (vk_id=" . $_POST["vk_id"] . " AND shift_id=" . $_POST["shift_id"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

function edit_appliing() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"])) {
		if (isset($_POST["vk_id"])) {
			if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
				echo json_encode(array('result' => "Fail"));
				return;
			}
		} else {
			$_POST["vk_id"] = $_SESSION["vk_id"];
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
		if (isset($_POST["vk_id"])) {
			array_push($names, "vk_id");
			array_push($values, "'" . $_POST["vk_id"] . "'");
		}
		if (isset($_POST["shift_id"])) {
			array_push($names, "shift_id");
			array_push($values, "'" . $_POST["shift_id"] . "'");
		}
		if (isset($_POST["prob"])) {
			array_push($names, "probability");
			array_push($values, "'" . $_POST["prob"] . "'");
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
		$query = "SELECT id FROM fighters where vk_id=" . $_POST["vk_id"] . ";";
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
		$query = "UPDATE guess_shift SET " . $conc . " WHERE (vk_id='" . $_POST['vk_id'] . "' AND shift_id=" . $_POST["shift_id"] . ");";
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
?>