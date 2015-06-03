<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <p>В этом разделе будет всё, что вы можете сделать для детей, чтобы они не скучали</p>
    <ul>
      <li>чуть более, чем полная <a href="resources/games_classif.png" target="_blank">классификация игр</a></li>
    </ul>
    <div ng-cloak ng-controller="gameApp" class="games-container">
      <details>
        <summary>Добавить игру</summary>
        Это пока заготовка для добавления игры) скоро всё заработает!
        <!-- <i>да, классификация очень большая. Но вы ведь хотите потом быстро подбирать игры под себя в лагере?</i><br> -->
        <div class="row own-row">
          <div class="col-xs-4">
            <h4>Методические цели:</h4>
            <table class="table-bordered">
              <tbody>
                <tr>
                  <td>Знакомство</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.z" ng-init="game.adding.classify.method.z=true">
                  </td>
                </tr>
                <tr>
                  <td>Сплочение</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.s" ng-init="game.adding.classify.method.s=true">
                  </td>
                </tr>
                <tr>
                  <td>Розыгрыши</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.r" ng-init="game.adding.classify.method.r=true">
                  </td>
                </tr>
                <tr>
                  <td>Познавательные</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.p" ng-init="game.adding.classify.method.p=true">
                  </td>
                </tr>
                <tr>
                  <td>Развлекательные</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.razv" ng-init="game.adding.classify.method.razv=true">
                  </td>
                </tr>
                <tr>
                  <td>Выявление лидера</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.l" ng-init="game.adding.classify.method.l=true">
                  </td>
                </tr>
                <tr>
                  <td>Минутки</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.m" ng-init="game.adding.classify.method.m=true">
                  </td>
                </tr>
                <tr>
                  <td>Снятие агрессии</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.a" ng-init="game.adding.classify.method.a=true">
                  </td>
                </tr>
                <tr>
                  <td>Снятие тактильного напряжения</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.t" ng-init="game.adding.classify.method.t=true">
                  </td>
                </tr>
                <tr>
                  <td>Спортивная разрядка</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.method.sport" ng-init="game.adding.classify.method.sport=true">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-xs-4">
            <h4>Место проведения</h4>
            <table class="table-bordered">
              <tbody>
                <tr>
                  <td>На воздухе</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.place.v" ng-init="game.adding.classify.place.v=true">
                  </td>
                </tr>
                <tr>
                  <td>В лесу</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.place.l" ng-init="game.adding.classify.place.l=true">
                  </td>
                </tr>
                <tr>
                  <td>В воде</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.place.w" ng-init="game.adding.classify.place.w=true">
                  </td>
                </tr>
                <tr>
                  <td>В помещении</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.place.b" ng-init="game.adding.classify.place.b=true">
                  </td>
                </tr>
                <tr>
                  <td>В автобусе</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.place.a" ng-init="game.adding.classify.place.a=true">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-xs-4">
            <h4>Форма</h4>
            <table class="table-bordered">
              <tbody>
                <tr>
                  <td>Танцы</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.d" ng-init="game.adding.classify.form.d=true">
                  </td>
                </tr>
                <tr>
                  <td>Музыкальные</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.m" ng-init="game.adding.classify.form.m=true">
                  </td>
                </tr>
                <tr>
                  <td>Настольные</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.t" ng-init="game.adding.classify.form.t=true">
                  </td>
                </tr>
                <tr>
                  <td>Ролевые</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.r" ng-init="game.adding.classify.form.r=true">
                  </td>
                </tr>
                <tr>
                  <td>С залом</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.z" ng-init="game.adding.classify.form.z=true">
                  </td>
                </tr>
                <tr>
                  <td>Интеллектуальные</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.i" ng-init="game.adding.classify.form.i=true">
                  </td>
                </tr>
                <tr>
                  <td>Спортивные</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.s" ng-init="game.adding.classify.form.s=true">
                  </td>
                </tr>
                <tr>
                  <td>Тренинги</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.tr" ng-init="game.adding.classify.form.tr=true">
                  </td>
                </tr>
                <tr>
                  <td>Эстафеты</td>
                  <td>
                    <input type="checkbox" ng-model="game.adding.classify.form.e" ng-init="game.adding.classify.form.e=true">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </details>
    </div>
  </div>
  <!-- /container -->
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/footer.php'); ?>
  <div id="after-js-container">
    <script type="text/javascript" src="/own/js/games.js"></script>
  </div>
</body>

</html>