<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');


if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
			case "vk_auth": vk_auth(); break;
		}
	}
}


function vk_auth(){
	require_once $_SERVER['DOCUMENT_ROOT'].'/own/passwords.php';
	$ownMd5 = md5((Passwords::$vk_app_id).((string)$_POST["uid"]).(Passwords::$vk_secret_key));
	$result = array();
	if ($ownMd5 == $_POST["hash"]) {
		$result["result"] = "Success";
		start_vk_session();
	} else {
		$result["result"] = "Fail";
	}
	session_start();
	echo json_encode($result);
}

function start_vk_session() {
	session_start();
	$_SESSION["vk_id"] = $_POST["uid"];
	$_SESSION["photo"] = $_POST["photo_rec"];
	require_once $_SERVER['DOCUMENT_ROOT'].'/own/passwords.php';
	$link = mysql_connect('127.0.0.1', 'lavton', Passwords::$db_pass)
    or die('Не удалось соединиться: ' . mysql_error());
	mysql_select_db('constellation') or die('Не удалось выбрать базу данных');
	// Выполняем SQL-запрос
	@mysql_query("Set charset utf8");
	@mysql_query("Set character_set_client = utf8");
	@mysql_query("Set character_set_connection = utf8");
	@mysql_query("Set character_set_results = utf8");
	@mysql_query("Set collation_connection = utf8_general_ci");
	// поиск юзера
	$query = 'SELECT group_of_rights FROM fighters where vk_id=\''.$_POST["uid"].'\';';
	$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
	$result = mysql_fetch_array($result, MYSQL_ASSOC);
	$_SESSION["group"] = $result["group_of_rights"];
	if (is_null($_SESSION["group"])) {
		$_SESSION["group"] = 2;
	} else {
		$_SESSION["group"] = $_SESSION["group"] + 0;
	}
	$_SESSION["current_group"] = $_SESSION["group"];
	setcookie ("vk_id", $_SESSION["vk_id"], time() + 60*60*24*100, "/");
	setcookie ("hash", $_POST["hash"], time() + 60*60*24*100, "/");
	setcookie ("photo", $_SESSION["photo"], time() + 60*60*24*100, "/");
	setcookie ("current_group", $_SESSION["current_group"], time() + 60*60*24*100, "/");
}

?>