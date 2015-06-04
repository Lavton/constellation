<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <p>Вожатый - это работа. И, как и у всякой работы, у вас будут свои права и свои обязанности.
    </p>
    <ul>
      <li><a href="resources/low_base.pdf" target="_blank">Законодательная база</a></li>
      <li><a href="resources/rights.pdf" target="_blank">Права и обязанности</a></li>
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
    }, 783123)
    </script>
    <script type="text/javascript">
    VK.Widgets.Comments("vk_comments", {
      limit: 10,
      width: "665",
      attach: "*",
      autoPublish: "0"
    }, 783123);
    </script>
  </div>
</body>

</html>