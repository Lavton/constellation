<div class="shifts-container">
  <!-- идущие смены -->
  <div ng-cloak ng-controller="shiftsApp" id="all-shifts">
    <h2 ng-show="shifts.actual">Текущие</h2>
    <ul>
      <li ng-repeat="shift in shifts.actual">
        <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a>
      </li>
    </ul>
    <!-- предстоящие смены -->
    <h2 ng-show="shifts.future">Грядущие</h2>
    <ul>
      <li ng-repeat="shift in shifts.future">
        <p><a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a> (Всего: {{1*shift.common}}, бойцов: {{1*shift.common_f}} / кандидатов: {{1*shift.common - 1*shift.common_f}})
          <br>
        </p>
      </li>
    </ul>
    <hr>
  </div>
  <!-- мини-сводка по людям -->
  <div ng-cloak ng-controller="shiftsAppPeople" id="all-shifts-people">
    <span ng-show="(shifts.future).length > 1">Всего на предстоящие смены записалось: {{Object.keys(shifts.people).length}}</span>
    <div class="row">
      <!-- бойцы -->
      <div class="col-xs-6">
        Бойцов: {{Object.keys(fighters).length}}
        <table class="table-bordered" width="100%" ng-show="(shifts.future).length > 1">
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
            <tr ng-repeat="(uid, person) in fighters">
              <td>
                <a href={{ "//vk.com/"+person[0].domain}} target="_blank"> <img ng-src="{{person[0].photo_50}}" /></a>
              </td>
              <td>{{person[0].last_name}} {{person[0].first_name}}</td>
              <td>
                <span ng-show="window.current_group>=window.groups.FIGHTER.num">
            <a href={{"/about/users/"+person[0].id}} class="ajax-nav">
              боец
            </a>
              </span>
                <span ng-hide="window.current_group>=window.groups.FIGHTER.num">
              боец
              </span>
              </td>
              <td>
                <ol>
                  <li ng-repeat="shift in person">
                    <a href={{ "/events/shifts/"+shift.shift_id}} class="ajax-nav">
                {{shift.shift_name}}
              </a> ({{shift.probability}}%)
                  </li>
                </ol>
              </td>
              <td>{{person.length}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- таблица с записавшимися кандидатами -->
      <div class="col-xs-6">
        Кандидатов: {{Object.keys(candidats).length}}
        <table class="table-bordered" width="100%" ng-show="(shifts.future).length > 1">
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
            <tr ng-repeat="(uid, person) in candidats">
              <td>
                <a href={{ "//vk.com/"+person[0].domain}} target="_blank"> <img ng-src="{{person[0].photo_50}}" /></a>
              </td>
              <td>{{person[0].last_name}} {{person[0].first_name}}</td>
              <td>
                <span ng-show="window.current_group>=window.groups.FIGHTER.num">
                <a href={{"/about/candidats/"+person[0].id}} class="ajax-nav">
                  кандидат
                </a>
              </span>
                <span ng-hide="window.current_group>=window.groups.FIGHTER.num">
              кандидат
              </span>
              </td>
              <td>
                <ol>
                  <li ng-repeat="shift in person">
                    <a href={{ "/events/shifts/"+shift.shift_id}} class="ajax-nav">
                {{shift.shift_name}}
              </a> ({{shift.probability}}%)
                  </li>
                </ol>
              </td>
              <td>{{person.length}}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- row, записавшиеся люди -->
  <!-- архивные смены -->
  <div ng-cloak ng-controller="shiftsAppPeople" id="all-shifts-arhive">
    <hr>
    <button class="btn" ng-click="get_arhive(shifts.arhive_year)">Архив</button> с
    <input type="number" ng-model="shifts.arhive_year" size="4" min="2010" max="{{shifts.max_arhive}}" />
    <h2 ng-show="shifts.prev">Прошедшие</h2>
    <ul>
      <li ng-repeat="shift in shifts.prev">
        <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}</a>
      </li>
    </ul>
    <!-- добавить новую смену -->
    <div ng-show="window.current_group>=window.groups.COMMAND_STAFF.num">
      <br/>
      <br/>
      <br/>
      <button type="button" class="btn btn-warning pre-add-s-new">Добавить новую смену.</button>
    </div>
  </div>
</div>
