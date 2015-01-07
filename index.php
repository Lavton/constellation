<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include('own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include('own/templates/menu.php');
  ?>
      
  <div class="container">
    <?php
      session_start();
      if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == 1) || (!(isset($_SESSION["user_id"])))) {
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
