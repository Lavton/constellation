<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
	      case "add_new_event": add_new_event(); break;
		}
	}
}

function add_new_event(){
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
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


    $names = array();
    $values = array();
      array_push($names, "name");
      array_push($values, "'".$_POST["name"]."'");

      array_push($names, "start_time");
      array_push($values, "'".$_POST["start_time"]."'");

      array_push($names, "end_time");
      array_push($values, "'".$_POST["end_time"]."'");

    $names = implode(", ", $names);
    $values = implode(", ", $values);
    $query = "INSERT INTO events (".$names.") VALUES (".$values.");";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}


?>