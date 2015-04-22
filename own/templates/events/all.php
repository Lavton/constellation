<div class="events-container" ng-cloak ng-controller="eventsApp">
  <h2 ng-show="events.future">Грядущие</h2>
  <ul>
    <li ng-repeat="event in events.future">
      <p><a href="/events/{{event.id}}" class="ajax-nav">{{event.name}}</a><br></p>
    </li>
  </ul>

  <h2 ng-show="events.actual">Текущие</h2>
  <ul>
    <li ng-repeat="event in events.actual">
      <a href="/events/{{event.id}}" class="ajax-nav">{{event.name}}</a>
    </li>
  </ul>

  <hr ng-show="events.prev">
  <h2 ng-show="events.prev">Прошедшие</h2>
  <ul>
    <li ng-repeat="event in events.prev">
      <a href="/events/{{event.id}}" class="ajax-nav">{{event.name}}</a>
    </li>
  </ul>

</div>




<?php /*комсостав+ может добавлять новые мероприятия*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= ADMIN))) {
?><br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-new-event" >Создать мероприятие?</button> 
 <div class="add-new-input-w hidden">	
 название: <input type="text" class="add-new-event-name" placeholder="название мероприятия" size=50 /> <br>
 дата и время начала: <input type="date" class="add-new-event-start-date" /> <input type="time" class="add-new-event-start-time"/><br><br>
 дата и время конца: <input type="date" class="add-new-event-end-date" /> <input type="time" class="add-new-event-end-time"/><br>
  </div>
<?php } ?>  
