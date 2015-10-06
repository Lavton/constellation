<!DOCTYPE html>
<html>

<head lang="en">
  <title>люди | Комсоставу | CПО "СОзвездие"</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <?php check_session(); session_start(); if (isset($_SESSION[ "current_group"]) && ($_SESSION[ "current_group"]>= COMMAND_STAFF)) { ?>
    <div ng-cloak ng-controller="CSeventsApp" id="cs-events">
      <abbr title="Мероприятия, проводящиеся регулярно и на которые возможна подписка заранее">Базовые мероприятия:</abbr><br>
      <div ng-show="events">
      <ul>
        <li ng-repeat="event in events">
        <strong>{{event.name}}:</strong> {{event.comments}} 
        <a href="" ng-click="editEvent(event)"><img src="/own/images/edit.png" width="10px"></a>
        <a href="" ng-click="deleteEvent(event)"><img src="/own/images/delete.png" width="10px"></a>
        </li>
       </ul>
      </div>
      Добавить базовое мероприятие:
      <button type="button" ng-click="addNewEvent()" class="btn btn-warning" >Создать мероприятие?</button> 
  <div ng-show="adding_new || edit_ev" ng-init="adding_new=false; edit_ev=false"> 
  <em>Поля, отмеченные * обязательны для заполнения<br></em>
<br>
<button type="button" ng-click="addNewEventSubmit()" ng-show="adding_new && newevent.name" class="btn btn-success">Создать</button>
<button type="button" ng-click="editEventSubmit()" ng-show="edit_ev && newevent.name" class="btn btn-success">Редактировать</button>
<br>
 название*: <input ng-model="newevent.name" placeholder="название мероприятия" size=50 /> <br>
 Виден для*: <input type="number" min="1" max="7" ng-model="newevent.visibility" size="{{(newevent.visibility).length}}" ng-init="newevent.visibility=3"/> ({{groups[window.visibilities[newevent.visibility]].rus}}) <br>
 Описание: <input type="text" ng-model="newevent.comments" size="50">
 <br>
 <button type="button" ng-click="addNewEventSubmit()" ng-show="adding_new && newevent.name" class="btn btn-success">Создать</button>
 <button type="button" ng-click="editEventSubmit()" ng-show="edit_ev && newevent.name" class="btn btn-success">Редактировать</button>
 </div>

    </div>
    <?php } ?>
  </div>
  <!-- page-container -->
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/footer.php'); ?>
  <div id="after-js-container">
    <script type="text/javascript">
    $('.bbcode').markItUp(mySettings);
    </script>
  </div>
</body>

</html>
