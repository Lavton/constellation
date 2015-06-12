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
            // include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/one.php');
            include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/one_shift_name.html');
            ?> 
            <div class="row own-row">
              <?php 
              include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/one_shift_common.html');
              include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/one_shift_add_apply.html');
              ?>
            </div>
            <?php 
            include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/shifts/one_shift_people.html');
            ?>

            <br/>
            <br/>
            <a href="#" class="shift_priv ajax-nav"> &lt;&lt;предыдущая</a> &nbsp; &nbsp; <a href="#" class="shift_next pull-right ajax-nav">следующая &gt;&gt;</a>
            <?php
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
  <script type="text/javascript">
  $('.bbcode').markItUp(mySettings);
  </script>
</div>
</body>
</html>
