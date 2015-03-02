<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
			case "all": get_all(); break;
      case "get_one_info": get_one_info(); break;
      case 'set_new_data': set_new_data(); break;
      case "kill_shift": kill_shift(); break;
      case "add_new_shift": add_new_shift(); break;
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


function get_one_info() {
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
    $query = "SELECT * FROM shifts WHERE id='".$_POST['id']."' ORDER BY id;";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["shift"] = mysql_fetch_array($rt, MYSQL_ASSOC);
    if (($result["shift"]["visibility"]+0) > ($_SESSION["current_group"]+0)) {
      $result["shift"] = array();
    }
    // $result["q"] = $query;
    $result["result"] = "Success";
    echo json_encode($result);
  }
}

function set_new_data() {
  check_session();
  session_start();
  if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) {
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

    $names = array();
    $values = array();
    if (isset($_POST["place"])) {
      array_push($names, "place");
      array_push($values, "'".$_POST["place"]."'");
    }
    if (isset($_POST["start_date"])) {
      array_push($names, "start_date");
      array_push($values, "'".$_POST["start_date"]."'");
    }
    if (isset($_POST["finish_date"])) {
      array_push($names, "finish_date");
      array_push($values, "'".$_POST["finish_date"]."'");
    }
    if (isset($_POST["visibility"])) {
      array_push($names, "visibility");
      array_push($values, "'".$_POST["visibility"]."'");
    }
    if (isset($_POST["comments"])) {
      array_push($names, "comments");
      array_push($values, "'".$_POST["comments"]."'");
    }
    $conc = array();
    foreach ($names as $key => $value) {
      array_push($conc, "".$value."=".$values[$key]);
    }
    $conc = implode(", ", $conc);
    $query = "UPDATE shifts SET ".$conc." WHERE id='".$_POST['id']."';";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["result"] = "Success";
    $result["query"] = $query;
    echo json_encode($result);
  } else {
    echo json_encode(Array('result' => 'Fail'));
  }
}
// удаляет пользователя
function kill_shift() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
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

    //удаляем смену по id
    $query = "DELETE FROM shifts WHERE id=".$_POST["id"].";";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["result"] = "Success";
    echo json_encode($result);
  }
}

//добавляет пользователя
function add_new_shift() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
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

    $names = array();
    $values = array();
    if (isset($_POST["start_date"])) {
      array_push($names, "start_date");
      array_push($values, "'".$_POST["start_date"]."'");
    }
    if (isset($_POST["finish_date"])) {
      array_push($names, "finish_date");
      array_push($values, "'".$_POST["finish_date"]."'");
    }


    $names = implode(", ", $names);
    $values = implode(", ", $values);
    $query = "INSERT INTO shifts (".$names.") VALUES (".$values.");";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["result"] = "Success";
    $query = "select max(id) as id FROM shifts;";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $line = mysql_fetch_array($rt, MYSQL_ASSOC);
    $result["id"] = $line["id"];
    echo json_encode($result);
  }
}

?>