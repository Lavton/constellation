<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');

  function get_content() {
?>
  <div id="page-container">
    <?php 
      check_session();
      session_start();
      if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] < FIGHTER) || (!(isset($_SESSION["vk_id"])))) {
    ?>
    У нас много мероприятий в течение года! <br/>
    Станьте бойцами и узнаете
    <?php
      } else {
        if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] < ADMIN)) {
    ?>
<iframe src="https://www.google.com/calendar/embed?src=kn2p9ovqid67tekk0ecbbnutsc%40group.calendar.google.com&ctz=Europe/Moscow" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
<br>

    <?php
      }
      }

    ?>
    <?php
      if (!isset($_GET["id"])){
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/events/all.php');
      } else {
        // include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/events/one.php');
      }
    ?>
  </div>
  <div class="info_wall"></div>
<?php
  }
  function get_js() {
?>
<div id="after-js-container">
  <script type="text/javascript">
      document.title = 'мероприятия | CПО "СОзвездие"';
  </script>
  <script type="text/javascript" src="/own/js/events/all.js"></script>
    <script type="text/javascript" src="/own/js/events/one.js"></script> 
  <script type="text/javascript">
    // get_event(<?=$_GET["id"]?>);
  </script>
  <script type="text/javascript">
  $('.bbcode').markItUp(mySettings);
  </script>
  
</div>
<?php    
  }
  if (is_ajax()) {
    /*Если поступил ajax-запрос, мы просто возвращаем содержимое двух контейнеров*/
    get_content();
    get_js();
    exit();
  }
  /*иначе - весь код страницы*/
?>

<!DOCTYPE html>
<html>
<head lang="en">
  <title>мероприятия | CПО "СОзвездие"</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>


<?php
  get_content();
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
  get_js();
?>
</body>
</html>
