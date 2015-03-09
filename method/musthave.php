<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
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
    <p>Вообще, чемодан вожатого (да и сама вожатская) - как Греция. В них должно быть всё!<br>
      Но, чтобы иметь хоть небольшое представление о том, что это "всё" включает - воспользуйтесь нашем "Чемоданчиком вожатого"
    </p>
    (кликабельно)
    <ul>
      <li><a href="resources/camod.png" target="_blank">
        <img src="resources/camod.png" width=100%>
      </a></li>
    </ul>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  </div>
</body>
</html>
