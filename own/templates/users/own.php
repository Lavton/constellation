<div ng-cloak ng-controller="ownFighterApp">
  <div class="user-info hidden">
    <div class="col-xs-12">
      <h2>{{fighter.name}} {{fighter.second_name}} {{fighter.surname}} <span ng-show="fighter.maiden_name">({{fighter.maiden_name}})</span>
        <button type="button" class="btn btn-primary text-right" ng-click="editPerson()">Редактировать</button>
      </h2>
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
          <li ng-show="fighter.instagram_id"><strong>id в Instagram:</strong><a href='https://instagram.com/{{fighter.instagram_id}}' target="_blank"> {{fighter.instagram_id}} </a></li>
          <li ng-show="fighter.birthdate"><strong>День рождения: </strong>{{fighter.birthdate | date: 'dd.MM.yyyy'}}</li>
          <li ng-show="fighter.year_of_entrance"><strong>Год вступления в отряд: </strong>{{fighter.year_of_entrance}}</li>
          <li ng-show="fighter.group_of_rights"><strong>Права доступа: </strong>{{fighter.group_of_rights}} ({{groups[fighter.group_of_rights]}})</li>
        </ul>
      </div>
    </div>
  </div>
  <div class="user-edit hidden">
    <form ng-submit="submit()">
      <div class="col-xs-12">
      <h2>{{fighter.name}} {{fighter.second_name}} {{fighter.surname}} <span ng-show="fighter.maiden_name">({{fighter.maiden_name}})</span>
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
            <li ng-show="fighter.domain"><strong>vk: </strong><a target='_blank' href='//vk.com/{{fighter.domain}}'>vk.com/{{fighter.domain}}</a></li>
            <li><strong>Телефон:</strong> +7<input type="text" ng-model="fighter.phone" size="{{(fighter.phone).length}}" /> <em>(цифрами)</em></li>
            <li><strong>Второй телефон:</strong> +7<input type="text" class="change-s-phone" ng-model="fighter.second_phone" size="{{(fighter.second_phone).length}}" /> </li>
            <li><strong>e-mail:</strong> <input type="email" ng-model="fighter.email" size="{{(fighter.email).length}}" /></li>
            <li><strong>id в Instagram:</strong> <input ng-model="fighter.instagram_id" size="{{(fighter.intagram_id).length}}" /></li>
            <li><strong>День рождения: </strong><input type="date" ng-model="fighter.birthdate" size="{{(fighter.birthdate).length}}" /></li>
            <li ng-show="fighter.year_of_entrance"><strong>Год вступления в отряд: </strong>{{fighter.year_of_entrance}}</li>
            <li ng-show="fighter.group_of_rights"><strong>Права доступа: </strong>{{fighter.group_of_rights}} ({{groups[fighter.group_of_rights]}})</li>
          </ul>
        </div>
      </div>
    </form>
  </div>
</div>


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