<?php

	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
      case "get_stro": get_stro_c(); break;
		}
	}

/*данные по строевке*/
function get_stro_c() {
  echo file_get_contents("Stro.json");
}

?>