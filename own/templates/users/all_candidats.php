<?php /*смотрим всех бойцов*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
?>

<div class="table-container" ng-cloak ng-controller="candidatsApp">
<!-- поиск по бойцам -->  
  <div class="search_wrap"> Search: <input class="search" ng-model="query"></div>

<!-- табличка, в которой будут все контакты бойцов -->
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
        <td><img ng-src="{{candidate.photo_100}}" /></td>
        <td>
          <ul>
            <li><strong>ФИ:</strong> {{candidate.second_name}} {{candidate.first_name}}</li>
            <li ng-show="candidate.phone"><strong>Телефон:</strong><a href='tel:+7{{candidate.phone}}'> {{goodView(candidate.phone)}} </a></li>
            <li ng-show="candidate.vk_domain"><strong>vk:</strong> <a target='_blank' href='//vk.com/{{candidate.vk_domain}}'>vk.com/{{candidate.vk_domain}}</a></li>
          </ul>
        </td>
      </tr>
    </tbody>
  </table>

</div> <!-- table-container -->

<?php /*комсостав+ может добавлять новых кандидатов*/
if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
?><br/><br/><br/>
  <button type="button" class="btn btn-warning pre-add-new-cand" >Добавить нового кандидата?</button> 
 <span class="add-new-input-cand-w hidden">  vk.com/<input type="text" class="add-new-candidate-d" placeholder="введите домен вконтакте" size=30 />
         id: <input type="number" class="add-new-candidate-id" />

  </span>
<?php } ?>


<?php } ?>