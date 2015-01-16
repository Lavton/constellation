<?php
//Function to check if the request is an AJAX request
function is_ajax() {
  return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_POST["ajaxLoad"]);
}

/*алаесы для групп доступа*/
define("UNREG", 1);
define("CANDIDATE", 2);
define("FIGHTER", 3);
define("OLD_FIGHTER", 4);
define("EX_COMMAND_STAFF", 5);
define("COMMAND_STAFF", 6);
define("ADMIN", 7);
?>