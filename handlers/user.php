<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/handlers/helper.php';

if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) {
		//Checks if action value exists
		$action = $_POST["action"];
		switch ($action) {
			/*если просят менять группу доступа - сделаем это!*/
			case "change_group":change_group();
				break;
			case "get_all_more_info":get_all_more_info();
				break;
			case "add_new_person":add_new_person();
				break;

			case "get_one_info":get_one_info();
				break;
			case 'user_modify':user_modify();
				break;
			case "kill_user":kill_user();
				break;
			case "get_own_info":get_own_info(); // DELETE
				break;
				// смены и достижения
			case 'get_shifts_nd_ach':get_shifts_nd_ach();
				break;
			case "ok_edit_achv": ok_edit_achv();
				break;
			case "delete_achv": delete_achv();
				break;
			case "add_achv": add_achv();
				break;
			case "own_add_candidate":own_add_candidate();
				break;

			/*общая инфа о всех*/
			case "get_common_inf":get_common_inf(); //DELETE
				break;

			case "get_phones": get_phones();
				break;
		}
	}
}

//change current group and return if successed
function change_group() {
	check_session();
	session_start();

	if ($_SESSION["group"] >= $_POST["new_group"]) {
		$_SESSION["current_group"] = $_POST["new_group"];
		setcookie("current_group", $_SESSION["current_group"], time() + 60 * 60 * 24 * 100, "/");
		echo json_encode(Array('result' => 'Success', 'ss' => $_SESSION));
	} else {
		echo json_encode(Array('result' => 'Fail'));
	}
}

//get all users more info
function get_all_more_info() {
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
		// поиск юзера
		$query = 'SELECT  UM.id, UM.birthdate, UM.phone, UF.second_phone, UF.email, UF.instagram_id 
		FROM UsersMain AS UM
		LEFT JOIN UsersFighters AS UF ON UM.id = UF.id;';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["users"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["users"], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}
//get one user info for profile
function get_one_info() {
	check_session();
	session_start();
	// if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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

		// поиск юзера
		$query = "SELECT UM.id, UM.uid, UM.first_name, UM.last_name, IFNULL(UF.id, 0) AS isFighter, 
			IFNULL(UC.id, 0) AS isCandidate FROM UsersMain AS UM
			LEFT JOIN UsersFighters AS UF ON UF.id=UM.id
			LEFT JOIN UsersCandidats AS UC ON UC.id= UM.id
			WHERE UM.id='" . $_POST['id'] . "';";

		if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
			$query = "SELECT UM.id, UM.uid, UM.first_name, UM.last_name, UM.middle_name, UM.phone,
			UM.birthdate, UM.group_of_rights, IFNULL(UF.id, 0) AS isFighter, UF.second_phone,
			UF.email, UF.instagram_id, UF.year_of_entrance, UF.nickname, UF.maiden_name, 
			IFNULL(UC.id, 0) AS isCandidate FROM UsersMain AS UM
			LEFT JOIN UsersFighters AS UF ON UF.id=UM.id
			LEFT JOIN UsersCandidats AS UC ON UC.id= UM.id
			WHERE UM.id='" . $_POST['id'] . "';";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$query = "select min(id) as mid FROM UsersMain where id > " . $_POST['id'] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$query = "select max(id) as mid FROM UsersMain where id < " . $_POST['id'] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		// $result["q"] = $query;
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	// }
}

//change one user info in profile
function user_modify() {
	check_session();
	session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) || ($_POST["id"] == 0)) {
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
		$result = array();
		// запись базовой инфы
		// если запись себя
		if ($_POST["id"] == 0) {
			$result = updater($link, "UsersMain", array("id" => $_SESSION["id"],
				"middle_name" => $_POST["middle_name"], "phone" => $_POST["phone"], "birthdate" => $_POST["birthdate"]));
		} else {
			$result = updater($link, "UsersMain", array("id" => $_POST["id"], 
				"uid" => $_POST["uid"], "first_name" => $_POST["first_name"], "last_name" => $_POST["last_name"],
				"middle_name" => $_POST["middle_name"], "phone" => $_POST["phone"], "birthdate" => $_POST["birthdate"],
				"group_of_rights" => $_POST["group_of_rights"]));
		}
		if ($_POST["status"] != 3) {
			// если и не был бойцом, ничего не получится удалить
			$res = deleter($link, "UsersFighters",  "id=" . $_POST["id"]);
		}
		if ($_POST["status"] != 2) {
			$res = deleter($link, "UsersCandidats", "id=" . $_POST["id"]);
		}
		if ($_POST["status"] == 2) {
			if ($_POST["id"] == 0) {
				$res = inserter($link, "UsersCandidats", array("id" => $_SESSION["id"]), True, True);
			} else {
				$res = inserter($link, "UsersCandidats", array("id" => $_POST["id"]), True, True);
			}
		}
		if ($_POST["status"] == 3) {
			if ($_POST["id"] == 0) {
				$res = inserter($link, "UsersFighters", array("id" => $_SESSION["id"], 
					"nickname" => $_POST["nickname"], "second_phone" => $_POST["second_phone"], 
					"email" => $_POST["email"], "instagram_id" => $_POST["instagram_id"]), True, True);
			} else {
				$res = inserter($link, "UsersFighters", array("id" => $_POST["id"], "maiden_name" => $_POST["maiden_name"], 
					"nickname" => $_POST["nickname"], "second_phone" => $_POST["second_phone"], 
					"email" => $_POST["email"], "instagram_id" => $_POST["instagram_id"], 
					"year_of_entrance" => $_POST["year_of_entrance"]), True, True);
			}

		}
		mysqli_close($link);
		echo json_encode($result);
	} else {
		mysqli_close($link);
		echo json_encode(Array('result' => 'Fail'));
	}
}

//добавляет человека
function add_new_person() {
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
		$result = inserter($link, "UsersMain", array("uid" => $_POST["uid"], "first_name" => $_POST["first_name"],
			"last_name" => $_POST["last_name"], "middle_name" => $_POST["middle_name"],
			"phone" => $_POST["phone"], "birthdate" => $_POST["birthdate"], "group_of_rights" => $_POST["status"]), True);

		if ($_POST["status"] == 3) {
			// боец
			$res2 = inserter($link, "UsersFighters", array("id" => $result["id"], "year_of_entrance" => $_POST["year_of_entrance"]));
		}

		if ($_POST["status"] == 2) {
			// кандидат
			$res2 = inserter($link, "UsersCandidats", array("id" => $result["id"]));
		}
		mysqli_close($link);
		echo json_encode($result);
	}
}

// удаляет пользователя
function kill_user() {
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

		//удаляем бойца по id
		$result = deleter($link, "UsersMain", "id=" . $_POST["id"]);
		mysqli_close($link);
		echo json_encode($result);
	}
}

function get_own_info() {
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
	if (!(isset($_SESSION["id"]))) {

		$query = 'SELECT id FROM fighters where vk_id=\'' . $_SESSION["vk_id"] . '\';';
		$_SESSION["id"] = mysqli_query($link, $query) or die('Запрос не удался: ');
		$_SESSION["id"] = mysqli_fetch_array($_SESSION["id"], MYSQL_ASSOC);
		$_SESSION["id"] = $_SESSION["id"]["id"];
	}
	// поиск юзера
	$query = "SELECT * FROM fighters WHERE id='" . $_SESSION['fighter_id'] . "' ORDER BY id;";
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
	$result["result"] = "Success";
	// $result["session"] = $_SESSION;
	mysqli_close($link);
	echo json_encode($result);
}

/*кандидат сам вносит себя в БД со страницы с логином*/
function own_add_candidate() {
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
	$query = "SELECT 1 from candidats WHERE vk_id=" . $_POST["vk_id"] . ";";
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$check = array();
	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		array_push($check, $line);
	}
	/*если ещё нет в БД*/
	if (sizeof($check) == 0) {

		$names = array();
		$values = array();
		if (isset($_POST["vk_id"])) {
			array_push($names, "vk_id");
			array_push($values, "'" . $_POST["vk_id"] . "'");
		}
		if (isset($_POST["second_name"])) {
			array_push($names, "second_name");
			array_push($values, "'" . $_POST["second_name"] . "'");
		}
		if (isset($_POST["birthdate"])) {
			array_push($names, "birthdate");
			array_push($values, "'" . $_POST["birthdate"] . "'");
		}
		if (isset($_POST["phone"])) {
			array_push($names, "phone");
			array_push($values, "'" . $_POST["phone"] . "'");
		}

		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO candidats (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	}

	$result["result"] = "Success";
	mysqli_close($link);
	echo json_encode($result);
}

// базовая информация о пользователях. Будем её кэшировать.
function get_common_inf() {
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
		// берём всех
		$query = "SELECT UM.id, UM.uid, UM.first_name, UM.last_name, IFNULL(UF.id, 0) AS isFighter, 
		UF.nickname, UF.maiden_name, IFNULL(UC.id, 0) AS isCandidate FROM UsersMain AS UM 
		LEFT JOIN UsersFighters AS UF ON UF.id=UM.id
		LEFT JOIN UsersCandidats AS UC  ON UC.id=UM.id
		ORDER BY UM.id;";
		if ($_SESSION["current_group"] >= FIGHTER) {
			$query = "SELECT UM.id, UM.uid, UM.first_name, UM.last_name, 
			UM.phone,
			IFNULL(UF.id, 0) AS isFighter, 
			UF.nickname, UF.maiden_name, IFNULL(UC.id, 0) AS isCandidate FROM UsersMain AS UM 
			LEFT JOIN UsersFighters AS UF ON UF.id=UM.id
			LEFT JOIN UsersCandidats AS UC  ON UC.id=UM.id
			ORDER BY UM.id;";
		}

		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["users"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["users"], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	} else {
		echo json_encode(array("users" => [], "result" => "notAuth"));
	}
}

// выдаёт прошедшие смены и достижения бойца
function get_shifts_nd_ach() {
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
		// смены, на которых был боец
		$query = "SELECT * FROM (SELECT BN.*, 
			EM.name, EM.place, EM.start_date, EM.finish_date, EM.visibility
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
			) AS BN ON BN.shift=EM.id) AS AllUsersShiftsConnections
			WHERE user=".$_POST["id"].";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shifts"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["shifts"], $line);
		}

		// достижения
		$query = "SELECT * FROM UsersFightersAchievements  WHERE (fighter=".$_POST["id"].") ORDER BY start_year DESC";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["achievements"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["achievements"], $line);
		}

		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

// редактирование достижения
function ok_edit_achv() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"]) && (($_SESSION["current_group"] >= COMMAND_STAFF) || $_SESSION["id"]*1 == $_POST["fighter_id"]*1)) {
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

		if (isset($_POST["start_year"])) {
			array_push($values, "'" . $_POST["start_year"] . "'");
			array_push($names, "start_year");
		}

		if (isset($_POST["finish_year"])) {
			array_push($values, "'" . $_POST["finish_year"] . "'");
			array_push($names, "finish_year");
		}
		if (isset($_POST["achiev"])) {
			array_push($values, "'" . $_POST["achiev"] . "'");
			array_push($names, "achiev");
		}
		$conc = array();
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
		$conc = implode(", ", $conc);
		$query = "UPDATE achievements SET " . $conc . " WHERE id='" . $_POST["id"] . "';";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	} else {
		mysqli_close($link);
		echo json_encode(Array('result' => 'Fail'));
	}
}

// удалить достижение
function delete_achv() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"]) && (($_SESSION["current_group"] >= COMMAND_STAFF) || $_SESSION["id"]*1 == $_POST["fighter_id"]*1)) {
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
		$result = deleter($link, "UsersFightersAchievements", "id=" . $_POST["id"]);
		mysqli_close($link);
		echo json_encode($result);
	} else {
		mysqli_close($link);
		echo json_encode(Array('result' => 'Fail'));
	}
}


// добавляет достижение
function add_achv() {
	check_session();
	session_start();
	if (isset($_SESSION["current_group"]) && (($_SESSION["current_group"] >= COMMAND_STAFF) || $_SESSION["id"]*1 == $_POST["fighter_id"]*1)) {
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
		$result = inserter($link, "UsersFightersAchievements", array("fighter" => $_POST["fighter"],
			"start_year" => $_POST["start_year"], "finish_year" => $_POST["finish_year"], 
			"achievement" => $_POST["achievement"]), True);
		mysqli_close($link);
		echo json_encode($result);
	} else {
		mysqli_close($link);
		echo json_encode(Array('result' => 'Fail'));
	}
}


// выдаёт телефоны
function get_phones() {
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
		// поиск мероприятий
		$query = 'SELECT id, phone FROM UsersMain WHERE id IN ('.implode(", ", $_POST["ids"]).');';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["phones"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["phones"], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}
?>