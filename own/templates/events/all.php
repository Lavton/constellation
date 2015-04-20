


<?php /*комсостав+ может добавлять новые мероприятия*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
?><br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-new-event" >Создать мероприятие?</button> 
 <span class="add-new-input-w hidden">  vk.com/<input type="text" class="add-new-fighter-d" placeholder="введите домен вконтакте" size=30 />
         id: <input type="number" class="add-new-fighter-id" />

  </span>
<?php } ?>  
