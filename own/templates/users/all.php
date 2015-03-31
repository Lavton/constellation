<?php /*смотрим всех*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
?>
<button type="button" class="btn btn-info get-all">а можно всех посмотреть?</button>
<div class="table-container" ng-cloak ng-controller="fightersApp">
  <button type="button" class="own-hidden btn btn-info vCard-start" ng-click="toggleChecking()">Кого хотите посмотреть?</button>
  <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-all" ng-click="checkAll()">Выбрать всех</button>
  <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-none" ng-click="uncheckAll()">Снять выбор</button>
  <button type="button" class="btn btn-success own-hidden vCard-get" disabled="disabled" ng-click="showSelected()">Посмотреть контакты</button>
  <br/>
  <div class="vCard-creater own-hidden">

    <input type="text" class="vCard-category" placeholder="назначить группу для контактов" size=30 /> <br/>
    <span title="Не все программы корректно отображают формат записи без отчества и девичьей фамилии и норовят вставить их при наличии">Добавить доп инфу? <input type="checkbox" ng-model="fighters.has_second" /></span>
    <button type="button" class="btn btn-success vCard-make" ng-click="makeCard()">импорт в <abbr title='формат записной книжки для Android, iPhone и т.д.'>vCard</abbr></button>
  </div>
  <div class="search_wrap hidden"> Search: <input class="search" ng-model="query"></div>
  <table class="table common-contacts hidden table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>имя</th>
        <th>год вступления в отряд</th>
      </tr>
    </thead>
    <tbody>
      <tr ng-repeat="fighter in fighters | filter:query" class="{{fighter.id}}">
        <td class="ids {{hidden_ids}}"><a href='users/{{fighter.id}}' class="ajax-nav">{{fighter.id}}</a></td>
        <td class="inputs {{hidden_inputs}}"> 
          <input type="checkbox" checklist-model="fighters.selected_f" checklist-value="fighter" ng-click="checkClicked()">
        </td>
        <td><strong>{{fighter.name}} {{fighter.surname}}</strong></td>
        <td>{{fighter.year_of_entrance}}</td>
      </tr>
    </tbody>
  </table>
  <table class="table direct-contacts hidden">
    <thead>
      <tr>
        <th>#</th>
        <th>фото</th>
        <th>данные</th>
      </tr>
    </thead>
    <tbody>
      <tr ng-repeat="fighter in fighters.selected_f | orderBy:id" class="{{fighter.id}}">
        <td><a href='users/{{fighter.id}}' class="ajax-nav">{{fighter.id}}</a></td>
        <td><img ng-src="{{fighter.photo_100}}" /></td>
        <td>
          <ul>
            <li><strong>ФИО:</strong> {{fighter.surname}} <span ng-show="fighter.maiden_name">({{fighter.maiden_name}}) </span>{{fighter.name}} {{fighter.second_name}} </li>
            <li ng-show="fighter.phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.phone}}'> {{goodView(fighter.phone)}} </a></li>
            <li ng-show="fighter.second_phone"><strong>Телефон:</strong><a href='tel:+7{{fighter.second_phone}}'> {{goodView(fighter.second_phone)}} </a></li>
            <li ng-show="fighter.email"><strong>e-mail:</strong><a href='mailto:{{fighter.email}}'> {{fighter.email}} </a></li>
            <li ng-show="fighter.vk_domain"><strong>vk:</strong> <a target='_blank' href='//vk.com/{{fighter.vk_domain}}'>vk.com/{{fighter.vk_domain}}</a></li>
          </ul>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<?php /*комсостав+ может добавлять новых бойцов*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
?><br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-new" >Добавить нового бойца?</button> 
 <span class="add-new-input-w hidden">  vk.com/<input type="text" class="add-new-fighter-d" placeholder="введите домен вконтакте" size=30 />
         id: <input type="number" class="add-new-fighter-id" />

  </span>
<?php } ?>  
<?php } ?>