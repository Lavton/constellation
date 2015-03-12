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
      case "apply_to_shift": apply_to_shift(); break;
      case "del_from_shift": del_from_shift(); break;
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

    // поиск смены
    $query = "SELECT * FROM shifts WHERE id='".$_POST['id']."' ORDER BY id;";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["shift"] = mysql_fetch_array($rt, MYSQL_ASSOC);
    $st = "'".$result["shift"]["start_date"]."'";
    $query = "SELECT min(id) as mid FROM shifts where visibility <= ".$_SESSION["current_group"]." AND (start_date > ".$st." OR (start_date = ".$st." AND id > ".$_POST['id']."));";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["next"] = mysql_fetch_array($rt, MYSQL_ASSOC);

    $query = "SELECT max(id) as mid FROM shifts where visibility <= ".$_SESSION["current_group"]." AND (start_date < ".$st." OR (start_date = ".$st." AND id < ".$_POST['id']."));";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["prev"] = mysql_fetch_array($rt, MYSQL_ASSOC);

    $_POST["vk_id"] = $_SESSION["vk_id"];
    $query = "SELECT vk_id, fighter_id FROM guess_shift where (like_one=".$_POST["vk_id"]." OR like_two=".$_POST["vk_id"]." OR like_three=".$_POST["vk_id"].");";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["like_h"] = array();
    while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
      array_push($result["like_h"], $line);
    }

    $query = "SELECT * FROM guess_shift where (vk_id=".$_POST["vk_id"]." AND shift_id=".$_POST["id"].");";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["myself"] = mysql_fetch_array($rt, MYSQL_ASSOC);

    if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
      $query = "SELECT * FROM guess_shift where (vk_id!=".$_POST["vk_id"]." AND shift_id=".$_POST["id"].") ORDER BY cr_time DESC;";
    } else {
      $query = "SELECT vk_id, shift_id, fighter_id, probability, social, profile, min_age, max_age, comments, cr_time FROM guess_shift where (vk_id!=".$_POST["vk_id"]." AND shift_id=".$_POST["id"].") ORDER BY cr_time DESC;";
    }
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["all_apply"] = array();
    while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
      array_push($result["all_apply"], $line);
    }
    if (($result["shift"]["visibility"]+0) > ($_SESSION["current_group"]+0)) {
      $result["shift"] = array();
    }

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

function apply_to_shift() {
  check_session();
  session_start();
  if (isset($_SESSION["current_group"])) {
    if (isset($_POST["vk_id"])) { 
      if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
        echo json_encode(array('result' =>  "Fail"));
        return;
      }
    } else {
      $_POST["vk_id"] = $_SESSION["vk_id"];
    }
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
    if (isset($_POST["vk_id"])) {
      array_push($names, "vk_id");
      array_push($values, "'".$_POST["vk_id"]."'");
    }
    if (isset($_POST["shift_id"])) {
      array_push($names, "shift_id");
      array_push($values, "'".$_POST["shift_id"]."'");
    }
    if (isset($_POST["prob"])) {
      array_push($names, "probability");
      array_push($values, "'".$_POST["prob"]."'");
    }
    if (isset($_POST["social"])) {
      array_push($names, "social");
      array_push($values, "'".$_POST["social"]."'");
    }
    if (isset($_POST["profile"])) {
      array_push($names, "profile");
      array_push($values, "'".$_POST["profile"]."'");
    }

    if (isset($_POST["min_age"])) {
      array_push($names, "min_age");
      array_push($values, "'".$_POST["min_age"]."'");
    }
    if (isset($_POST["max_age"])) {
      array_push($names, "max_age");
      array_push($values, "'".$_POST["max_age"]."'");
    }
    
    if (isset($_POST["like_one"])) {
      array_push($names, "like_one");
      array_push($values, "'".$_POST["like_one"]."'");
    }
    if (isset($_POST["like_two"])) {
      array_push($names, "like_two");
      array_push($values, "'".$_POST["like_two"]."'");
    }
    if (isset($_POST["like_three"])) {
      array_push($names, "like_three");
      array_push($values, "'".$_POST["like_three"]."'");
    }
    if (isset($_POST["dislike_one"])) {
      array_push($names, "dislike_one");
      array_push($values, "'".$_POST["dislike_one"]."'");
    }
    if (isset($_POST["dislike_two"])) {
      array_push($names, "dislike_two");
      array_push($values, "'".$_POST["dislike_two"]."'");
    }
    if (isset($_POST["dislike_three"])) {
      array_push($names, "dislike_three");
      array_push($values, "'".$_POST["dislike_three"]."'");
    }    
    if (isset($_POST["comments"])) {
      array_push($names, "comments");
      array_push($values, "'".$_POST["comments"]."'");
    }
    // cначала проверим, боец ли этот человек
    $query = "SELECT id FROM fighters where vk_id=".$_POST["vk_id"].";";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $line = mysql_fetch_array($rt, MYSQL_ASSOC);
    if (isset($line["id"])) {
      array_push($names, "fighter_id");
      array_push($values, "'".$line["id"]."'");
    }
    $names = implode(", ", $names);
    $values = implode(", ", $values);
    $query = "INSERT INTO guess_shift (".$names.") VALUES (".$values.");";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
  

    $result["result"] = "Success";
    echo json_encode($result);
  }
}

function del_from_shift() {
  check_session();
  session_start();
  if (isset($_SESSION["current_group"])) {
    if (isset($_POST["vk_id"])) { 
      if (!((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)))) {
        echo json_encode(array('result' =>  "Fail"));
        return;
      }
    } else {
      $_POST["vk_id"] = $_SESSION["vk_id"];
    }
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
    $query = "DELETE FROM guess_shift WHERE vk_id=".$_POST["vk_id"].";";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["result"] = "Success";
    echo json_encode($result);
  }
}

?>