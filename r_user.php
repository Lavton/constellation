<?php
if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
		$action = $_POST["action"];
		switch($action) { //Switch case for value of action
			case "change_group": change_group(); break;
		}
	}
}

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

//change curren group and return if successed
function change_group(){
	session_start();

	if (isset($_SESSION["groups_av"][$_POST["new_group"]])) {
		$_SESSION["current_group"] = $_POST["new_group"];
		echo json_encode(Array('result' => 'Success'));
	} else {
		echo json_encode(Array('result' => 'Fail'));
	}
}

?>