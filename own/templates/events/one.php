<?php
if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
  /*просмотр мероприятия*/
?>
  <div ng-cloak ng-controller="oneEventApp" id="event-one">
    <br>
    <div class="event-info hidden">
      <div class="col-xs-12">
        <h2>{{event.name}}
        <button type="button" ng-show="event.editable" class="btn btn-primary text-right" ng-click="editEventInfo(true)">Редактировать описание мероприятия</button>
      </h2>
        <hr>
        <span class="saved">  (Изменения сохранены)</span>
      </div>
      <div class="row own-row">
        <div class="col-xs-5 info-str">
          <ul>
            <li ng-show="parent_event.name"><strong>Головное мероприятие:</strong> <a target="_blank" href="{{'/events/'+parent_event.id}}">{{parent_event.name}}</a> </li>
            <li ng-show="event.place || event.editable"><strong>Место:</strong> {{event.place}}</li>
            <li ng-show="event.start_time"><strong>Начало:</strong> {{event.start_time}} </li>
            <li ng-show="event.end_time"><strong>Дата окончания:</strong> {{event.end_time}} </li>
            <li ng-show="event.visibility"><strong>Виден для: </strong>{{event.visibility}} ({{window.groups[window.visibilities[event.visibility]].rus}})</li>
            <li ng-show="event.contact || event.editable"><strong>Контактное лицо:</strong> {{event.contact}} </li>
            <li ng-show="event.lastUpdated"><strong>Последнее обновление</strong> {{event.lastUpdated}}</li>
            <li ng-show="event.editor"><strong>Создал </strong> {{event.editor_user.name}} {{event.editor_user.surname}}</li>
            <li ng-show="event.comments"><strong>Комментарии:</strong>
              <br/>
              <div class="table-bordered bb-codes" ng-bind-html="event.bbcomments" ng-show="event.bbcomments"></div>
          </ul>
        </div>
        <div class="col-xs-7 info-str">
          <button ng-click="applyToEvent()">Записаться на мероприятие</button>
          <!-- <a href="" ng-click="exportToVK()">текст для ВК</a><br>
        <div ng-show="vk_export">
          <textarea ng-model="vk_export" rows="10" cols="80"></textarea>
        </div> -->
        </div>
      </div>
    </div>
    <span ng-show="event.editable">
  <div class="event-edit hidden">
    <form ng-submit="submit()">
      <div class="col-xs-12">
        <h2><input type="text" ng-model="event.name"/> 
          <input type="submit" class="btn btn-primary text-right" ng-click="editEventInfo()" value="Сохранить"></input>
            <button type="button" class="btn btn-primary text-right" ng-click="resetInfo(); editEventInfo()" >Отменить</input>
        </h2>        
        <hr>
      </div>
      <div class="row own-row">
        <div class="col-xs-5 info-str">
          <ul>
          <li>Головное мероприятие: <select ng-model="event.parent_id" ng-options="value.id as value.name for (key , value) in pos_parents"></select></li>
          <li>Место: <input ng-model="event.place"></li>
          <li>дата и время начала: <input type="date" ng-model="event.start_date"/> <input type="time" ng-model="event.start_ttime" /></li>
          <li>дата и время конца: <input type="date" ng-model="event.end_date" /> <input type="time" ng-model="event.end_ttime"/> </li>
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
            <li><strong>Виден для: </strong><input type="number" min="1" max="7" ng-model="event.visibility" size="{{(event.visibility).length}}" /> ({{groups[event.visibility]}})</li>
          <?php } else { ?> 
            <li ng-show="event.visibility"><strong>Виден для: </strong>{{event.visibility}} ({{groups[event.visibility]}})</li>
          <?php } ?>
          <li>Контактное лицо: <input type="text" ng-model="event.contact" size="{{(event.contact).length}}"/> <a href="" ng-click="setContactMe()">Я</a></li>
          <li><strong>Комментарии:</strong><br/> <textarea ng-model="event.comments" class="bbcode" cols="20" rows="5"></textarea>  </li>
          </ul>
        </div>
        <div class="col-xs-7 info-str">
         <button ng-click="applyToEvent(personToApply)">Записать на мероприятие</button>
         <input class="vk_input" ng-model="personToApply">
        </div>
      </div>
    </form>
  </div>
</span>
    <!-- edit -->
    <button type="button" ng-show="event.editable" class="btn btn-danger kill-event" ng-click="killEvent()">Удалить мероприятие</button>
    <?php
}
?>
  </div>
  <br/>
  <br/>
  <a href="#" class="event_priv ajax-nav">
    <<предыдщий</a> &nbsp; &nbsp;
      <a href="#" class="event_next pull-right ajax-nav">следующий>></a>
