<?php /*смотрим всех*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
?>
<div class="shifts-container" ng-cloak ng-controller="shiftsApp">
  <h2 ng-show="shifts.future">Грядущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.future">
      <a href="/events/shifts/{{shift.id}}">{{shift.name}}</a>
    </li>
  </ul>

  <h2 ng-show="shifts.actual">Текущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.actual">
      <a href="/events/shifts/{{shift.id}}">{{shift.name}}</a>
    </li>
  </ul>

  <hr ng-show="shifts.prev">
  <h2 ng-show="shifts.prev">Прошедшие</h2>
  <ul>
    <li ng-repeat="shift in shifts.prev">
      <a href="/events/shifts/{{shift.id}}">{{shift.name}}</a>
    </li>
  </ul>

</div>
<?php } ?>