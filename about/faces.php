<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  
    if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
      echo "Access denied";
      include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
      echo'</body>
      </html>';
      exit();
    }
  ?>
        

    <div class="container ctred">
     расширенный вариант нумеровочки со страничками каждого бойца.<br/>
      и смены, которые мы отработали, может что ещё.. <br/>
 
    </div> <!-- /container -->


<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
</body>
</html>
