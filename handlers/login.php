<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');


if (is_ajax()) {
    auth_function();
}

//return data of authorization
function auth_function(){
	$return = $_POST;
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
	$query = 'SELECT * FROM users where name=\''.$_POST["username"].'\';';
	$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
	$result = mysql_fetch_array($result, MYSQL_ASSOC);
	if (isset($result["user_id"])) {
		$result["result"] = "Success";
	} else {
		$result["result"] = "Fail";
	}
	if ($result["result"] == "Success")	 {
		start_session($result);
	}
	// $result["ss"] = $_SESSION;
	echo json_encode($result);
}


function start_session($result){
	session_start();
/*поиск доступных групп доступа*/
	$_SESSION["user"] = $result["name"];
	$_SESSION["user_id"] = $result["user_id"];
	$query = 'SELECT group_id, name FROM groups WHERE group_id IN (SELECT group_id FROM users_groups WHERE user_id='.$_SESSION["user_id"].') ORDER BY group_id;';
	$rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
	while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
	    $_SESSION["groups_av"][$line["group_id"]] = $line["name"];
	    $_SESSION["current_group"] = $line["group_id"];
    }
	$query = 'SELECT * FROM fighters where id=\''.$_SESSION["user_id"].'\';';
	$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
	$result = mysql_fetch_array($result, MYSQL_ASSOC);
	$_SESSION["user_info"] = $result;
}

?>