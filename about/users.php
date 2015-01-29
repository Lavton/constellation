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
      <div class="user-info">
        <div class="col-xs-12 user-name">
          <h2></h2>
          <div class="btn-toolbar">
            <a href="/users/415/edit" class="hidden btn btn-primary" role="button">Редактировать</a>
          </div>
          <hr>
        </div>
        <div class="row own-row">
              <div class="col-xs-3">
      
            <img class="pull-left ava" src="" width="200" height="200">
      
          </div>
          <div class="col-xs-9 info-str">
            <ul>
              <li class='vk hidden'><strong>vk: </strong></li>
              <li class='phone-first hidden'><strong>Телефон: </strong></li>
              <li class='phone-second hidden'><strong>Телефон: </strong></li>
              <li class='email hidden'><strong>e-mail: </strong></li>
              <li class='birthdate hidden'><strong>День рождения: </strong></li>
              <li class='year_of_entrance hidden'><strong>Год вступления в отряд: </strong></li>
              <li class='group_of_rights hidden'><strong>Права доступа: </strong></li>
              
            </ul>
          </div>
      
        </div>

      </div>
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

    <div class="table-container" ng-cloak ng-controller="fightersApp">
      <div class="search_wrap hidden"> Search: <input class="search" ng-model="query">
      <table class="table common-contacts hidden table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>имя</th>
            <th>год вступления в отряд</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="fighter in fighters | filter:query" class="{{fighter.id}}">
            <td class="ids"><a href='users/{{fighter.id}}'>{{fighter.id}}</a></td>
            <td class="inputs hidden"> <input type="checkbox" name='vCard_check' value="{{fighter.id}}"> </td>
            <td><strong>{{fighter.name}} {{fighter.surname}}</strong></td>
            <td>{{fighter.year_of_entrance}}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <?php
      }
    ?>
  </div>  <!-- page-container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">

  <!-- скрипт для сохранения файла из js -->
  <script src="/standart/js/FileSaver.js"></script>

  <script type="text/javascript" src="/standart/js/angular.js"></script>


   <script type="text/javascript" src="/own/js/users/all_of_us.js"></script>

  <script type="text/javascript" src="/own/js/users/own_profile.js"></script>


  <script type="text/javascript" src="/own/js/users/one_fighter.js"></script>
  <?php
  if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
  ?>
  <script type="text/javascript">
    get_user_info(<?=$_GET["id"]?>);
  </script>
  <?php
  }
  ?>

</div>
</body>
</html>
