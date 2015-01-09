<!DOCTYPE html>
<html>
<head lang="en">
  <?php
    session_start();
    if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] > 2)) {
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
