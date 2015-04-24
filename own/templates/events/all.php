<br>
<?php /*комсостав+ может добавлять новые мероприятия. Но пока видно лишь админу))*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= ADMIN))) {
?>
<div class="events-container" ng-cloak ng-controller="eventsApp">
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




<br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-new-event" >Создать мероприятие?</button> 
 <div class="add-new-input-w hidden">	
 название: <input type="text" class="add-new-event-name" placeholder="название мероприятия" size=50 /> <br>
 дата и время начала: <input type="date" class="add-new-event-start-date" /> <input type="time" class="add-new-event-start-time"/><br><br>
 дата и время конца: <input type="date" class="add-new-event-end-date" /> <input type="time" class="add-new-event-end-time"/><br>
 </div>
<?php } ?>  
