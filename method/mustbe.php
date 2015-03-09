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
    <p>Вожатый - это работа. И, как и у всякой работы, у вас будут свои права и свои обязанности.
    </p>
    <ul>
      <li><a href="resources/low_base.pdf" target="_blank">Законодательная база</a></li>
      <li><a href="resources/rights.pdf" target="_blank">Права и обязанности</a></li>
    </ul>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  </div>
</body>
</html>
