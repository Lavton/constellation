<!DOCTYPE html>
<html>
<head lang="en">
  <title>Комсоставу | CПО "СОзвездие"</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>
      
  <div id="page-container">
    <?php
      check_session();
      session_start();
      if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) {
    ?>
    В этом разделе доп. функции и материалы, доступные комсоставу.
    <?php 
      } 
    ?>
  </div><!-- page-container -->
<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>

<div id="after-js-container">
  <script type="text/javascript">
  $('.bbcode').markItUp(mySettings);
  </script>
</div>
</body>
</html>
