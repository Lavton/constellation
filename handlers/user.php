<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
			/*если просят менять группу доступа - сделаем это!*/
			case "change_group": change_group(); break;
			case "all": get_all(); break;
		}
	}
}

//change curren group and return if successed
function change_group(){
	session_start();

	if (isset($_SESSION["groups_av"][$_POST["new_group"]])) {
		$_SESSION["current_group"] = $_POST["new_group"];
		echo json_encode(Array('result' => 'Success'));
	} else {
		echo json_encode(Array('result' => 'Fail'));
	}
}

function get_all() {
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
	/*поиск юзера*/
	$query = 'SELECT id, vk_id, name, surname, year_of_entrance FROM fighters ORDER BY id;';
	$rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
	while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
		$result["users"][$line["id"]] = $line;
    }
	$result["result"] = "Success";
	echo json_encode($result);
}

?>