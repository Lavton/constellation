<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
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
      if (isset($_GET["id"])) {
        if (isset($_GET["edit"])) {
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/detach_edit.php');
        } else {
          if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE)) {
            include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/one.php');
          } else {
            echo "Вы не авторизованы. <a href='/login'>Войдите</a>";
          }
        }

      } else {
        if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE)) {
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/all.php');
        } else {
          echo "Вы не авторизованы. <a href='/login'>Войдите</a>";
        }

      }
    ?>
  </div><!-- page-container -->
<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>

<div id="after-js-container">
  <script type="text/javascript" src="/own/js/shifts/one.js"></script>
  <script type="text/javascript" src="/own/js/shifts/detach_edit.js"></script>
  <?php
  if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
    if (isset($_GET["edit"])) {
  ?>
  <script type="text/javascript">
    get_shift_edit(<?=$_GET["id"]?>);
  </script>
  <?php } else { ?>
  <script type="text/javascript">
    get_shift(<?=$_GET["id"]?>);
  </script>
  <?php
  }
  }
  ?>
  <script type="text/javascript">
  $('.bbcode').markItUp(mySettings);
  </script>
</div>
</body>
</html>
