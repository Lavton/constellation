<?php

	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
      case "get_camod": get_camod_c(); break;
		}
	}
function get_camod_c() {
  echo file_get_contents("Camod.json");
}

?>