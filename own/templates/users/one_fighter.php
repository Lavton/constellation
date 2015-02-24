<?php
if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
	/*просмотр профиля одного человека*/
?>
<div ng-cloak ng-controller="oneFighterApp">
  <div class="user-info hidden">
    <div class="col-xs-12">
      <h2>{{fighter.name}} {{fighter.second_name}} {{fighter.surname}} <span ng-show="fighter.maiden_name">({{fighter.maiden_name}})</span>
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
        <button type="button" class="btn btn-primary text-right" ng-click="editPerson()">Редактировать</button>
        <?php } ?>
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
          <li ng-show="fighter.birthdate"><strong>День рождения: </strong>{{fighter.birthdate | date: 'dd.MM.yyyy'}}</li>
          <li ng-show="fighter.year_of_entrance"><strong>Год вступления в отряд: </strong>{{fighter.year_of_entrance}}</li>
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
          <li ng-show="fighter.group_of_rights"><strong>Права доступа: </strong>{{fighter.group_of_rights}} ({{groups[fighter.group_of_rights]}})</li>
          <?php } ?>          
        </ul>
      </div>
    </div>
  </div>
<?php /*редактируют лишь ком состав и админ*/
if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { 
  ?> 
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
            <li><strong>Телефон:</strong> +7<input type="text" ng-model="fighter.phone" size="{{(fighter.phone).length}}" /> </li>
            <li><strong>Второй телефон:</strong> +7<input type="text" class="change-s-phone" ng-model="fighter.second_phone" size="{{(fighter.second_phone).length}}" /> </li>
            <li><strong>e-mail:</strong> <input type="email" ng-model="fighter.email" size="{{(fighter.email).length}}" /></li>
            <li><strong>День рождения: </strong><input type="date" ng-model="fighter.birthdate" size="{{(fighter.birthdate).length}}" /></li>
            <li><strong>Год вступления в отряд: </strong><input type="number" min="2000" max="3000" ng-model="fighter.year_of_entrance" size="{{(fighter.year_of_entrance).length}}" /></li>
            <li><strong>Права доступа: </strong><input type="number" min="1" max="7" ng-model="fighter.group_of_rights" size="{{(fighter.group_of_rights).length}}" /> ({{groups[fighter.group_of_rights]}})</li>
          </ul>
        </div>
      </div>
    </form>
  </div>
<?php } ?>
<?php
if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
  /*удалить из БД*/
?>
<button type="button" class="btn btn-danger kill-fighter" ng-click="killFighter()" >Удалить профиль</button> 
<?php
}
?>
</div>
<br/><br/><a href="#" class="profile_priv"><<предыдщий</a>
<a href="#" class="profile_next pull-right">следующий>></a>
<?php
}
?>
