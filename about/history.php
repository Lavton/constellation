<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
?>
<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>
      

    <div id="page-container">
  тут история отряда.
  <?php 
  session_start();
  echo print_r($_SESSION);
  echo "<br><br><br>";
  echo print_r($_COOKIE);
  ?>
    </div> 


<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
</body>
</html>
