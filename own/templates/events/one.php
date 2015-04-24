<?php
if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
  /*просмотр мероприятия*/
?>
<div ng-cloak ng-controller="oneEventApp">
  <br>

  <div class="event-info hidden">
    <div class="col-xs-12">
      <h2>{{event.name}}
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
        <button type="button" class="btn btn-primary text-right" ng-click="editEventInfo(true)">Редактировать описание мероприятия</button>
        <?php } ?>
      </h2>
      <hr>
      <span class="saved">  (Изменения сохранены)</span>
    </div>
    <div class="row own-row">
      <div class="col-xs-5 info-str">
        <ul>
          <li ng-show="parent_event.name"><strong>Головное мероприятие:</strong> <a target="_blank" href="{{'/events/'+parent_event.id}}">{{parent_event.name}}</a> </li>
          <li ng-show="event.start_time"><strong>Начало:</strong> {{event.start_time}} </li>
          <li ng-show="event.end_time"><strong>Дата окончания:</strong> {{event.end_time}} </li>
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
          <li ng-show="event.visibility"><strong>Виден для: </strong>{{event.visibility}} ({{groups[event.visibility]}})</li>
          <?php } ?>   
          <li ng-show="event.comments"><strong>Комментарии:</strong><br/> 
            <div class="table-bordered" ng-bind-html="event.bbcomments" ng-show="event.bbcomments"></div>
        </ul>
      </div>
      <div class="col-xs-7 info-str">
      </div>
    </div>
  </div>


<?php /*редактируют лишь ком состав и админ*/
if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) {
  ?> 
  <div class="event-edit hidden">
    <form ng-submit="submit()">
      <div class="col-xs-12">
        <h2><input type="text" ng-model="event.name"/> 
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
          <input type="submit" class="btn btn-primary text-right" ng-click="editEventInfo()" value="Сохранить"></input>
            <button type="button" class="btn btn-primary text-right" ng-click="resetInfo(); editEventInfo()" >Отменить</input>
          <?php } ?>
        </h2>        
        <hr>
      </div>
      <div class="row own-row">
        <div class="col-xs-5 info-str">
          <ul>
          <li>Головное мероприятие: <select ng-model="event.parent_id" ng-options="value.id as value.name for (key , value) in pos_parents"></select></li>
          <li>дата и время начала: <input type="date" ng-model="event.start_date"/> <input type="time" ng-model="event.start_ttime" /></li>
          <li>дата и время конца: <input type="date" ng-model="event.end_date" /> <input type="time" ng-model="event.end_ttime"/> </li>
          <li><strong>Виден для: </strong><input type="number" min="1" max="7" ng-model="event.visibility" size="{{(event.visibility).length}}" /> ({{groups[event.visibility]}})</li>
          <li><strong>Комментарии:</strong><br/> <textarea ng-model="event.comments" class="bbcode" cols="20" rows="5"></textarea>  </li>
          </ul>
        </div>
      </div>
    </form>
  </div>
<?php } ?>
<?php
if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
  /*удалить из БД*/
?>
<button type="button" class="btn btn-danger kill-event" ng-click="killEvent()" >Удалить мероприятие</button> 
<?php
}
}
?>
</div>

<br/><br/><a href="#" class="event_priv ajax-nav"><<предыдщий</a> &nbsp; &nbsp;
<a href="#" class="event_next pull-right ajax-nav">следующий>></a>
