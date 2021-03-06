<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/own/templates/php_globals.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/handlers/helper.php';
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) {
		//Checks if action value exists
		$action = $_POST["action"];
		switch ($action) {
			case "vk_auth":vk_auth();
				break;
			case "local_login":local_login();
				break;
		}
	}
}

// обычная авторизация через ВК
function vk_auth() {
	require_once $_SERVER['DOCUMENT_ROOT'] . '/own/passwords.php';
	$ownMd5 = md5((Passwords::$vk_app_id) . ((string) $_POST["uid"]) . (Passwords::$vk_secret_key));
	
	$result = array();
	if ($ownMd5 == $_POST["hash"]) {
		$result["result"] = "Success";
		start_vk_session();
		$result = $_SESSION;

	} else {
		$result["result"] = "Fail";
	}
	session_start();
	echo json_encode($result);
}

function start_vk_session() {
	session_start();
	$_SESSION["uid"] = $_POST["uid"];
	$_SESSION["photo"] = $_POST["photo_rec"];
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
	$query = "SELECT id, uid, group_of_rights from UsersMain WHERE uid=" . $_POST["uid"] . ";";
	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
	$check = array();
	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
		array_push($check, $line);
	}
	/*если ещё нет в БД*/
	if (sizeof($check) == 0) {
		$result = inserter($link, "UsersMain", array("uid" => $_POST["uid"], "first_name" => $_POST["first_name"], "last_name" => $_POST["last_name"]), True);
		$_POST["id"] = $result["id"];
	}
	$check = $check[0];
	// поиск юзера
	$_SESSION["group"] = $check["group_of_rights"];
	$_SESSION["id"] = $check["id"];
	if (is_null($_SESSION["group"])) {
		$_SESSION["group"] = 2;
	} else {
		$_SESSION["group"] = $_SESSION["group"] + 0;
	}
	$_SESSION["current_group"] = $_SESSION["group"];
	setcookie("uid", $_SESSION["uid"], time() + 60 * 60 * 24 * 100, "/");
	setcookie("hash", $_POST["hash"], time() + 60 * 60 * 24 * 100, "/");
	setcookie("photo", $_SESSION["photo"], time() + 60 * 60 * 24 * 100, "/");
	setcookie("current_group", $_SESSION["current_group"], time() + 60 * 60 * 24 * 100, "/");
	setcookie("id", $_SESSION["id"], time() + 60 * 60 * 24 * 100, "/");
	mysqli_close($link);
}


// чит. авторизация
function local_login() {
	require_once $_SERVER['DOCUMENT_ROOT'] . '/own/passwords.php';
	$result = array();
	if (Passwords::$local_pass == $_POST["password"]) {
		if ((!Passwords::$is_local) && ($_POST["uid"]*1 != "20699608")) { //uid Писаревой Вики
			$result["result"] = "Fail";
		} else {
			$result["result"] = "Success";
			start_vk_session();
		}
	} else {
		$result["result"] = "Fail";
	}
	session_start();
	echo json_encode($result);

}
?>