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
      
  <div id="page-container">
    <?php
    /*Если незарег, то одно показываем, иначе - другое*/
      session_start();
      if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == UNREG) || (!(isset($_SESSION["user_id"])))) {
        include('own/templates/indexes/1.php');
      } else {
        include('own/templates/indexes/not1.php');
      }
    ?>
<table>
  <tr>
<td><div class="vk-g"><div id="vk_groups"></div></div></td>
<td><div class="in-w"><iframe src='/inwidget/index.php?view=16&inline=4' scrolling='no' frameborder='no' style='border:none;width:260px;height:400px;overflow:hidden;'></iframe></div></td>
</tr></table>
  </div> 

<?php
  include('own/templates/footer.php');
?>
<div id="after-js-container">
  <script type="text/javascript" src="//vk.com/js/api/openapi.js"></script>

  <!-- VK Widget -->
  <script type="text/javascript">
    /*при ajax загрузке не всегда опенАПИ к этому моменту подгружается.
  Ждём, пока это не произойдёт в цикле.*/
    var intID = setInterval(function(){
      if (typeof VK !== "undefined") {
        VK.Widgets.Group("vk_groups", {mode: 0, width: "260", height: "400", color1: 'FFFFFF', color2: '2B587A', color3: '333'}, 19748633);
        clearInterval(intID);
      }
    }, 50);
  </script>
  <script type="text/javascript">
      document.title = 'CПО "СОзвездие" | будущий сайт отряда';
  </script>
</div>
</body>
</html>
