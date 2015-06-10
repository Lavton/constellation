<br>
<div class="events-container" ng-cloak ng-controller="eventsApp" id="events-all">
  <h2 ng-show="events.future">Грядущие</h2>
  <ul>
    <li ng-repeat="event in events.future">
       <p>{{event.start_time}} <a href="/events/{{event.id}}" class="ajax-nav">{{event.name}}</a>  <a ng-show="event.parent_id" href="/events/{{event.parent_id}}"><em>({{event.parent_name}})</em></a><br></p>
    </li>
  </ul>

  <h2 ng-show="events.actual">Текущие</h2>
  <ul>
    <li ng-repeat="event in events.actual">
      {{event.start_time}} <a href="/events/{{event.id}}" class="ajax-nav">{{event.name}}</a>  <a ng-show="event.parent_id" href="/events/{{event.parent_id}}"><em>({{event.parent_name}})</em></a>
    </li>
  </ul>

  <hr>
  <button class="btn" ng-click="get_arhive(events.arhive_month)">Архив</button> с <input type="month" ng-model="events.arhive_month">

  <h2 ng-show="events.prev">Прошедшие</h2>
  <ul>
    <li ng-repeat="event in events.prev">
      {{event.start_time}} <a href="/events/{{event.id}}" class="ajax-nav">{{event.name}}</a> <a ng-show="event.parent_id" href="/events/{{event.parent_id}}"><em>({{event.parent_name}})</em></a>
    </li>
  </ul>

</div>



<?php /*Все бойцы могут добавлять мероприятия (согласовав с комсоставом)*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
?>

<br/><br/>
<br/>
  <button type="button" class="btn btn-warning pre-add-new-event" >Создать мероприятие?</button> 
 <div class="add-new-input-w hidden">	
  <em>Все поля обязательны для заполнения. Если вы не знаете, что ставить - поставьте что-нибудь :)<br>
Если вы не в комсоставе, вы можете создавать мероприятия только для других бойцов. Попросите комсостав, чтобы он изменил область видимости созданного вами мероприятия, если вы хотите, чтобы оно было видно кандидатам
</em>
<br>
 название: <input type="text" class="add-new-event-name" placeholder="название мероприятия" size=50 /> <br>
 дата и время начала: <input type="date" class="add-new-event-start-date" /> <input type="time" class="add-new-event-start-time"/> <em>Пример: "2015-06-19" "20:40"</em><br><br>
 дата и время конца: <input type="date" class="add-new-event-end-date" /> <input type="time" class="add-new-event-end-time"/><br>
 </div>
<?php } ?>  
<hr>
<?php /*,бойцам виден соотв. календарь .ics*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
?>
<p>Ссылка на .ics календарь для импорта в Google.Calendar 
<input readonly size="60" value="http://spo-sozvezdie.hol.es/events/so82fighter.ics?nocache"> <br>
Не давайте эту ссылку кандидатам, так как на ней нет проверки на то, боец ли вы (иначе Google не сможет импортировать)</p>

<?php
} elseif ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == CANDIDATE))) {
?>
<p>Ссылка на .ics календарь для импорта в Google.Calendar 
<input readonly size="60" value="http://spo-sozvezdie.hol.es/events/candidats.ics?nocache"> <br></p>

<?php
}
?>
Для импорта откройте <a href="https://www.google.com/calendar/render" target="_blank">календарь</a>, и в "других календарях", в выпадающем меню выберите "добавить по URL".<br>
После этого, чтобы добавить синхронизацию с приложением Календарь под Android, нажмите на "обновить" в правом верхнем углу, затем в меню->настройки, выберите импортированный календарь и установите флаг на "Синхронизация"
