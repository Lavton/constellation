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
  ?>
      
  <div id="page-container">
    <?php
      session_start();
      /*смотрим на свой профиль*/
      if (isset($_GET["id"]) && $_GET["id"] == 0) {
        echo "<h2>Выбрать категорию доступа</h2>";
        echo "<i>данное поле влияет на то, как вы видите страницы.</i> ";
        echo '<span class="saved">  (Изменения сохранены)</span><br/>';
        foreach ($_SESSION["groups_av"] as $key => $value) {
          if ($_SESSION["current_group"] == $key) {
            echo '<input type="radio" checked name="group_r" value="'.$key.'"> '.$value.'<br/>';
          } else {
            echo '<input type="radio" name="group_r" value="'.$key.'"> '.$value.'<br/>';
          }
        }

      /*смотрим на чужой профиль (доступно >=бойцам)*/
      } elseif (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
    ?>
        echo '<h2>Просмотреть профиль</h2>';
        echo "(не сейчас, а когда он будет)";

    <?php
      /*не боец попытался посмотреть профиль*/
      } elseif (isset($_GET["id"])) {
        echo "Access denied";
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
        echo'</div></body>
        </html>';
        exit();
      }
    ?>

    <?php
    /*не смотрим конкретный профиль*/
      if (!isset($_GET["id"])){
        /*если не боец, то нельзя посмотреть людей в отряде*/
        if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
          echo "Access denied";
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
          echo'</div></body>
          </html>';
          exit();
        }
        /*иначе - смотри людей)*/
    ?>
    расширенный вариант нумеровочки со страничками каждого бойца.<br/>
    и смены, которые мы отработали, может что ещё.. <br/>
    <span title='запрос может занять некоторое время. Ищите конкретного человека? Воспользуйтесь поиском!'>
      <button type="button" class="btn btn-info get-all unclick">а можно всех посмотреть?</button>
    </span> 
     <button type="button" class="own-hidden btn btn-info vCard-start unclick">Кого хотите посмотреть?</button>
     <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-all">Выбрать всех</button>
     <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-none">Снять выбор</button>
     <button type="button" class="btn btn-success own-hidden vCard-get unclick" disabled="disabled">Посмотреть контакты</button>
     <br/>
     <input type="text" class="vCard-category own-hidden" placeholder="назначить группу для контактов" size=30 />
     <button type="button" class="btn btn-success own-hidden vCard-make">импорт в <abbr title='формат записной книжки для Android, iPhone и т.д.'>vCard</abbr></button>

    <?php
      }
    ?>
    <div class="table-container"></div>
  </div> 

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  <script type="text/javascript" src="/own/js/users/own_profile.js"></script>

  <!-- скрипт для сохранения файла из js -->
  <script src="/standart/js/FileSaver.js"></script>
  <script type="text/javascript" src="/own/js/users/all_of_us.js"></script>

  <script type="text/javascript" src="/own/js/users/one_fighter.js"></script>

</div>
</body>
</html>
