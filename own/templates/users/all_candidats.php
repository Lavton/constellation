<?php /*смотрим всех бойцов*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
?>

<div id="all-cand" class="table-container" ng-cloak ng-controller="candidatsApp">
<!-- поиск по бойцам -->  
  <div class="search_wrap"> Search: <input class="search" ng-model="query"></div>

<!-- табличка, в которой будут все контакты кандидатов -->
<button ng-click="getMoreInfo()">Получить подробную информацию</button>
  <table class="table common-contacts">
    <thead>
      <tr>
        <th>#</th>
        <th>фото</th>
        <th>данные</th>
      </tr>
    </thead>
    <tbody>
      <tr ng-repeat="candidate in candidats | filter:query" class="{{candidate.id}}">
        <td><a href='candidats/{{candidate.id}}' class="ajax-nav">{{candidate.id}}</a></td>
        <td><img ng-src="{{candidate.photo}}" /></td>
        <td>
          <ul>
            <li><strong>ФИО:</strong> {{candidate.last_name}} {{candidate.first_name}} {{candidate.second_name}}</li>
            <li ng-show="candidate.phone"><strong>Телефон:</strong><a href='tel:+7{{candidate.phone}}'> {{goodView(candidate.phone)}} </a></li>
            <li ng-show="candidate.domain"><strong>vk:</strong> <a target='_blank' href='//vk.com/{{candidate.domain}}'>vk.com/{{candidate.domain}}</a></li>
            <li ng-show="candidate.birthdate"><strong>ДР:</strong> {{candidate.birthdate | date: 'dd.MM.yyyy'}} </li>
          </ul>
        </td>
      </tr>
    </tbody>
  </table>

</div> <!-- table-container -->

<?php /*комсостав+ может добавлять новых кандидатов*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
?><br/><br/><br/>
<div class="add-new-candidate">
  <button type="button" class="btn btn-warning pre-add-new">Добавить нового кандидата?</button>
  <span class="add-new-input-w hidden">  vk.com/<input type="text" class="add-new-d" placeholder="введите домен вконтакте" size=30 />
    id: <input type="number" class="add-new-id" />

  </span>
</div>
<?php } ?>


<?php } ?>