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
      <p>В этом разделе будет всё, что вы можете сделать для детей, чтобы они не скучали</p>
      <ul><li>чуть более, чем полная <a href="resources/games_classif.png" target="_blank">классификация игр</a></li></ul>
      <div ng-cloak ng-controller="gameApp" class="games-container">
        <details>
           <summary>Добавить игру</summary>
           {{hello}}
        </details>
      </duv>
    </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  <script type="text/javascript" src="/own/js/games.js"></script>
  </div>
</body>
</html>
