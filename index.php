<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include('own/templates/header.php');
    include_once('own/templates/php_globals.php');
  ?>

</head>
<body>
  <?php
    include('own/templates/menu.php');
  ?>
      
  <div class="container">
    <?php
    /*Если незарег, то одно показываем, иначе - другое*/
      session_start();
      if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == UNREG) || (!(isset($_SESSION["user_id"])))) {
        include('own/templates/indexes/1.php');
      } else {
        include('own/templates/indexes/not1.php');
      }
    ?>

  </div> 

<?php
  include('own/templates/footer.php');
?>
</body>
</html>
