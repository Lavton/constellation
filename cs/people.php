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
    <div ng-cloak ng-controller="CSpeopleApp" id="cs-people">
      Посмотреть дни рождения людей<br>
      <button ng-click="getBirthdays()">Смотреть</button>
      <br>
      <div ng-show="people">
      <ul>
        <li ng-repeat="person in people">
          <img ng-src="{{person.photo}}" width="30px">
          <a href="/about/{{person.url_s}}/{{person.id}}"> {{person.first_name}} {{person.last_name}}</a> 
          ДР: {{person.birthdate | date: 'dd.MM.yyyy'}}  Осталось дней: {{(person.dayFromYear - today + 365 - 1) % 365}} 
          <span ng-show="person.shift">
            <a href="/events/shifts/{{person.shift.id}}" class="ajax-nav">
              {{person.shift.time_name}} <span ng-show="person.shift.place">({{person.shift.place}})</span>, {{person.shift.fn_date.getFullYear()}} г.
            </a>
          </span>
        </li>
      </ul>
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
