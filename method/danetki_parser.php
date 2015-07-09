<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <p>Данная страница помогает сохранить данетки с сайте <a href="http://www.danetka.ru/">danetka.ru</a>. Все "Ситуации" принадлежат тому сайту и их авторам, данная страница лишь записывает их в другом формате, а именно списком с вопросами-ответами.
      <br> Он работает медленно, дабы не нагружать сервер, на котором находится данный сайт, ровно как и сервер с базой ситуаций.
    </p>
    <div ng-cloak ng-controller="parseDNApp" id="parse-DN">
      <button ng-click="getListPages()" class="getList" ng-hide="get_list">получить список списков страниц</button>
      <button class="btn btn-info" ng-show="get_pages" ng-click="beginParse()">начать парсинг</button>
      <br>
      <div class="row">
        <div class="col-xs-5">
          <ul ng-show="listPages && !selectPages">
            Выбрано: {{num_of_select}}, займёт примерно {{num_of_select * 3}} мин.
            <li ng-repeat="pages in listPages">
              <input type="checkbox" ng-click="swithCheck(pages)"> {{pages.name}} <a href="{{pages.url}}" target="_blank">{{pages.url}}</a>
            </li>
          </ul>
          <ul ng-show="selectPages">
            Осталось: {{selectPages.length}} 
            До запуска парсинга следующей страницы осталось {{time_left}}
            <li ng-repeat="pages in selectPages">
              {{pages.name}} <a href="{{pages.url}}" target="_blank">{{pages.url}}</a>
            </li>
          </ul>
          <hr>
          <ul ng-show="sitPages.length">
          Страниц с ситуациями: {{sitPages.length}}
            <li ng-repeat="pages in sitPages">
              <a href="{{pages}}" target="_blank">{{pages}}</a>
            </li>
          </ul>
        </div>
        <div class="col-xs-7">
          Всего ситуаций: {{situations.length}} <br>
          <textarea ng-model="con_situations" readonly cols="100" rows="40"></textarea>
        </div>
      </div>
    </div>
    <!-- /container -->
    <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/footer.php'); ?>
    <div id="after-js-container">
    </div>
</body>

</html>
