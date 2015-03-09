<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
?>
<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>
      

<div id="page-container">
  <h1>Немножко о сайте</h1>
  <p>Вау! вы нашли эту страничку! определённо, талант<br/></p>
  <p>О чём она? Немножко старческих воспоминаний и множко технической информации.<br/>
  Так что, если вы не интересуетесь разработкой, лучше просто перейдите на <a href="/">главную</a>, скушайте печенек там, ещё чего-нибудь</p>
<br/><br/><br/><br/><br/><br/>
Ну, если вы ещё здесь...<br/>
Начнём историю
<p>Кто тут?<br/>
На данный момент сайт создаётся лишь одним человеком :(. Я очень надеюсь, что в скором времени
на месте этих строк появится что-то про "дружную команду разработчиков", но пока...
</p>
<p>Я - <a href="vk.com/lavton" target="_blank">Антон Лиознов</a>, боец СПО Созвездия набора 2012 года.
Если вас интересуют более подробные вещи, связанные с сайтом или же с интелект-картами в методическом отделе - пишите!</p>
<p>
Идея создания сайта возникла у весной 2014, когда я готовился стать методистом. (К слову, выборы проиграл и так им и не стал)<br/>
И возникла она по той простой причине, что хотелось систематизировать материалы к ШВМам, написанные до этого на разных бесконечных вордовских документов.<br/>
Плюс сыграла роль знакомство с  <a href="https://grails.org/" target="_blank">Grails</a> летом 2014. (хотя он и не используется тут сейчас) и в принципе с веб-технологиями. <br/>
Создав версию на grails и написав там первые 4 ШВМа, эта идея несколько заглохла.
</p>
<p>
Осенью с твёрдой уверенностью, что сайт надо будет сделать, я взял практику в <a href="https://compscicenter.ru/" target="_blank">Computer Science Center</a> посвящённую вебу <br/>
И вот, закончив её, под новый год 2015 появились первые шаги для сайта!
</p><br>
<p>
Понятно дело, без VCS не обошлось. <a href="https://github.com/Lavton/constellation" target="_blank">ссылка на проект на GitHub</a>. Тут вы сможете найти актуальную версию исходников сайта.<br/>
</p><p>
Для бекенда был выбран Apache+PHP+MySQL (LAMP) по причине распространённости, и, как следствие, относительной дешевизны хостингов. Под Linux устанавливал по <a href="http://help.ubuntu.ru/wiki/web-server" target="_blank">такому мануалу</a><br/>
</p><p>
Фронтенд представляет собой адскую помесь с <a href="getbootstrap.com" target="_blank">Bootstrap</a>'овским CSS и 
JavaScript (ядро логики - <a href="angular.ru" target="_blank">angularJs</a>, с большой примесью <a href="http://jquery.com/" target="_blank">jQuery</a>, и вспомогательным <a href="underscorejs.org" target="_blank">underscore</a>)
</p>
<p>Например, большая часть обмена данными - <a href="http://labs.jonsuh.com/jquery-ajax-php-json/" target="_blank">ajax</a>, хотя сами данные нередко используются в angular.
Ну и <a href="http://habrahabr.ru/post/154617/" target="_blank">навигация</a> тоже чисто на jquery. <br>
Как вы поняли, исходники фронтенда просто ужастны! К счастью, всё это работает D. Если вы - боец СО* и сможете их сделать нормальными - это будет просто чудо!)
</p>
<p>Когда я возился с виджетом для инстаграмма очень помог <a href="http://inwidget.ru/" target="_blank">inwidget</a>.<br/>
Чтобы запускать angular, подгружая сам код асинхронно - <a href="https://docs.angularjs.org/guide/bootstrap" target="_blank">ручная загрузка</a>. <br>
<a href="http://vitalets.github.io/checklist-model/" target="_blank">А вот эта штука</a> оказалась крайне полезна для чекбоксов в цикле.
</p>
<p>Что касательно книг и подобного, то самые основы я восстанавливал в памяти по <a href="site-do.ru" target="_blank">site-do</a>,<br/>
А уж дальше пользовался <i>PHP 5 (Котеров, Костарев)</i>, <i>Ajax в действии (Крейн)</i>, <a href="http://habrahabr.ru/post/155107/" target="_blank">jQuery для начинающих</a>
</p>
</div> 


<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">

</div>

</body>
</html>
