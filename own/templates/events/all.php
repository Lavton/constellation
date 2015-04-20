


<?php /*комсостав+ может добавлять новые мероприятия*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= ADMIN))) {
?><br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-new-event" >Создать мероприятие?</button> 
 <div class="add-new-input-w hidden">	
 название: <input type="text" class="add-new-event-name" placeholder="название мероприятия" size=50 /> <br>
 дата и время начала: <input type="date" class="add-new-event-start-date" /> <input type="time" class="add-new-event-start-time"/><br>
 дата и время конца: <input type="date" class="add-new-event-end-date" /> <input type="time" class="add-new-event-end-time"/><br>
  </div>
<?php } ?>  
