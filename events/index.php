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
        if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= ADMIN)) {
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/events/one.php');
        } elseif (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] < FIGHTER) || (!(isset($_SESSION["vk_id"])))) { ?>
    У нас много мероприятий в течение года! <br/>
    Станьте бойцами и узнаете
          <?php 
        } else { ?>
          <iframe src="https://www.google.com/calendar/embed?src=kn2p9ovqid67tekk0ecbbnutsc%40group.calendar.google.com&ctz=Europe/Moscow" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
          <br>

        <?php 
       }
      } else {
        if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE)) {
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/events/all.php');
        }
      }
    ?>
  </div><!-- page-container -->
<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>

<div id="after-js-container">
  <script type="text/javascript">
      document.title = 'мероприятия | CПО "СОзвездие"';
  </script>
  <script type="text/javascript" src="/own/js/events/all.js"></script>
  <script type="text/javascript" src="/own/js/events/one.js"></script> 

  <?php
  if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
  ?>
  <script type="text/javascript">
    get_event(<?=$_GET["id"]?>);
  </script>
  <?php
  }
  ?>
  <script type="text/javascript">
  $('.bbcode').markItUp(mySettings);
  </script>
</div>
</body>
</html>
