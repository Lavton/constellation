<?php
if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
	/*просмотр смены*/
?>
<div ng-cloak ng-controller="oneShiftApp">
  <div class="shift-info hidden">
    <div class="col-xs-12">
      <h2>{{shift.name}}
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
        <button type="button" class="btn btn-primary text-right" ng-click="editShiftInfo()">Редактировать</button>
        <?php } ?>
      </h2>
      <hr>
      <span class="saved">  (Изменения сохранены)</span>
    </div>
    <div class="row own-row">
      <div class="col-xs-5 info-str">
        <ul>
          <li ng-show="shift.start_date"><strong>Дата начала:</strong> {{shift.start_date | date: 'dd.MM.yyyy'}} </li>
          <li ng-show="shift.finish_date"><strong>Дата окончания:</strong> {{shift.finish_date | date: 'dd.MM.yyyy'}} </li>
          <li ng-show="shift.place"><strong>Место:</strong> {{shift.place}} </li>
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
          <li ng-show="shift.visibility"><strong>Виден для: </strong>{{shift.visibility}} ({{groups[shift.visibility]}})</li>
          <?php } ?>   
          <li ng-show="shift.comments"><strong>Комментарии:</strong><br/> {{shift.comments}} </li>     
        </ul>
      </div>
    </div>
  </div>


<?php /*редактируют лишь ком состав и админ*/
if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { 
  ?> 
  <div class="shift-edit hidden">
    <form ng-submit="submit()">
      <div class="col-xs-12">
        <h2>{{shift.name}}
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
          <input type="submit" class="btn btn-primary text-right" ng-click="editShiftInfo()" value="Сохранить"></input>
            <button type="button" class="btn btn-primary text-right" ng-click="resetInfo(); editShiftInfo()" >Отменить</input>
          <?php } ?>
        </h2>        
        <hr>
      </div>
      <div class="row own-row">
        <div class="col-xs-5 info-str">
          <ul>
            <li><strong>Дата начала:</strong> <input type="date" ng-model="shift.start_date" size="{{(shift.start_date).length}}" /> </li>
            <li><strong>Дата окончания:</strong> <input type="date" ng-model="shift.finish_date" size="{{(shift.finish_date).length}}" /> </li>
            <li><strong>Место:</strong> <input type="text" ng-model="shift.place" size="{{(shift.place).length}}" /> </li>
            <li><strong>Виден для: </strong><input type="number" min="1" max="7" ng-model="shift.visibility" size="{{(shift.visibility).length}}" /> ({{groups[shift.visibility]}})</li>
            <li><strong>Комментарии:</strong><br/> <textarea ng-model="shift.comments" cols=50 rows=5></textarea>  </li>
          </ul>
        </div>
      </div>
    </form>
  </div>
<?php } ?>

</div>
<?php
}
?>
