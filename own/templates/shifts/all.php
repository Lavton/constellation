<div class="shifts-container">
  <!-- идущие смены -->
  <div ng-cloak ng-controller="shiftsApp" id="all-shifts">
    <h2 ng-show="shifts.actual">Текущие</h2>
    <ul>
      <li ng-repeat="shift in shifts.actual">
        <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}
        <span ng-show="shift.place">({{shift.place}})</span>
        </a>
      </li>
    </ul>
    <!-- предстоящие смены -->
    <h2 ng-show="shifts.future">Грядущие</h2>
    <ul>
      <li ng-repeat="shift in shifts.future">
        <p> <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}}
        <span ng-show="shift.place">({{shift.place}})</span>
        </a> (Всего: {{1*shift.common}}, бойцов: {{1*shift.common_f}} / кандидатов: {{1*shift.common - 1*shift.common_f}})
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
        Бойцов: {{fighters.length}}
        <table class="table-bordered" width="100%">
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
                <img ng-src="{{person[0].photo_50}}" />
              </td>
              <td>{{person[0].last_name}} {{person[0].first_name}}</td>
              <td>
                <span ng-show="window.current_group>=window.groups.FIGHTER.num">
            <a href="/about/users/{{person[0].id}}" class="ajax-nav">
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
                    <a href="/events/shifts/{{shift.event}}" class="ajax-nav">
                {{shift.name}} <span ng-show="shift.place">({{shift.place}})</span>
              </a> ({{shift.probability}}%)
                  </li>
                </ol>
              </td>
              <td>{{person[0].length}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- таблица с записавшимися кандидатами -->
      <div class="col-xs-6">
        Кандидатов: {{candidats.length}}
        <table class="table-bordered" width="100%">
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
                <img ng-src="{{person[0].photo_50}}" />
              </td>
              <td>{{person[0].last_name}} {{person[0].first_name}}</td>
              <td>
                <span ng-show="window.current_group>=window.groups.FIGHTER.num">
                <a href="/about/candidats/{{person[0].id}}" class="ajax-nav">
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
                    <a href="/events/shifts/{{shift.event}}" class="ajax-nav">
                    {{shift.name}} <span ng-show="shift.place">({{shift.place}})</span>
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
        <a href="/events/shifts/{{shift.id}}" class="ajax-nav">{{shift.name}} {{shift.fn_date.getFullYear()}}
        <span ng-show="shift.place">({{shift.place}})</span>
        </a>
      </li>
    </ul>

<!-- создание мероприятия -->
<div ng-show="window.current_group>=window.groups.COMMAND_STAFF.num">
<br/><br/>
      <button type="button" ng-click="addNewEvent()" class="btn btn-warning" >Создать смену?</button> 
  <div ng-show="adding_new" ng-init="adding_new=false"> 
  <em>Поля, отмеченные * обязательны для заполнения<br></em>
      <span class="scrl"></span>
<br>
<button type="button" ng-click="addNewEventSubmit()" ng-show="adding_new" class="btn btn-success">Создать</button>
<br> 
 Дата начала*: <input type="date" class="date" ng-model="newevent.start_date" ng-change="onSetDate()">  {{newevent.start_date}}<br>
 Дата окончания*: <input type="date" class="date" ng-model="newevent.finish_date">  {{newevent.finish_date}}<br>
 название*: <input ng-model="newevent.name" placeholder="название смены" size=50 /> <em>например "Лето 1 смена" или "весна"</em> <br>
 место: <input ng-model="newevent.place" placeholder="место проведения смены" size=50 /> <br><br>
 Виден для*: <input type="number" min="1" max="7" ng-model="newevent.visibility" size="{{(newevent.visibility).length}}" ng-init="newevent.visibility=3"/> ({{window.groups[window.visibilities[newevent.visibility]].rus}}) <br>
 Описание: <br> <textarea ng-model="newevent.comments" class="bbcode" cols="20" rows="5"></textarea>
 <br>
 <button type="button" ng-click="addNewEventSubmit()" ng-show="adding_new" class="btn btn-success">Создать</button>
 </div>
 </div>
<!-- конец добавления -->


<!--     <div class="add-new-shift" ng-show="window.current_group>=window.groups.COMMAND_STAFF.num">
      <br/>
      <br/>
      <button type="button" class="btn btn-warning pre-add-new">Добавить новую смену?</button>
      <div class="add-new hidden">
        <br> Дата начала:
        <input type="date" class="add-new-start-date" />
        <br>Дата окончания:
        <input type="date" class="add-new-end-date" />
      </div>
    </div>
 -->  </div>
</div>
