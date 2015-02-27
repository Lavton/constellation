<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
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
        if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE)) {
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/one.php');
        }

      } else {
        if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE)) {
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/all.php');
        }
      }
    ?>
  </div><!-- page-container -->
<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>

<div id="after-js-container">
  <script type="text/javascript">
    document.title = 'Смены | CПО "СОзвездие"';
  </script>
  <script type="text/javascript" src="/own/js/shifts/all.js"></script>
  <script type="text/javascript" src="/own/js/shifts/one.js"></script>
  <?php
  if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
  ?>
  <script type="text/javascript">
    get_shift(<?=$_GET["id"]?>);
  </script>
  <?php
  }
  ?>

</div>
</body>
</html>
