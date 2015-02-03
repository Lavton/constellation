<?php
if (isset($_GET["id"]) && $_GET["id"] == 0) {
  require_once ($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
  echo "<h2>Выбрать категорию доступа</h2>";
  echo "<i>данное поле влияет на то, как вы видите страницы.</i> ";
  echo '<span class="saved">  (Изменения сохранены)</span><br/>';
  for ($i=1; $i <= $_SESSION["group"]; $i++) { 
    if ($_SESSION["current_group"] == $i) {
      echo '<input type="radio" checked name="group_r" value="'.$i.'"> '.$groups_rus[$i].'<br/>';
    } else {
      echo '<input type="radio" name="group_r" value="'.$i.'"> '.$groups_rus[$i].'<br/>';
    }    
  }
}
?>