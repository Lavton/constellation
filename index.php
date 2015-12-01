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
    /*Если незарег, то одно показываем, иначе - другое*/
      check_session();
      session_start();
      if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"]*1 == UNREG) || (!(isset($_SESSION["uid"])))) {
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/indexes/1.php');
      } else {
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/indexes/not1.php');
      }
    ?>
  </div> 

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  <script type="text/javascript" src="//vk.com/js/api/openapi.js"></script>

  <!-- VK Widget -->
  <script type="text/javascript">
    /*при ajax загрузке не всегда опенАПИ к этому моменту подгружается.
  Ждём, пока это не произойдёт в цикле.*/
    var intID = setInterval(function(){
      if (typeof VK !== "undefined") {
        if ($("#vk_groups")[0] != undefined) {
          VK.Widgets.Group("vk_groups", {mode: 0, width: "260", height: "400", color1: 'FFFFFF', color2: '2B587A', color3: '333'}, 19748633);
        }
        clearInterval(intID);
      }
    }, 50);
  </script>

  <script type="text/javascript">
      document.title = 'CПО "СОзвездие" | сайт отряда';
  </script>
</div>
</body>
</html>
