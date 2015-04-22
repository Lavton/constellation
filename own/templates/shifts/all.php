<?php /*смотрим всех*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
?>
<div class="shifts-container" ng-cloak ng-controller="shiftsApp">
  <h2 ng-show="shifts.future">Грядущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.future">
      <p><a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a><br></p>
    </li>
  </ul>

  <h2 ng-show="shifts.actual">Текущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.actual">
      <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a>
    </li>
  </ul>

  <hr ng-show="shifts.prev">
  <h2 ng-show="shifts.prev">Прошедшие</h2>
  <ul>
    <li ng-repeat="shift in shifts.prev">
      <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a>
    </li>
  </ul>

</div>
<?php } ?>
<?php /*комсостав+ может добавлять новые смены*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
?><br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-s-new" >Добавить новую смену.</button> 
 
  </span>
<?php } ?>  
