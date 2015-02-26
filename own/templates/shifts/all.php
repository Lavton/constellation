<?php /*смотрим всех*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
?>
<div class="shifts-container" ng-cloak ng-controller="shiftsApp">
  <h2>Грядущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.future">
      <a href="/events/shifts/{{shift.id}}">{{shift.name}}</a>
    </li>
  </ul>

  <h2>Текущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.actual">
      <a href="/events/shifts/{{shift.id}}">{{shift.name}}</a>
    </li>
  </ul>

  <hr>
  <h2>Прошедшие</h2>
  <ul>
    <li ng-repeat="shift in shifts.prev">
      <a href="/events/shifts/{{shift.id}}">{{shift.name}}</a>
    </li>
  </ul>

</div>
<?php } ?>