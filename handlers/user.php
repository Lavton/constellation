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
			case "get_all_ids":get_all_ids();
				break;
			case "add_new_person":add_new_person();
				break;

			case "get_one_info":get_one_info();
				break;
			case 'fighter_modify':fighter_modify();
				break;
			case "kill_user":kill_user();
				break;
			case "get_own_info":get_own_info();
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

			/*то, что относится ко всем кандидатам*/
			case "get_all_candidats_ids":get_all_candidats_ids();
				break;
			case "add_new_candidate":add_new_candidate();
				break;
			case "all_candidats":all_candidats();
				break;

			/*1 кандидат*/
			case "get_one_candidate_info":get_one_candidate_info();
				break;
			case "set_new_cand_data":set_new_cand_data();
				break;
			case "kill_candidate":kill_candidate();
				break;

			case "own_add_candidate":own_add_candidate();
				break;

			/*общая инфа о всех*/
			case "get_common_inf":get_common_inf();
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
function fighter_modify() {
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
		$names = array();
		$values = array();
		if ($_POST["id"] != 0) {
			if (isset($_POST["uid"])) {
				array_push($values, "'" . $_POST["uid"] . "'");
				array_push($names, "vk_id");
			}
			if (isset($_POST["group_of_rights"])) {
				array_push($values, "'" . $_POST["group_of_rights"] . "'");
				array_push($names, "group_of_rights");
			}
			if (isset($_POST["first_name"])) {
				array_push($values, "'" . $_POST["first_name"] . "'");
				array_push($names, "name");
			}
			if (isset($_POST["last_name"])) {
				array_push($values, "'" . $_POST["last_name"] . "'");
				array_push($names, "surname");
			}
			if (isset($_POST["maiden_name"])) {
				array_push($values, "'" . $_POST["maiden_name"] . "'");
				array_push($names, "maiden_name");
			}
			if (isset($_POST["year_of_entrance"])) {
				array_push($values, "'" . $_POST["year_of_entrance"] . "'");
				array_push($names, "year_of_entrance");
			}
		}
		if (isset($_POST["second_name"])) {
			array_push($values, "'" . $_POST["second_name"] . "'");
			array_push($names, "second_name");
		}

		if (isset($_POST["phone"])) {
			array_push($values, "'" . $_POST["phone"] . "'");
			array_push($names, "phone");
		}
		if (isset($_POST["second_phone"])) {
			array_push($values, "'" . $_POST["second_phone"] . "'");
			array_push($names, "second_phone");
		}
		if (isset($_POST["email"])) {
			array_push($values, "'" . $_POST["email"] . "'");
			array_push($names, "email");
		}
		array_push($values, "'" . $_POST["Instagram_id"] . "'");
		array_push($names, "Instagram_id");

		if (isset($_POST["birthdate"])) {
			array_push($values, "'" . $_POST["birthdate"] . "'");
			array_push($names, "birthdate");
		}
		$conc = array();
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
		$conc = implode(", ", $conc);
		$query = "";
		if ($_POST["id"] == 0) {
			if (!(isset($_SESSION["fighter_id"]))) {

				$query = 'SELECT id FROM fighters where vk_id=\'' . $_SESSION["vk_id"] . '\';';
				$_SESSION["fighter_id"] = mysqli_query($link, $query) or die('Запрос не удался: ');
				$_SESSION["fighter_id"] = mysqli_fetch_array($_SESSION["fighter_id"], MYSQL_ASSOC);
				$_SESSION["fighter_id"] = $_SESSION["fighter_id"]["id"];

			}
			$query = "UPDATE fighters SET " . $conc . " WHERE id='" . $_SESSION['fighter_id'] . "';";
		} else {
			$query = "UPDATE fighters SET " . $conc . " WHERE id='" . $_POST['id'] . "';";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		//	$result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["result"] = "Success";
		// $result["query"] = $query;
		mysqli_close($link);
		echo json_encode($result);
	} else {
		mysqli_close($link);
		echo json_encode(Array('result' => 'Fail'));
	}
}

//get list of existing ids and vk_ids
function get_all_ids() {
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

		// поиск юзера
		$query = "SELECT id, vk_id FROM fighters ORDER BY id;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["ids"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["ids"], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
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
	if (!(isset($_SESSION["fighter_id"]))) {

		$query = 'SELECT id FROM fighters where vk_id=\'' . $_SESSION["vk_id"] . '\';';
		$_SESSION["fighter_id"] = mysqli_query($link, $query) or die('Запрос не удался: ');
		$_SESSION["fighter_id"] = mysqli_fetch_array($_SESSION["fighter_id"], MYSQL_ASSOC);
		$_SESSION["fighter_id"] = $_SESSION["fighter_id"]["id"];
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

function get_all_candidats_ids() {
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

		// поиск юзера
		$query = "SELECT id, vk_id FROM candidats ORDER BY id;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["ids"] = array();
		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["ids"], $line);
		}
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

//добавляет кандидата
function add_new_candidate() {
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

		//добавляем id и vk_id
		// $query = "INSERT INTO fighters (id, vk_id) VALUES (".$_POST["id"].", '".$_POST["vk_id"]."');";

		$names = array();
		$values = array();
		if (isset($_POST["id"])) {
			array_push($names, "id");
			array_push($values, "'" . $_POST["id"] . "'");
		}
		if (isset($_POST["vk_id"])) {
			array_push($names, "vk_id");
			array_push($values, "'" . $_POST["vk_id"] . "'");
		}
		if (isset($_POST["birthdate"])) {
			array_push($names, "birthdate");
			array_push($values, "'" . $_POST["birthdate"] . "'");
		}
		$names = implode(", ", $names);
		$values = implode(", ", $values);
		$query = "INSERT INTO candidats (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

function all_candidats() {
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
		$query = 'SELECT id, vk_id, birthdate, phone, second_name FROM candidats ORDER BY id;';
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

function get_one_candidate_info() {
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
		$query = "SELECT * FROM candidats WHERE id='" . $_POST['id'] . "' ORDER BY id;";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		$query = "select min(id) as mid FROM candidats where id > " . $_POST['id'] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$query = "select max(id) as mid FROM candidats where id < " . $_POST['id'] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

		// $result["q"] = $query;
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}
}

function set_new_cand_data() {
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
		$names = array();
		$values = array();
		if ($_POST["id"] != 0) {
			if (isset($_POST["vk_id"])) {
				array_push($names, "vk_id");
				array_push($values, "'" . $_POST["vk_id"] . "'");
			}
		}
		if (isset($_POST["second_name"])) {
			array_push($names, "second_name");
			array_push($values, "'" . $_POST["second_name"] . "'");
		}
		if (isset($_POST["phone"])) {
			array_push($names, "phone");
			array_push($values, "'" . $_POST["phone"] . "'");
		}
		if (isset($_POST["birthdate"])) {
			array_push($names, "birthdate");
			array_push($values, "'" . $_POST["birthdate"] . "'");
		}
		$conc = array();
		foreach ($names as $key => $value) {
			array_push($conc, "" . $value . "=" . $values[$key]);
		}
		$conc = implode(", ", $conc);
		$query = "";
		if ($_POST["id"] == 0) {
			if (!(isset($_SESSION["fighter_id"]))) {

				$query = 'SELECT id FROM candidats where vk_id=\'' . $_SESSION["vk_id"] . '\';';
				$_SESSION["fighter_id"] = mysqli_query($link, $query) or die('Запрос не удался: ');
				$_SESSION["fighter_id"] = mysqli_fetch_array($_SESSION["fighter_id"], MYSQL_ASSOC);
				$_SESSION["fighter_id"] = $_SESSION["fighter_id"]["id"];

			}
			$query = "UPDATE candidats SET " . $conc . " WHERE id='" . $_SESSION['fighter_id'] . "';";
		} else {
			$query = "UPDATE candidats SET " . $conc . " WHERE id='" . $_POST['id'] . "';";
		}
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		//  $result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
		$result["result"] = "Success";
		// $result["query"] = $query;
		mysqli_close($link);
		echo json_encode($result);
	} else {
		mysqli_close($link);
		echo json_encode(Array('result' => 'Fail'));
	}
}

/*удаляет кандидата из БД*/
function kill_candidate() {
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
		$query = "DELETE FROM candidats WHERE id=" . $_POST["id"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	}

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
		$query = 'SELECT detachments.shift_id as id, shifts.place, shifts.finish_date, shifts.time_name FROM detachments, shifts WHERE (ranking IS NULL AND people LIKE \'%'.$_POST["uid"].'%\' AND shifts.id=detachments.shift_id) ORDER BY shifts.finish_date DESC';
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["shifts"] = array();

		while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
			array_push($result["shifts"], $line);
		}

		// достижения
		$query = 'SELECT * FROM achievements WHERE (fighter_id='.$_POST["fighter_id"].') ORDER BY start_year DESC';
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
	if (isset($_SESSION["current_group"]) && (($_SESSION["current_group"] >= COMMAND_STAFF) || $_SESSION["fighter_id"]*1 == $_POST["fighter_id"]*1)) {
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
	if (isset($_SESSION["current_group"]) && (($_SESSION["current_group"] >= COMMAND_STAFF) || $_SESSION["fighter_id"]*1 == $_POST["fighter_id"]*1)) {
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

		$query = "DELETE FROM achievements WHERE id=" . $_POST["id"] . ";";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
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
	if (isset($_SESSION["current_group"]) && (($_SESSION["current_group"] >= COMMAND_STAFF) || $_SESSION["fighter_id"]*1 == $_POST["fighter_id"]*1)) {
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
		
		if (isset($_POST["fighter_id"])) {
			array_push($values, "'" . $_POST["fighter_id"] . "'");
			array_push($names, "fighter_id");
		}

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

		$names = implode(", ", $names);
		$values = implode(", ", $values);

		$query = "INSERT INTO achievements (" . $names . ") VALUES (" . $values . ");";
		$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
		$result["result"] = "Success";
		mysqli_close($link);
		echo json_encode($result);
	} else {
		mysqli_close($link);
		echo json_encode(Array('result' => 'Fail'));
	}
}
?>