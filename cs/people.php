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
          ДР: {{person.birthdate | date: 'dd.MM.yyyy'}}
        </li>

      </ul>

        <hr>
        <div class="row">
          <!-- бойцы -->
          <div class="col-xs-6" ng-init="fighters=[]">
            Бойцов: {{Object.keys(fighters).length}}
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
                    <img ng-src="{{person.photo_50}}" />
                  </td>
                  <td>{{person.last_name}} {{person.first_name}}</td>
                  <td>
                    <a href="/about/users/{{person.id}}" class="ajax-nav">
                  боец
                </a>
                    </span>
                  </td>
                  <td>
                    <ol>
                      <li ng-repeat="shift in person.shifts">
                        <a href="/events/shifts/{{shift.id}}" class="ajax-nav">
                      {{shift.time_name}} <span ng-show="shift.place">({{shift.place}})</span>, {{shift.fn_date.getFullYear()}} г.
                    </a> <span ng-show="shift.probability"> ({{shift.probability}}%)</span>
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
            Кандидатов: {{Object.keys(candidats).length}}
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
                    <img ng-src="{{person.photo_50}}" />
                  </td>
                  <td>{{person.last_name}} {{person.first_name}}</td>
                  <td>
                    <a href="/about/candidats/{{person.id}}" class="ajax-nav">
                  кандидат
                </a>
                  </td>
                  <td>
                    <ol>
                      <li ng-repeat="shift in person.shifts">
                        <a href="/events/shifts/{{shift.id}}" class="ajax-nav">
                          {{shift.time_name}} <span ng-show="shift.place">({{shift.place}})</span>, {{shift.fn_date.getFullYear()}} г.
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
            <input type="checkbox" ng-click="checkClicked(shift)">
          </td>
          <td>
            {{shift.time_name}} ({{shift.place}}), {{shift.fn_date.getFullYear()}} г.
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
