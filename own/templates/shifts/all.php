<?php /*смотрим всех*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
?>
<div class="shifts-container" ng-cloak ng-controller="shiftsApp">
  <h2 ng-show="shifts.actual">Текущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.actual">
      <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a>
    </li>
  </ul>

  <h2 ng-show="shifts.future">Грядущие</h2>
  <ul>
    <li ng-repeat="shift in shifts.future">
      <p><a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a>  (Всего: {{1*shift.common}}, бойцов: {{1*shift.common_f}} / кандидатов: {{1*shift.common - 1*shift.common_f}})<br></p>
    </li>
  </ul>
  <hr>
  <!-- мини-сводка по людям -->
  <span ng-show="(shifts.future).length > 1">Всего на предстоящие смены записалось: {{Object.keys(shifts.people).length}} </span>
  <table  class="table-bordered" width="100%" ng-show="(shifts.future).length > 1">
    <thead>
      <tr>
        <th></th>
        <th>ФИО</th>
        <th>Статус</th>
        <th>Смены (вероятность поехать)</th>
        <th>&Sigma;</th>
      </tr>
      </thead>
      <tbody>
      <tr ng-repeat="(uid, person) in shifts.people">
        <td><a href={{"//vk.com/"+person[0].domain}} target="_blank"> <img ng-src="{{person[0].photo_50}}"/></a> 
        </td>
        <td>{{person[0].last_name}} {{person[0].first_name}}</td>
        <td>
          <span ng-show="person[0].fighter_id">
            <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) { ?>
            <a href={{"/about/users/"+person[0].fighter_id}} class="ajax-nav">
              <?php } ?>
              боец
            <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) { ?>
            </a>
              <?php } ?>
          </span>
          <span ng-hide="person[0].fighter_id">кандидат</span> &nbsp;
        </td>
        <td>
          <ol>
            <li ng-repeat="shift in person">
              <a href={{"/events/shifts/"+shift.shift_id}} class="ajax-nav">
                {{shift.shift_name}}
              </a> ({{shift.probability}}%)
            </li>
          </ol>
        </td>
        <td>{{person.length}}</td>
      </tr>
    </tbody>
  </table>
  <hr>
  <button class="btn" ng-click="get_arhive(shifts.arhive_year)">Архив</button> с <input type="number" ng-model="shifts.arhive_year" size="4" min="2010" max="{{shifts.max_arhive}}"/>
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
<?php } ?>  
