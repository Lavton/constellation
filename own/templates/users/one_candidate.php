<?php
if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
	/*просмотр профиля одного человека*/
?>
<div ng-cloak ng-controller="oneCandidateApp">
  <div class="user-info hidden">
    <div class="col-xs-12">
      <h2>{{candidate.first_name}} {{candidate.last_name}}
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
        <button type="button" class="btn btn-primary text-right" ng-click="editPerson()">Редактировать</button>
        <?php } ?>
      </h2>
      <hr>
      <span class="saved">  (Изменения сохранены)</span>
    </div>
    <div class="row own-row">
      <div class="col-xs-3">
        <img class="pull-left ava" ng-src="{{candidate.photo_200}}" width="200" height="200">
      </div>
      <div class="col-xs-9 info-str">
        <ul>
          <li ng-show="candidate.domain"><strong>vk: </strong><a target='_blank' href='//vk.com/{{candidate.domain}}'>vk.com/{{candidate.domain}}</a></li>
          <li ng-show="candidate.phone"><strong>Телефон:</strong><a href='tel:+7{{candidate.phone}}'> {{goodView(candidate.phone)}} </a></li>
          <li ng-show="candidate.birthdate"><strong>День рождения: </strong>{{candidate.birthdate | date: 'dd.MM.yyyy'}}</li>
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
      <h2>{{candidate.first_name}} {{candidate.last_name}}
          <input type="submit" class="btn btn-primary text-right" ng-click="editPerson()" value="Сохранить"></input>
          <button type="button" class="btn btn-primary text-right" ng-click="resetInfo(); editPerson()" >Отменить</input>
        </h2>
        <hr>
      </div>
      <div class="row own-row">
        <div class="col-xs-3">  
          <img class="pull-left ava" ng-src="{{candidate.photo_200}}" width="200" height="200">
        </div>
        <div class="col-xs-9 info-str">
          <ul>
            <li><strong>vk: </strong>vk.com/<input type="text" class="vk-domain" ng-model="candidate.domain" size="{{(candidate.domain).length}}" />   (uid: {{candidate.vk_id}})</li>
            <li><strong>Телефон:</strong> +7<input type="text" ng-model="candidate.phone" size="{{(candidate.phone).length}}" />  <em>(цифрами)</em></li>
            <li><strong>День рождения: </strong><input type="date" ng-model="candidate.birthdate" size="{{(candidate.birthdate).length}}" /></li>
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
<button type="button" class="btn btn-danger kill-candidate" ng-click="killCandidate()" >Удалить профиль</button> 
<?php
}
?>
</div>
<br/><br/><a href="#" class="profile_priv ajax-nav"><<предыдщий</a> &nbsp; &nbsp;
<a href="#" class="profile_next pull-right ajax-nav">следующий>></a>
<?php
}
?>
