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
$link = mysqli_connect( 
            Passwords::$db_host,  /* Хост, к которому мы подключаемся */ 
            Passwords::$db_user,       /* Имя пользователя */ 
            Passwords::$db_pass,   /* Используемый пароль */ 
            Passwords::$db_name);     /* База данных для запросов по умолчанию */ 

if (!$link) { 
   printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error()); 
   exit; 
}    
$link->set_charset("utf8");
	// поиск юзера
	$query = 'SELECT group_of_rights FROM fighters where vk_id=\''.$_POST["uid"].'\';';
	$result = mysqli_query($link, $query) or die('Запрос не удался: ');
	$result = mysqli_fetch_array($result, MYSQL_ASSOC);
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