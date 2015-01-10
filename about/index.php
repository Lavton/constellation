<!DOCTYPE html>
<html>
<head lang="en">
  <?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
    session_start();
    if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) {
      echo '<meta http-equiv="Refresh" content="0; URL=/about/faces">';
    } else {
      echo '<meta http-equiv="Refresh" content="0; URL=/about/history">';
    }  
    exit();
  ?>
</head>
<body>
</body>
</html>
