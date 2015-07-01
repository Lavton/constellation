<!DOCTYPE html>
<html>

<head lang="en">
  <title>смены | Комсоставу | CПО "СОзвездие"</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <?php check_session(); session_start(); if (isset($_SESSION[ "current_group"]) && ($_SESSION[ "current_group"]>= COMMAND_STAFF)) { ?>
    <div ng-cloak ng-controller="CSshiftsApp" id="cs-shifts">
      Выберите смены, в период которых вы бы хотели посмотреть выезжающих людей.<br>
      <button ng-click="selectShifts()">Выбрать</button>

      <table>
        <tr ng-repeat="shift in shifts">
          <td><input type="checkbox" ng-click="checkClicked(shift)"></td><td>{{shift.time_name}} ({{shift.place}}), {{shift.fn_date.getFullYear()}} г.</td>
        </tr>
      </table>
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
