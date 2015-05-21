<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>
    

  <div id="page-container">
    <div style="position:fixed; background-color: white; left:150; top:50; z-index:4; display:none; border: 4px outset green" class="show_div_camod">
      <button class="OK_camod_button">OK</button><br>
      <div>
      </div>
    </div>

    <p>Вообще, чемодан вожатого (да и сама вожатская) - как Греция. В них должно быть всё!<br>
      Но, чтобы иметь хоть небольшое представление о том, что это "всё" включает - воспользуйтесь нашем "Чемоданчиком вожатого"
    </p>
    <a href="resources/camod.png" target="_blank">(клик)</a>
    <img src="resources/camod_check.png" class="camod" width=150%>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  <script type="text/javascript" src="/own/js/camod.js"></script>
</div>
</body>
</html>
