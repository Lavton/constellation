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
    <p>Не так-то просто поехать в лагерь! Ведь вы будете работать с детьми!<br>
      Вот лишь некоторые документы, которые вам понадобится собрать:
    </p>
    <ul>
      <li><a href="resources/documents.pdf" target="_blank">Документы</a></li>
    </ul>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  </div>
</body>
</html>
