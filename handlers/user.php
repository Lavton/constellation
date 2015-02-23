<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
			/*если просят менять группу доступа - сделаем это!*/
			case "change_group": change_group(); break;
			case "all": get_all(); break;
			case 'get_full_info': get_full_info(); break;
			case "get_one_info": get_one_info(); break;
			case 'set_new_data': set_new_data(); break;
      case "get_all_ids": get_all_ids(); break;
      case "add_new_fighter": add_new_fighter(); break;
      case "kill_fighter": kill_fighter(); break;
		}
	}
}

//change current group and return if successed
function change_group(){
  check_session();
	session_start();

	if ($_SESSION["group"] >= $_POST["new_group"]) {
		$_SESSION["current_group"] = $_POST["new_group"];
		setcookie ("current_group", $_SESSION["current_group"], time() + 60*60*24*100, "/");
		echo json_encode(Array('result' => 'Success'));
	} else {
		echo json_encode(Array('result' => 'Fail'));
	}
}

//get all users base info
function get_all() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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
  	$query = 'SELECT id, name, surname, maiden_name, nickname, year_of_entrance FROM fighters ORDER BY id;';
  	$rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
  	$result["users"] = array();

  	while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
  		array_push($result["users"], $line);
      }
  	$result["result"] = "Success";
  	echo json_encode($result);
  }
}

//get selected users full info
function get_full_info() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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

  	$ids = array();
  	foreach ($_POST["ids"] as $value) {
  		array_push($ids, $value);
  	}
  	$ids = implode(", ", $ids);
  	/*поиск юзера*/
  	$query = "SELECT id, vk_id, name, second_name, surname, maiden_name, birthdate, phone, second_phone, email FROM fighters WHERE id IN ($ids) ORDER BY id;";
  	$rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
  	$result["users"] = array();
  	while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
  		array_push($result["users"], $line);
      }
  	$result["result"] = "Success";
  	echo json_encode($result);
  }
}

//get one user info for profile
function get_one_info() {
	check_session();
  session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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
  	$query = "SELECT * FROM fighters WHERE id='".$_POST['id']."' ORDER BY id;";
  	$rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
  	$result["user"] = mysql_fetch_array($rt, MYSQL_ASSOC);
  	$result["result"] = "Success";
  	echo json_encode($result);
  }
}

//change one user info in profile
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
  	if (isset($_POST["vk_id"])) {
  		array_push($names, "vk_id");
  		array_push($values, "'".$_POST["vk_id"]."'");
  	}
  	if (isset($_POST["group_of_rights"])) {
  		array_push($names, "group_of_rights");
  		array_push($values, "'".$_POST["group_of_rights"]."'");
  	}
  	if (isset($_POST["name"])) {
  		array_push($names, "name");
  		array_push($values, "'".$_POST["name"]."'");
  	}
  	if (isset($_POST["second_name"])) {
  		array_push($names, "second_name");
  		array_push($values, "'".$_POST["second_name"]."'");
  	}
  	if (isset($_POST["surname"])) {
  		array_push($names, "surname");
  		array_push($values, "'".$_POST["surname"]."'");
  	}
  	if (isset($_POST["maiden_name"])) {
  		array_push($names, "maiden_name");
  		array_push($values, "'".$_POST["maiden_name"]."'");
  	}
  	if (isset($_POST["phone"])) {
  		array_push($names, "phone");
  		array_push($values, "'".$_POST["phone"]."'");
  	}
  	if (isset($_POST["second_phone"])) {
  		array_push($names, "second_phone");
  		array_push($values, "'".$_POST["second_phone"]."'");
  	}
  	if (isset($_POST["email"])) {
  		array_push($names, "email");
  		array_push($values, "'".$_POST["email"]."'");
  	}
  	if (isset($_POST["birthdate"])) {
  		array_push($names, "birthdate");
  		array_push($values, "'".$_POST["birthdate"]."'");
  	}
  	if (isset($_POST["year_of_entrance"])) {
  		array_push($names, "year_of_entrance");
  		array_push($values, "'".$_POST["year_of_entrance"]."'");
  	}
  	$conc = array();
  	foreach ($names as $key => $value) {
  		array_push($conc, "".$value."=".$values[$key]);
  	}
  	$conc = implode(", ", $conc);
  	$query = "UPDATE fighters SET ".$conc." WHERE id='".$_POST['id']."';";
  	$rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
  //	$result["user"] = mysql_fetch_array($rt, MYSQL_ASSOC);
  	$result["result"] = "Success";
  	$result["query"] = $query;
  	echo json_encode($result);
  } else {
    echo json_encode(Array('result' => 'Fail'));
  }
}

//get list of existing ids and vk_ids
function get_all_ids() {
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

    // поиск юзера
    $query = "SELECT id, vk_id FROM fighters ORDER BY id;";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["ids"] = array();
    while ($line = mysql_fetch_array($rt, MYSQL_ASSOC)) {
      array_push($result["ids"], $line);
    }
    $result["result"] = "Success";
    echo json_encode($result);
  }
}

//добавляет пользователя
function add_new_fighter() {
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

    //добавляем id и vk_id
    // $query = "INSERT INTO fighters (id, vk_id) VALUES (".$_POST["id"].", '".$_POST["vk_id"]."');";
    

    $names = array();
    $values = array();
    if (isset($_POST["id"])) {
      array_push($names, "id");
      array_push($values, "'".$_POST["id"]."'");
    }
    if (isset($_POST["vk_id"])) {
      array_push($names, "vk_id");
      array_push($values, "'".$_POST["vk_id"]."'");
    }
    if (isset($_POST["group_of_rights"])) {
      array_push($names, "group_of_rights");
      array_push($values, "'".$_POST["group_of_rights"]."'");
    }
    if (isset($_POST["name"])) {
      array_push($names, "name");
      array_push($values, "'".$_POST["name"]."'");
    }

    if (isset($_POST["surname"])) {
      array_push($names, "surname");
      array_push($values, "'".$_POST["surname"]."'");
    }
    if (isset($_POST["birthdate"])) {
      array_push($names, "birthdate");
      array_push($values, "'".$_POST["birthdate"]."'");
    }
    if (isset($_POST["year_of_entrance"])) {
      array_push($names, "year_of_entrance");
      array_push($values, "'".$_POST["year_of_entrance"]."'");
    }

    $names = implode(", ", $names);
    $values = implode(", ", $values);
    $query = "INSERT INTO fighters (".$names.") VALUES (".$values.");";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["result"] = "Success";
    echo json_encode($result);
  }
}

// удаляет пользователя
function kill_fighter() {
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

    //удаляем бойца по id
    $query = "DELETE FROM fighters WHERE id=".$_POST["id"].";";
    $rt = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
    $result["result"] = "Success";
    echo json_encode($result);
  }
}
?>