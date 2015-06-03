<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <p>Лагерь - как маленькая жизнь. Время здесь течёт по другому, но за ним нужно следить. Закон 00!
    </p>
    <ul>
      <li><a href="resources/time.pdf" target="_blank">Распорядок дня + периоды смены</a></li>
      <hr>
      <li><a href="resources/org-hoz.pdf" target="_blank">ОргХозСбор</a></li>
      <li><a href="resources/coner.pdf" target="_blank">Отрядный уголок</a></li>
      <li><a href="resources/light_rules.pdf" target="_blank">Правила свечки</a></li>
      <hr> (кликабельно)
      <li>
        <a href="resources/periods.png" target="_blank">
          <img src="resources/periods.png" width=100%>
        </a>
      </li>
      <li>
        <a href="resources/coner_camp.png" target="_blank">
          <img src="resources/coner_camp.png" width=100%>
        </a>
      </li>
    </ul>
    <div style="width: 665px;
    margin: 0 auto;">
      <br>
      <hr>
      <div id="vk_like"></div>
      <div id="vk_comments"></div>
    </div>
  </div>
  <!-- /container -->
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/footer.php'); ?>
  <div id="after-js-container">
    <script type="text/javascript">
    VK.Widgets.Like("vk_like", {
      type: "fill"
    }, 784531354)
    </script>
    <script type="text/javascript">
    VK.Widgets.Comments("vk_comments", {
      limit: 10,
      width: "665",
      attach: "*",
      autoPublish: "0"
    }, 784531354);
    </script>
  </div>
</body>

</html>