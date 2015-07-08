<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <p>Данная страница помогает сохранить данетки с сайте <a href="http://www.danetka.ru/">danetka.ru</a> в удобном для вожатых формате, а именно списком с вопросами-ответами.<br>
    Он работает медленно, дабы не нагружать сервер, на котором находится данный сайт, ровно как и сервер с базой ситуаций.
    </p>
    <div ng-cloak ng-controller="parseDNApp" id="parse-DN">
      <button ng-click="getPages()" class="getList">получить список страниц</button><br>
      <ul ng-show="listPages">
        Выбрано: {{num_of_select}}, займёт {{num_of_select * 3}} мин.
        <li ng-repeat="pages in listPages">
          <input type="checkbox" ng-click="swithCheck(pages)"> {{pages.name}} <a href="{{pages.url}}" target="_blank">{{pages.url}}</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- /container -->
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/footer.php'); ?>
  <div id="after-js-container">
  </div>
</body>

</html>