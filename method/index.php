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
      <li><a href="mustdo" class="ajax-nav">к лагерю необходимо сделать</a></li>
      <li><a href="mustbe" class="ajax-nav">правовая основа</a></li>
      <li><a href="musthave" class="ajax-nav">чемодан вожатого</a></li>
      <li><a href="map" class="ajax-nav">территория и люди</a></li>
      <li><a href="time" class="ajax-nav">распорядок, периоды и мероприятия</a></li>
      <li><a href="age" class="ajax-nav">возрастные особенности</a></li>
      <hr>
      <li><a href="knowledges" class="ajax-nav">источники знаний</a></li>
    </ul>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  </div>
</body>
</html>
