<br>
<div class="events-container" ng-cloak ng-controller="eventsApp" id="events-all">
  <h2 ng-show="events.future">Грядущие</h2>
  <ul>
    <li ng-repeat="event in events.future">
       <p><span class="date {{event.start_date}}">{{formatDate(event.start_date)}}</span> <a href="/events/{{event.id}}" class="ajax-nav">{{event.EMname}}</a>  <a ng-show="event.parent_id" href="/events/{{event.parent_id}}"><em>({{event.parent_name}})</em></a>
       <br></p>
    </li>
  </ul>

  <h2 ng-show="events.actual">Текущие</h2>
  <ul>
    <li ng-repeat="event in events.actual">
      <p>{{formatDate(event.start_date)}}, в {{event.start_time}} <a href="/events/{{event.id}}" class="ajax-nav">{{event.EMname}}</a>  <a ng-show="event.parent_id" href="/events/{{event.parent_id}}"><em>({{event.parent_name}})</em></a>
       <br></p>
    </li>
  </ul>

  <hr>
  <button class="btn" ng-click="get_arhive(events.arhive_month)">Архив</button> с <input type="month" ng-model="events.arhive_month">

  <h2 ng-show="events.prev">Прошедшие</h2>
  <ul>
    <li ng-repeat="event in events.prev">
      <p>{{formatDate(event.start_date)}}, в {{event.start_time}} <a href="/events/{{event.id}}" class="ajax-nav">{{event.EMname}}</a>  <a ng-show="event.parent_id" href="/events/{{event.parent_id}}"><em>({{event.parent_name}})</em></a>
              {{event.base}}
       <br></p>
    </li>
  </ul>

<?php /*Все бойцы могут добавлять мероприятия (согласовав с комсоставом)*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
?>
<!-- создание мероприятия -->
<br/><br/>
      <button type="button" ng-click="addNewEvent()" class="btn btn-warning" >Создать мероприятие?</button> 
  <div ng-show="adding_new || edit_ev" ng-init="adding_new=false; edit_ev=false"> 
  <em>Поля, отмеченные * обязательны для заполнения<br></em>
      <span class="scrl"></span>
<br>
<button type="button" ng-click="addNewEventSubmit()" ng-show="adding_new" class="btn btn-success">Создать</button>
<br> 
 <span ng-hide="newevent.parent_id"> Базовое мероприятие:  <select ng-change="changeBase(newevent.base_id)" ng-model="newevent.base_id" ng-options="value.id as value.name for (key , value) in eventsBase"></select> </span><br>
 <span ng-hide="newevent.base_id"> Головное мероприятие: <select ng-model="newevent.parent_id" ng-options="value.id as value.name for (key , value) in pos_parents"></select></span> <br><br>
 название*: <input ng-model="newevent.name" placeholder="название мероприятия" size=50 /> <br>
 место: <input ng-model="newevent.place" placeholder="место мероприятия" size=50 /> <br><br>
 Дата начала*: <input type="text" class="date" ng-model="newevent.start_date">  {{newevent.start_date}}<br>
 Время начала*: <input type="text" ng-model="newevent.start_time"> {{newevent.start_time}} <br>
 Дата окончания*: <input type="text" class="date" ng-model="newevent.finish_date">  {{newevent.finish_date}}<br>
 Время окончания*: <input type="text" ng-model="newevent.finish_time"> {{newevent.finish_time}} <br><br>
 Контактое лицо: <input type="text" ng-model="newevent.contact"><br>
 Виден для*: <input type="number" min="1" max="7" ng-model="newevent.visibility" size="{{(newevent.visibility).length}}" ng-init="newevent.visibility=3"/> ({{window.groups[window.visibilities[newevent.visibility]].rus}}) <br>
 Описание: <br> <textarea ng-model="newevent.comments" class="bbcode" cols="20" rows="5"></textarea>
 <br>
 <button type="button" ng-click="addNewEventSubmit()" ng-show="adding_new" class="btn btn-success">Создать</button>
 </div>
<?php } ?>  

</div>



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
