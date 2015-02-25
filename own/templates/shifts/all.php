<?php /*смотрим всех*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
?>
<div class="table-container" ng-cloak ng-controller="shiftsApp">
    <div class="jumbotron">
    <!-- Example row of columns -->
    <div class="row">
      <div class="col-md-4">
        <h2>Текущие</h2>
     </div>
      <div class="col-md-4">
        <h2>Грядущие</h2>
        <a href="#">смена</a>
      </div>
      <div class="col-md-4">
        <h2>Прошедшие</h2>
      </div>
    </div>
    <hr>
  </div>

</div>
<?php } ?>