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
        <em>{{event.id}}</em> <strong>{{event.name}}:</strong> {{event.comments}}
        </li>
       </ul>
      </div>
      Добавить базовое мероприятие:
      <button type="button" ng-click="addNewEvent()" class="btn btn-warning" >Создать мероприятие?</button> 
  <div ng-show="adding_new" ng-init="adding_new=false"> 
  <em>Поля, отмеченные * обязательны для заполнения<br></em>
<br>
<button type="button" ng-click="addNewEventSubmit()" class="btn btn-success">Создать</button>
<br>
 название*: <input ng-model="newevent.name" placeholder="название мероприятия" size=50 /> <br>
 Виден для*: <input type="number" min="1" max="7" ng-model="newevent.visibility" size="{{(newevent.visibility).length}}" ng-init="newevent.visibility=3"/> ({{groups[window.visibilities[newevent.visibility]].rus}}) <br>
 Описание: <input type="text" ng-model="newevent.comments" size="50">
 <br>
 <button type="button" ng-click="addNewEventSubmit()" class="btn btn-success">Создать</button>
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
