<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
  function get_content() {
?>
  <div id="page-container">
    всё про мероприятия.<br/>
    Предстоящие. <br/>
    Актуальные (к которым сейчас надо готовиться)<br/>
    Прошедшие.<br/>
    <br/>
    для каждого мероприятия ветвь новостей и обсуждений. Функциональность для раскладки... <br/> 
  </div>
<?php
  }
  function get_js() {
?>
<div id="after-js-container">
  <script type="text/javascript">
      document.title = 'мероприятия | CПО "СОзвездие"';
  </script>
</div>
<?php    
  }
  if (is_ajax()) {
    /*Если поступил ajax-запрос, мы просто возвращаем содержимое двух контейнеров*/
    get_content();
    get_js();
    exit();
  }
  /*иначе - весь код страницы*/
?>

<!DOCTYPE html>
<html>
<head lang="en">
  <title>мероприятия | CПО "СОзвездие"</title>
  <?php
    include('own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include('own/templates/menu.php');
  ?>


<?php
  get_content();
  include('own/templates/footer.php');
  get_js();
?>
</body>
</html>
