<?php

	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) {
      case "put": put_c(); break;
      case "get": get_c(); break;
		}
	}

function put_c(){
	file_put_contents("Stro.json", json_encode($_POST["cam"]));
	echo json_encode(array("res" => True));
}
function get_c() {
  echo file_get_contents("Stro.json");
}

?>