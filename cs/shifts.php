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
      Выберите смены, в период которых вы бы хотели посмотреть выезжающих людей.
      <br>
      <button ng-click="selectShifts()">Выбрать</button>
      <div ng-show="people">
        <hr>
        <div class="row">
          <!-- бойцы -->
          <div class="col-xs-6" ng-init="fighters=[]">
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
                    <a href="/about/users/{{person[0].id}}" class="ajax-nav">
                  боец
                </a>
                    </span>
                  </td>
                  <td>
                    <ol>
                      <li ng-repeat="shift in person">
                        <a href="/events/shifts/{{shift.shift}}" class="ajax-nav">
                      {{shift.name}} <span ng-show="shift.place">({{shift.place}})</span>, {{shift.fn_date.getFullYear()}} г.
                    </a> <span ng-hide="shift.isDet"> ({{shift.probability}}%)</span>
                      </li>
                    </ol>
                  </td>
                  <td>{{person.length}}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- таблица с записавшимися кандидатами -->
          <div class="col-xs-6" ng-init="candidats=[]">
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
                    <a href="/about/candidats/{{person[0].id}}" class="ajax-nav">
                  кандидат
                </a>
                  </td>
                  <td>
                    <ol>
                      <li ng-repeat="shift in person">
                        <a href="/events/shifts/{{shift.shift}}" class="ajax-nav">
                          {{shift.name}} <span ng-show="shift.place">({{shift.place}})</span>, {{shift.fn_date.getFullYear()}} г.
                        </a> <span ng-show="shift.probability"> ({{shift.probability}}%)</span>
                      </li>
                    </ol>
                  </td>
                  <td>{{person.length}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- row -->
        <hr>
      </div>
      <table>
        <tr ng-repeat="shift in shifts">
          <td>
            <input type="checkbox" ng-click="checkClicked(shift)" id="shift-{{shift.id}}">
          </td>
          <td>
            {{shift.name}} ({{shift.place}}), {{shift.fn_date.getFullYear()}} г.
            <a href="/events/shifts/{{shift.id}}"><img src="/own/images/link.png" width="15px"></a>
          </td>
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
