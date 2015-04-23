<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
	      case "add_new_event": add_new_event(); break;
	      case "all": get_all(); break;
        case "arhive": arhive(); break;
        case "get_one_info": get_one_info(); break;
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

//get all shifts base info
function get_all() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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
  	// поиск мероприятий
  	$query = 'SELECT * FROM events WHERE (end_time >= CURRENT_TIMESTAMP) ORDER BY start_time;';
  	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
  	$result["events"] = array();

  	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
      if (($line["visibility"]+0) <= ($_SESSION["current_group"]+0)) {
    		array_push($result["events"], $line);
      }
    }
  	$result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}

//get arhive events base info
function arhive() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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
    // поиск мероприятий
    $query = 'SELECT * FROM events WHERE (end_time < CURRENT_TIMESTAMP AND start_time >= "'.$_POST["month"].'-01 00:00:00") ORDER BY start_time;';
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["events"] = array();

    while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
      if (($line["visibility"]+0) <= ($_SESSION["current_group"]+0)) {
        array_push($result["events"], $line);
      }
    }
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}

function get_one_info() {
    check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
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

    // поиск мероприятия
    $query = "SELECT * FROM events WHERE id='".$_POST['id']."';";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["event"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
    $st = "'".$result["shift"]["start_time"]."'";
    $query = "SELECT min(id) as mid FROM shifts where visibility <= ".$_SESSION["current_group"]." AND start_time > ".$st.";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

    $query = "SELECT max(id) as mid FROM shifts where visibility <= ".$_SESSION["current_group"]." AND start_time < ".$st.";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
    mysqli_close($link);
    echo json_encode($result);
  }
}
?>