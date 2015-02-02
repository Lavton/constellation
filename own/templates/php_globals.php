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
					$query = 'SELECT group_of_rights FROM fighters where vk_id=\''.$_COOKIE["vk_id"].'\';';
					$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
					$result = mysql_fetch_array($result, MYSQL_ASSOC);
					$_SESSION["group"] = $result["group_of_rights"]+0 ;
					$_SESSION["current_group"] = $_COOKIE["current_group"] <= $_SESSION["group"] ? $_COOKIE["current_group"] : $_SESSION["group"];
					setcookie ("vk_id", $_SESSION["vk_id"], time() + 60*60*24*100, "/");
					setcookie ("hash", $_SESSION["hash"], time() + 60*60*24*100, "/");
					setcookie ("photo", $_SESSION["photo"], time() + 60*60*24*100, "/");
					setcookie ("current_group", $_COOKIE["current_group"], time() + 60*60*24*100, "/");
				}
			}
		}
	}
?>