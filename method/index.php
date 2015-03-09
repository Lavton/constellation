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
    <p>В этом разделе собрано всё, что касается вас как человека, который едет
      не только развлекать, но и отвечать за детей.
    </p>
    <ul>
      <li><a href="mustdo">к лагерю необходимо сделать</a></li>
      <li><a href="mustbe">правовая основа</a></li>
      <li><a href="musthave">чемодан вожатого</a></li>
      <li><a href="map">территория и люди</a></li>
      <li><a href="time">распорядок, периоды и мероприятия</a></li>
      <li><a href="age">возрастные особенности</a></li>
      <hr>
      <li><a href="knowledges">источники знаний</a></li>
    </ul>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  </div>
</body>
</html>
