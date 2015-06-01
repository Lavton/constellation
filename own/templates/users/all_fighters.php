<?php /*смотрим всех бойцов*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
?>

<div class="table-container" ng-cloak ng-controller="fightersApp">
<!-- поиск по бойцам -->  
  <div class="search_wrap"> Search: <input class="search" ng-model="query"></div>

<!-- табличка, в которой будут все контакты бойцов -->
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
      <tr ng-repeat="fighter in fighters | filter:query" class="{{fighter.id}}">
        <td><a href='users/{{fighter.id}}' class="ajax-nav">{{fighter.id}}</a></td>
        <td><img ng-src="{{fighter.photo}}" /></td>
        <td>
          <ul>
            <li><strong>ФИО:</strong> {{fighter.last_name}} <span ng-show="fighter.maiden_name">({{fighter.maiden_name}}) </span>{{fighter.first_name}} {{fighter.second_name}} </li>
            <li ng-show="fighter.phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.phone}}'> {{goodView(fighter.phone)}} </a></li>
            <li ng-show="fighter.second_phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.second_phone}}'> {{goodView(fighter.second_phone)}} </a></li>
            <li ng-show="fighter.email"><strong>e-mail:</strong><a href='mailto:{{fighter.email}}'> {{fighter.email}} </a></li>
            <li ng-show="fighter.domain"><strong>vk:</strong> <a target='_blank' href='//vk.com/{{fighter.domain}}'>vk.com/{{fighter.domain}}</a></li>
            <li ng-show="fighter.Instagram_id"><strong>instagram:</strong> <a target='_blank' href='//instagram.com/{{fighter.Instagram_id}}'>instagram.com/{{fighter.Instagram_id}}</a></li>
          </ul>
        </td>
      </tr>
    </tbody>
  </table>

<!-- табличка, в которой будут все контакты бойцов -->


</div> <!-- table-container -->

<?php /*комсостав+ может добавлять новых бойцов*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
?><br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-new" >Добавить нового бойца?</button> 
 <span class="add-new-input-w hidden">  vk.com/<input type="text" class="add-new-fighter-d" placeholder="введите домен вконтакте" size=30 />
         id: <input type="number" class="add-new-fighter-id" />

  </span>
<?php } ?>


<?php } ?>