<?php
	//Function to check if the request is an AJAX request
	function is_ajax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_POST["ajaxLoad"]);
	}

	/*алаесы для групп доступа*/
	define("UNREG", 1);
	define("CANDIDATE", 2);
	define("FIGHTER", 3);
	define("OLD_FIGHTER", 4);
	define("EX_COMMAND_STAFF", 5);
	define("COMMAND_STAFF", 6);
	define("ADMIN", 7);

	$groups_rus = array();
	$groups_rus[1] = "Незарегистрированный пользователь";
	$groups_rus[2] = "Кандидат";
	$groups_rus[3] = "Боец";
	$groups_rus[4] = "Старик отряда";
	$groups_rus[5] = "Экс-комсостав";
	$groups_rus[6] = "Комсостав";
	$groups_rus[7] = "Администратор";

	function check_session() {
		session_start();
		if (!isset($_SESSION["vk_id"])) {
			if (isset($_COOKIE['vk_id'])) {
				session_start();
				require_once $_SERVER['DOCUMENT_ROOT'].'/own/passwords.php';
				$ownMd5 = md5((Passwords::$vk_app_id).((string)$_COOKIE["vk_id"]).(Passwords::$vk_secret_key));
				if ($ownMd5 == $_COOKIE["hash"]) {
					$_SESSION["vk_id"] = $_COOKIE["vk_id"];
					$_SESSION["photo"] = $_COOKIE["photo"];
					
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
					$query = 'SELECT id, group_of_rights FROM fighters where vk_id=\''.$_COOKIE["vk_id"].'\';';
					$result = mysqli_query($link, $query) or die('Запрос не удался: ');
					$result = mysqli_fetch_array($result, MYSQL_ASSOC);
					$_SESSION["group"] = $result["group_of_rights"];
					$_SESSION["fighter_id"] = $result["id"];
					if (is_null($_SESSION["group"])) {
						$_SESSION["group"] = 2;
					} else {
						$_SESSION["group"] = $_SESSION["group"] + 0;
					}
					$_SESSION["current_group"] = $_COOKIE["current_group"] <= $_SESSION["group"] ? $_COOKIE["current_group"] : $_SESSION["group"];
					setcookie ("vk_id", $_COOKIE["vk_id"], time() + 60*60*24*100, "/");
					setcookie ("fighter_id", $_COOKIE["fighter_id"], time() + 60*60*24*100, "/");
					setcookie ("hash", $_COOKIE["hash"], time() + 60*60*24*100, "/");
					setcookie ("photo", $_COOKIE["photo"], time() + 60*60*24*100, "/");
					setcookie ("current_group", $_COOKIE["current_group"], time() + 60*60*24*100, "/");
					mysqli_close($link);
				}
			}
		}
	}
?>