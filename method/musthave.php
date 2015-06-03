<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <p>Вообще, чемодан вожатого (да и сама вожатская) - как Греция. В них должно быть всё!
      <br> Но, чтобы иметь хоть небольшое представление о том, что это "всё" включает - воспользуйтесь нашем "Чемоданчиком вожатого"
    </p>
    <a href="resources/camod.png" target="_blank">(версия для сохранения)</a>
    <br>Кликайте на картинку и будет счастье!
    <br>
    <div class="camod-clicked">
      <div class="show_div">
        <button class="OK_button">OK</button>
        <br>
        <div>
        </div>
      </div>
      <img src="resources/camod_check.png" class="camod for_click" width=150%>
    </div>
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
    }, 7845634)
    </script>
    <script type="text/javascript">
    VK.Widgets.Comments("vk_comments", {
      limit: 10,
      width: "665",
      attach: "*",
      autoPublish: "0"
    }, 7845634);
    </script>
  </div>
</body>

</html>