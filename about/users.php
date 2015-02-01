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
      <div ng-cloak ng-controller="oneFighterApp">
        <div class="user-info hidden">
          
          <div class="col-xs-12">
            <h2>{{fighter.name}} {{fighter.second_name}} {{fighter.surname}} <span ng-show="fighter.maiden_name">({{fighter.maiden_name}})</span>
          <button type="button" class="btn btn-primary text-right" ng-click="editPerson()">Редактировать</button></h2>
            <hr>
            <span class="saved">  (Изменения сохранены)</span>
          </div>
          <div class="row own-row">
                <div class="col-xs-3">
        
              <img class="pull-left ava" ng-src="{{fighter.photo_200}}" width="200" height="200">
        
            </div>
            <div class="col-xs-9 info-str">
              <ul>
                <li ng-show="fighter.domain"><strong>vk: </strong><a target='_blank' href='//vk.com/{{fighter.domain}}'>vk.com/{{fighter.domain}}</a></li>
                <li ng-show="fighter.phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.phone}}'> {{goodView(fighter.phone)}} </a></li>
                <li ng-show="fighter.second_phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.second_phone}}'> {{goodView(fighter.second_phone)}} </a></li>
                <li ng-show="fighter.email"><strong>e-mail:</strong><a href='mailto:{{fighter.email}}'> {{fighter.email}} </a></li>
                <li ng-show="fighter.birthdate"><strong>День рождения: </strong>{{fighter.birthdate | date: 'dd.MM.yyyy'}}</li>
                <li ng-show="fighter.year_of_entrance"><strong>Год вступления в отряд: </strong>{{fighter.year_of_entrance}}</li>
                <li ng-show="fighter.group_of_rights"><strong>Права доступа: </strong>{{fighter.group_of_rights}}</li>
                
              </ul>
            </div>
          </div>
        </div>
        <div class="user-edit hidden">
          <form ng-submit="submit()">
            <div class="col-xs-12">
              <h2>
                <input type="text" ng-model="fighter.name" size="{{(fighter.name).length}}+1" /> 
                <input type="text" ng-model="fighter.second_name"  size="{{(fighter.second_name).length}}+1" />
                <input type="text" ng-model="fighter.surname"  size="{{(fighter.surname).length}}+1" />
                (<input type="text" ng-model="fighter.maiden_name"  size="{{(fighter.maiden_name).length}}+1" />)
                <input type="submit" class="btn btn-primary text-right" ng-click="editPerson()" value="Сохранить"></input>
                <button type="button" class="btn btn-primary text-right" ng-click="resetInfo(); editPerson()" >Отменить</input>
              </h2>
              <hr>
            </div>
            <div class="row own-row">
                  <div class="col-xs-3">
          
                <img class="pull-left ava" ng-src="{{fighter.photo_200}}" width="200" height="200">
          
              </div>
              <div class="col-xs-9 info-str">
                <ul>
                  <li><strong>vk: </strong>vk.com/<input type="text" class="vk-domain" ng-model="fighter.domain" size="{{(fighter.domain).length}}" />   (uid: {{fighter.vk_id}})</li>
                  <li><strong>Телефон:</strong> <input type="text" ng-model="fighter.phone" size="{{(fighter.phone).length}}" /> </li>
                  <li><strong>Второй телефон:</strong> <input type="text" ng-model="fighter.second_phone" size="{{(fighter.second_phone).length}}" /> </li>
                  <li><strong>e-mail:</strong> <input type="text" ng-model="fighter.email" size="{{(fighter.email).length}}" /></li>
                  <li><strong>День рождения: </strong><input type="text" ng-model="fighter.birthdate" size="{{(fighter.birthdate).length}}" /></li>
                  <li><strong>Год вступления в отряд: </strong><input type="text" ng-model="fighter.year_of_entrance" size="{{(fighter.year_of_entrance).length}}" /></li>
                  <li><strong>Права доступа: </strong><input type="text" ng-model="fighter.group_of_rights" size="{{(fighter.group_of_rights).length}}" /></li>
                  
                </ul>
              </div>
            </div>
          </form>
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
    <button type="button" class="btn btn-info get-all">а можно всех посмотреть?</button>
    <div class="table-container" ng-cloak ng-controller="fightersApp">
    <button type="button" class="own-hidden btn btn-info vCard-start" ng-click="toggleChecking()">Кого хотите посмотреть?</button>
    <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-all" ng-click="checkAll()">Выбрать всех</button>
    <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-none" ng-click="uncheckAll()">Снять выбор</button>
    <button type="button" class="btn btn-success own-hidden vCard-get" disabled="disabled" ng-click="showSelected()">Посмотреть контакты</button>
    <br/>
    <input type="text" class="vCard-category own-hidden" placeholder="назначить группу для контактов" size=30 />
    <button type="button" class="btn btn-success own-hidden vCard-make" ng-click="makeCard()">импорт в <abbr title='формат записной книжки для Android, iPhone и т.д.'>vCard</abbr></button>

      <div class="search_wrap hidden"> Search: <input class="search" ng-model="query"></div>
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
            <td class="ids {{hidden_ids}}"><a href='users/{{fighter.id}}'>{{fighter.id}}</a></td>
            <td class="inputs {{hidden_inputs}}"> 
              <input type="checkbox" checklist-model="fighters.selected_f" checklist-value="fighter" ng-click="checkClicked()">
            </td>
            <td><strong>{{fighter.name}} {{fighter.surname}}</strong></td>
            <td>{{fighter.year_of_entrance}}</td>
          </tr>
        </tbody>
      </table>
      <table class="table direct-contacts hidden">
        <thead>
          <tr>
            <th>#</th>
            <th>фото</th>
            <th>данные</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="fighter in fighters.selected_f | orderBy:id" class="{{fighter.id}}">
            <td><a href='users/{{fighter.id}}'>{{fighter.id}}</a></td>
            <td><img ng-src="{{fighter.photo_100}}" /></td>
            <td>
              <ul>
                <li><strong>ФИО:</strong> {{fighter.surname}} <span ng-show="fighter.maiden_name">({{fighter.maiden_name}}) </span>{{fighter.name}} {{fighter.second_name}} </li>
                <li ng-show="fighter.phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.phone}}'> {{goodView(fighter.phone)}} </a></li>
                <li ng-show="fighter.second_phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.second_phone}}'> {{goodView(fighter.second_phone)}} </a></li>
                <li ng-show="fighter.email"><strong>e-mail:</strong><a href='mailto:{{fighter.email}}'> {{fighter.email}} </a></li>
                <li ng-show="fighter.vk_domain"><strong>vk:</strong> <a target='_blank' href='//vk.com/{{fighter.vk_domain}}'>vk.com/{{fighter.vk_domain}}</a></li>
              </ul>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <?php
      }
    ?>
  </div><!-- page-container -->
<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">

  <!-- скрипт для сохранения файла из js -->
  <script src="/standart/js/FileSaver.js"></script>

  <script type="text/javascript" src="/standart/js/angular.js"></script>

   <script type="text/javascript" src="/own/js/users/all.js"></script>

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
