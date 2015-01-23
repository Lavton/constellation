<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
  ?>

</head>
<body>
  <?php
    include($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
    session_start();
    /*если ты боец+, то перенаправляют на нумеровочку*/
    if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) {
  ?>
  <?php
  include($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
  ?>
  <div id="after-js-container">
    <script type="text/javascript">
    // эмулируем нажатие по ссылке
      $("nav a[href='/about/users']").trigger("click");
    </script>
  </div>
  <?php
    } else {
        /*иначе - на историю отряда (нумеровочка недоступна)*/
  ?>
  <div id="after-js-container">
    <script type="text/javascript">
      $("nav a[href='/about/history']").trigger("click");
    </script>
  </div>
  <?php
    }  
  ?>
</body>
</html>