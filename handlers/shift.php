<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
			case "all": get_all(); break;
		}
	}
}

//get all users base info
function get_all() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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
  	$query = 'SELECT id, place, start_date, finish_date, visibility FROM shifts ORDER BY start_date;';
  	$rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
  	$result["shifts"] = array();

  	while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
      if (($line["visibility"]+0) <= ($_SESSION["current_group"]+0)) {
    		array_push($result["shifts"], $line);
      }
    }
  	$result["result"] = "Success";
  	echo json_encode($result);
  }
}

?>