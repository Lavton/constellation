<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
			/*если просят менять группу доступа - сделаем это!*/
			case "change_group": change_group(); break;
			case "all": get_all_fighters(); break;
			case "get_one_info": get_one_info(); break;
			case 'set_new_data': set_new_data(); break;
      case "get_all_ids": get_all_ids(); break;
      case "add_new_fighter": add_new_fighter(); break;

      case "kill_fighter": kill_fighter(); break;

      case "get_own_info": get_own_info(); break;

      /*то, что относится ко всем кандидатам*/
      case "get_all_candidats_ids": get_all_candidats_ids(); break;
      case "add_new_candidate": add_new_candidate(); break;
      case "all_candidats" : all_candidats(); break;


      /*1 кандидат*/
      case "get_one_candidate_info": get_one_candidate_info(); break;
      case "set_new_cand_data": set_new_cand_data(); break;
      case "kill_candidate": kill_candidate(); break;
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
		echo json_encode(Array('result' => 'Success', 'ss' => $_SESSION));
	} else {
    echo json_encode(Array('result' => 'Fail'));
	}
}

//get all users base info
function get_all_fighters() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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
  	// поиск юзера
  	$query = 'SELECT  id, vk_id, name, second_name, surname, maiden_name, birthdate, phone, second_phone, email, Instagram_id FROM fighters ORDER BY id;';
  	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
  	$result["users"] = array();

  	while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
  		array_push($result["users"], $line);
      }
  	$result["result"] = "Success";
  	mysqli_close($link);
    echo json_encode($result);
  }
}
//get one user info for profile
function get_one_info() {
	check_session();
  session_start();
	if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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

  	// поиск юзера
  	$query = "SELECT * FROM fighters WHERE id='".$_POST['id']."' ORDER BY id;";
  	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
  	$result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

    $query = "select min(id) as mid FROM fighters where id > ".$_POST['id'].";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
    $query = "select max(id) as mid FROM fighters where id < ".$_POST['id'].";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

    // $result["q"] = $query;
  	$result["result"] = "Success";
  	mysqli_close($link);
    echo json_encode($result);
  }
}

//change one user info in profile
function set_new_data() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) || ($_POST["id"]==0)) {
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
    if ($_POST["id"] != 0) {
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
      if (isset($_POST["year_of_entrance"])) {
        array_push($names, "year_of_entrance");
        array_push($values, "'".$_POST["year_of_entrance"]."'");
      }      
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
      array_push($names, "Instagram_id");
      array_push($values, "'".$_POST["Instagram_id"]."'");

  	if (isset($_POST["birthdate"])) {
  		array_push($names, "birthdate");
  		array_push($values, "'".$_POST["birthdate"]."'");
  	}
  	$conc = array();
  	foreach ($names as $key => $value) {
  		array_push($conc, "".$value."=".$values[$key]);
  	}
  	$conc = implode(", ", $conc);
    $query = "";
    if ($_POST["id"] == 0) {
if (!(isset($_SESSION["fighter_id"]))) {
    
      $query = 'SELECT id FROM fighters where vk_id=\''.$_SESSION["vk_id"].'\';';
      $_SESSION["fighter_id"] = mysqli_query($link, $query) or die('Запрос не удался: ');
      $_SESSION["fighter_id"] = mysqli_fetch_array($_SESSION["fighter_id"], MYSQL_ASSOC);
      $_SESSION["fighter_id"] = $_SESSION["fighter_id"]["id"];

  }
        $query = "UPDATE fighters SET ".$conc." WHERE id='".$_SESSION['fighter_id']."';";
    }else{
  	  $query = "UPDATE fighters SET ".$conc." WHERE id='".$_POST['id']."';";
    }
  	$rt = mysqli_query($link, $query) or die('Запрос не удался: ');
  //	$result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
  	$result["result"] = "Success";
  	// $result["query"] = $query;
  	mysqli_close($link);
    echo json_encode($result);
  } else {
    mysqli_close($link);
    echo json_encode(Array('result' => 'Fail'));
  }
}

//get list of existing ids and vk_ids
function get_all_ids() {
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

    // поиск юзера
    $query = "SELECT id, vk_id FROM fighters ORDER BY id;";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["ids"] = array();
    while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
      array_push($result["ids"], $line);
    }
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}

//добавляет бойца
function add_new_fighter() {
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
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}

// удаляет пользователя
function kill_fighter() {
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

    //удаляем бойца по id
    $query = "DELETE FROM fighters WHERE id=".$_POST["id"].";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}


function get_own_info() {
  check_session();
  session_start();
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
  if (!(isset($_SESSION["fighter_id"]))) {
    
      $query = 'SELECT id FROM fighters where vk_id=\''.$_SESSION["vk_id"].'\';';
      $_SESSION["fighter_id"] = mysqli_query($link, $query) or die('Запрос не удался: ');
      $_SESSION["fighter_id"] = mysqli_fetch_array($_SESSION["fighter_id"], MYSQL_ASSOC);
      $_SESSION["fighter_id"] = $_SESSION["fighter_id"]["id"];
  }
    // поиск юзера
    $query = "SELECT * FROM fighters WHERE id='".$_SESSION['fighter_id']."' ORDER BY id;";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
    $result["result"] = "Success";
    // $result["session"] = $_SESSION;
    mysqli_close($link);
    echo json_encode($result);
}



function get_all_candidats_ids() {
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

    // поиск юзера
    $query = "SELECT id, vk_id FROM candidats ORDER BY id;";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["ids"] = array();
    while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
      array_push($result["ids"], $line);
    }
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}


//добавляет кандидата
function add_new_candidate() {
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
    $query = "INSERT INTO candidats (".$names.") VALUES (".$values.");";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["result"] = "Success";
    $result["qw"] = $query;
    mysqli_close($link);
    echo json_encode($result);
  }
}

function all_candidats() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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
    // поиск юзера
    $query = 'SELECT id, vk_id, birthdate, phone FROM candidats ORDER BY id;';
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["users"] = array();

    while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
      array_push($result["users"], $line);
      }
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }

}


function get_one_candidate_info() {
    check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
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

    // поиск юзера
    $query = "SELECT * FROM candidats WHERE id='".$_POST['id']."' ORDER BY id;";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

    $query = "select min(id) as mid FROM candidats where id > ".$_POST['id'].";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["next"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
    $query = "select max(id) as mid FROM candidats where id < ".$_POST['id'].";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["prev"] = mysqli_fetch_array($rt, MYSQL_ASSOC);

    // $result["q"] = $query;
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }
}

function set_new_cand_data() {
  check_session();
  session_start();
  if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) || ($_POST["id"]==0)) {
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
    if ($_POST["id"] != 0) {
      if (isset($_POST["vk_id"])) {
        array_push($names, "vk_id");
        array_push($values, "'".$_POST["vk_id"]."'");
      }
    }
    if (isset($_POST["phone"])) {
      array_push($names, "phone");
      array_push($values, "'".$_POST["phone"]."'");
    }
    if (isset($_POST["birthdate"])) {
      array_push($names, "birthdate");
      array_push($values, "'".$_POST["birthdate"]."'");
    }
    $conc = array();
    foreach ($names as $key => $value) {
      array_push($conc, "".$value."=".$values[$key]);
    }
    $conc = implode(", ", $conc);
    $query = "";
    if ($_POST["id"] == 0) {
if (!(isset($_SESSION["fighter_id"]))) {
    
      $query = 'SELECT id FROM candidats where vk_id=\''.$_SESSION["vk_id"].'\';';
      $_SESSION["fighter_id"] = mysqli_query($link, $query) or die('Запрос не удался: ');
      $_SESSION["fighter_id"] = mysqli_fetch_array($_SESSION["fighter_id"], MYSQL_ASSOC);
      $_SESSION["fighter_id"] = $_SESSION["fighter_id"]["id"];

  }
        $query = "UPDATE candidats SET ".$conc." WHERE id='".$_SESSION['fighter_id']."';";
    }else{
      $query = "UPDATE candidats SET ".$conc." WHERE id='".$_POST['id']."';";
    }
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
  //  $result["user"] = mysqli_fetch_array($rt, MYSQL_ASSOC);
    $result["result"] = "Success";
    // $result["query"] = $query;
    mysqli_close($link);
    echo json_encode($result);
  } else {
    mysqli_close($link);
    echo json_encode(Array('result' => 'Fail'));
  }
}

/*удаляет кандидата из БД*/
function kill_candidate() {
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

    //удаляем бойца по id
    $query = "DELETE FROM candidats WHERE id=".$_POST["id"].";";
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    $result["result"] = "Success";
    mysqli_close($link);
    echo json_encode($result);
  }

}
?>