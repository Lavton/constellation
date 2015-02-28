<?php
if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
	/*просмотр смены*/
?>
<div ng-cloak ng-controller="oneShiftApp">
  <div class="shift-info hidden">
    <div class="col-xs-12">
      <h2>{{shift.name}}
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
        <button type="button" class="btn btn-primary text-right" ng-click="editPerson()">Редактировать</button>
        <?php } ?>
      </h2>
      <hr>
      <span class="saved">  (Изменения сохранены)</span>
    </div>
  </div>

</div>

<?php
}
?>