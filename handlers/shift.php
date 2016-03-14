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

			case "new_rank": new_rank();
				break;
			case "save_rank_comment": save_rank_comment();
				break;
			case "del_rank": del_rank();
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
			// сколько людей записалось
			$queryt = 'SELECT SUM(1) as sm FROM EventsSupply WHERE event=' . $line["id"] . ';';
			$rt_p = mysqli_query($link, $queryt) or die('Запрос не удался: ');
			$line["common"] = mysqli_fetch_array($rt_p, MYSQL_ASSOC);
			$line["common"] = $line["common"]["sm"];
			// cколько бойцов записалось
			$queryt = 'SELECT SUM(1) as sm FROM EventsSupply AS ES 
			JOIN UsersFighters AS UF ON UF.id=ES.user
			WHERE (ES.event=' . $line["id"] . ');';
			$rt_p = mysqli_query($link, $queryt) or die('Запрос не удался: ');
			$line["common_f"] = mysqli_fetch_array($rt_p, MYSQL_ASSOC);
			$line["common_f"] = $line["common_f"]["sm"];

			if (($line["visibility"] + 0) <= ($_SESSION["current_group"] + 0)) {
				array_push($result["shifts"], $line);
			}
		}

		// /*выведем сводную таблицу по людям*/
		// $query = 'SELECT uid, shift_id, fighter_id, probability FROM `guess_shift` WHERE shift_id IN (SELECT id from shifts WHERE (start_date >= CURRENT_DATE AND visibility <= ' . $_SESSION["current_group"] . '));';
		// $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		// $result["people"] = array();

		// while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		// 	/*группируем по людям сразу*/
		// 	if (!isset($result["people"][$line["uid"]])) {
		// 		$result["people"][$line["uid"]] = array();
		// 	}
		// 	array_push($result["people"][$line["uid"]], $line);
		// }
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// все люди, которые поедут на предстоящие смены
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

		$query = "SELECT ES.id AS supp_id, ES.user, ES.event, ESS.probability,
		EM.name, EM.place, EM.start_date,
		UM.id, UM.first_name, UM.last_name, UF.id AS fighter_id
		FROM EventsSupply AS ES JOIN EventsSupplyShifts AS ESS ON ES.id=ESS.supply_id 
		JOIN EventsMain AS EM ON EM.id=ES.event
		JOIN EventsShifts AS EvS ON EvS.id=EM.id
		JOIN UsersMain AS UM ON UM.id=ES.user
		LEFT JOIN UsersFighters AS UF ON UF.id=UM.id
		WHERE ((EM.start_date >= CURRENT_DATE) AND (EM.visibility<=".$_SESSION["current_group"].")
			);";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["people"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["people"], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// архивные смены
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
		$query = 'SELECT EM.id, EM.place, EM.start_date, EM.finish_date, EM.name, EM.visibility 
		    FROM EventsMain AS EM JOIN EventsShifts AS ES ON EM.id=ES.id 
			WHERE (EM.finish_date < CURRENT_DATE AND EM.visibility<='.$_SESSION["current_group"].'
				AND EM.start_date>='.$_POST["year"].'-01-01)
			 ORDER BY EM.start_date DESC;';
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
		$query = "SELECT EventsMain.*, ES.salary 
		FROM EventsMain
		JOIN EventsShifts AS ES ON EventsMain.id = ES.id
		 WHERE (EventsMain.id='" . $_POST['id'] . "' AND EventsMain.visibility <= " . $_SESSION["current_group"] . " );";
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
		$query = "SELECT name, place, finish_date, visibility FROM EventsMain WHERE id='" . $_POST['id'] . "' ORDER BY id;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shift"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "SELECT user, event  FROM EventsSupply where (event=" . $_POST["id"] . ") ORDER BY last_updated DESC;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["all_apply"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["all_apply"], $line);
		}
		$query = "SELECT ESD.id, ESD.children_num, 
		ESD.children_discription AS children_dis, ESR.show_it, ESR.id AS ranking, 
		ESR.comments, ESDP.user, ESDP.name 
		FROM EventsShiftsDetachmentsPeople AS ESDP
		RIGHT JOIN EventsShiftsDetachments AS ESD ON ESD.id=ESDP.detachment
		RIGHT JOIN EventsShiftsRanking AS ESR ON ESR.id=ESD.ranking
		 WHERE (ESR.shift='" . $_POST['id'] . "' AND ESR.show_it=1);";

		if (isset($_POST["edit"]) && ($_POST["edit"] == true)) {
			$query = "SELECT ESD.id, ESR.show_it, ESR.id AS ranking, 
			ESR.comments, ESDP.user, ESDP.name 
			FROM EventsShiftsDetachmentsPeople AS ESDP
			RIGHT JOIN EventsShiftsDetachments AS ESD ON ESD.id=ESDP.detachment
			RIGHT JOIN EventsShiftsRanking AS ESR ON ESR.id=ESD.ranking
			 WHERE (ESR.shift='" . $_POST['id'] . "' AND ESR.show_it=0);";
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
		$result["qw"]=$query;
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
			"finish_date" => $_POST["finish_date"],"visibility" => $_POST["visibility"],
			"comments" => $_POST["comments"]));
		$result2 = updater($link, "EventsShifts", array("id" => $_POST["id"], 
			"salary" => $_POST["salary"]));

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
		// записываем в головное
		$result = inserter($link, "EventsMain", array("name" => $_POST["name"], "place" => $_POST["place"], 
			"start_date" => $_POST["start_date"], "finish_date" => $_POST["finish_date"],
			"visibility" => $_POST["visibility"], "comments" => $_POST["comments"]), True);

		// записываем в смены
		$res2 = inserter($link, "EventsShifts", array("id" => $result["id"]));

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
		if (isset($_POST["user"])) {
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
		$result = $_POST;
		mysqli_close($link);
		echo json_encode($result);
	}
}

// редактирует заявку на смену
function edit_appliing() {
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
		$query = "SELECT id FROM EventsSupply WHERE (user=".$_POST["smbdy"]." AND event=".$_POST["id"].");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result = updater($link, "EventsSupplyShifts", array("supply_id" => $line["id"],
			"probability" => $_POST["probability"], "min_age" => $_POST["min_age"],
			"max_age" => $_POST["max_age"], "comments" => $_POST["comments"]), False, "supply_id=".$line["id"]);
		$res3 = deleter($link, "EventsSupplyShiftsLikes", "supply_id=".$line["id"]);
		foreach ($_POST["likes"] as $key => $value) {
			$res = inserter($link, "EventsSupplyShiftsLikes", array("supply_id" => $line["id"],
				"other" => $value, "pole" => "1"));
		}
		foreach ($_POST["dislikes"] as $key => $value) {
			$res = inserter($link, "EventsSupplyShiftsLikes", array("supply_id" => $line["id"],
				"other" => $value, "pole" => "-1"));
		}

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
		$result = inserter($link, "EventsShiftsDetachments", array("ranking" => $_POST["ranking"]), True);

		foreach ($_POST["people"] as $key_p => $person) {
			$res = inserter($link, "EventsShiftsDetachmentsPeople", array("detachment" => $result["id"],
				"user"=> $person["user"], "name" => $person["name"]));
		}
		mysqli_close($link);
		echo json_encode($result);
	}
}

// удаляет отряд со смены
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
		$result = deleter($link, "EventsShiftsDetachments", "id=".$_POST["id"]);
		mysqli_close($link);
		echo json_encode($result);
	}
}

// редактирование отряда (состава людей)
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
		$res = deleter($link, "EventsShiftsDetachmentsPeople", "detachment=".$_POST["id"]);
		foreach ($_POST["people"] as $key_p => $person) {
			$res = inserter($link, "EventsShiftsDetachmentsPeople", array("detachment" => $_POST["id"],
				"user"=> $person["user"], "name" => $person["name"]));
		}
		$result["id"] = $_POST["id"];
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// создаёт расстановку
function new_rank() {
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
		$result = inserter($link, "EventsShiftsRanking", array("shift" => $_POST["shift"], "show_it" => $_POST["show_it"]), True);
		mysqli_close($link);
		echo json_encode($result);
	}
}

// сохранить комментарий
function save_rank_comment() {
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
		$result = updater($link, "EventsShiftsRanking", array("id" => $_POST["id"], "comments" => $_POST["comments"]));
		mysqli_close($link);
		echo json_encode($result);
	}
}

// удаляет расстановку
function del_rank() {
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
		$result = deleter($link, "EventsShiftsRanking", "id=".$_POST["id"]);
		mysqli_close($link);
		echo json_encode($result);
	}

}

// публикует расстановку
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
		// сначала проверим, нет ли опубликованных расстановок уже
		$query = "SELECT 1 FROM EventsShiftsRanking WHERE (show_it=1 AND shift=" . $_POST["shift"] . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$line = mysqli_fetch_array($rt, MYSQL_ASSOC);
		if (is_null($line)) {
			$result = updater($link, "EventsShiftsRanking", array('id' => $_POST["id"], "show_it" => 1));
		} else {
			$result["result"] = "Fail";
		}
		mysqli_close($link);
		echo json_encode($result);
	}
}

// убирает расстановку для редактирования
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
		$result = updater($link, "EventsShiftsRanking", array("show_it" => 0), False, "shift=". $_POST["shift_id"]);
		mysqli_close($link);
		echo json_encode($result);
	}
}

// вставляет информацию про детей в отряде
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
		$result = updater($link, "EventsShiftsDetachments", array("id" => $_POST["id"], 
			"children_num" => $_POST["children_num"],
			"children_discription" => $_POST["children_dis"]));
		mysqli_close($link);
		echo json_encode($result);
	}
}
?>