<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="/own/images/icon.ico">
    <title>CПО "СОзвездие" | будущий сайт отряда</title>
    <link rel="stylesheet" href="/standart/css/bootstrap.css"/>
    <link rel="stylesheet" href="/standart/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="own/css/common_style.css">
</head>
<body>
  <?php
    include('menu.html');
     session_start();
  ?>
      
  <div class="container lead">
    <div class="starter-template">
      <h1>Для незарегистрированных пользователей</h1>
      <p class="lead">Тут просто написано, какие мы крутые</p>
      <p class="lead">что мы пед отряд<br/>
        что мы при Политехе, СПб<br/>
        что мы ездим в лен область и на море<br/>
        <br/>
        ну и как с нами связаться линк
      </p>
    </div>
    ______________________________________
    <div class="news">
      <h1> для зарегистрированных:<br/>Новости</h1>
      возможно это будет вкладками, возможно как-то ещё
      <div class="jumbotron">

        <!-- Example row of columns -->
        <div class="row">
          <div class="col-md-4">
            <h2>Отрядные мероприятия</h2>
            <p>Тут новости по мероприятиям. К которым сейчас вот готовимся. Линки ведут в обсуждения мероприятий</p>
            <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
          </div>
          <div class="col-md-4">
            <h2>Интересные события в СПб</h2>
            <p>Тут любой боец может написать про что-нибудь интересное, на что он хочет пригласить СО*</p>
            <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
         </div>
          <div class="col-md-4">
            <h2>Для обучения</h2>
            <p>А тут - различные полезные курсы и обучающие мероприятия. Да, самостоятельным полем. Потому что мы педагогический отряд, нам надо учиться!</p>
            <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
          </div>
        </div>
        <hr>
      </div>
    </div>
  </div> 

  <script type="text/javascript" src="standart/js/jquery.js"></script>
  <script type="text/javascript" src="standart/js/underscore.js"></script>
  <script type="text/javascript" src="standart/js/backbone.js"></script>
  <script type="text/javascript" src="standart/js/jstree.js"></script>
  <script type="text/javascript" src="standart/js/bootstrap.js"></script>
</body>
</html>
