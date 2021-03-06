<?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/php_globals.php'); ?>
<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/header.php'); ?>
</head>

<body>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/menu.php'); ?>
  <div id="page-container">
    <h1>Немножко о сайте</h1>
    <p>Вау! вы нашли секретную страничку!
      <br/>
    </p>
    <p>Эта страница о нашем сайте, сайте Студенческого Педагогического Отряда "СОзвездие".
      <br> А конкретно про мотивацию, историю и технические детали его реализации.
    </p>
    <hr> Перед всем этим... В этом блоке комментов я буду писать про маленькие изменения, которые, с одной стороны, всё же были, но с другой - слишком малы для поста на стене группы.
    <div id="vk_comments"></div>
    <hr>
    <br/>
    <br/>
    <br/>
    <br/>
    <p>Первый вопрос - зачем?
      <br> И тут надо понять, чем плох ВКонтакте, который все мы постоянно используем.
      <br> Хорош ВК быстротой. Написанное сообщение сразу будет прочитано, если человек онлайн.
      <br> А плох он своей... "линейностью". Даже если вы написали на стене важное сообщение, через некоторое вреия оно убежит вниз. Как же в таком случае держать информацию, которая должна быть доступна всегда?
      <br> Документы, обсуждения, викистраницы - вариант. Но 1)они всегда на задворках страницы 2)их функционал крайне мал
      <br>
      <br> Отсюда следует уже две главных цели сайта -
      <ul>
        <li>Содержать информацию в удобном виде</li>
        <li>Автоматизировать те действия, которые сложно было делать ВК</li>
      </ul>
      Насколько эти цели достигнуты? Смотрите сами.
    </p>
    <p>В создании и наполнении сайта принимают участие все бойцы. Однако по техническим вопросам лучше писать мне, <a href="//vk.com/lavton" target="_blank">Антону Лиознову</a>.
    </p>
    <p>
      Идея создания сайта возникла весной 2014 (хотя, как выяснилось после, она поднималась ещё в 2012 году :)) И вот под новый год 2015 появились первые шаги для сайта!
      <br/> Первый коммит датируется 4 января 2015, что, наверно, и стоит считать днём рождения сайта)
    </p>
    <br>
    <hr> теперь про технологии
    <p>
      Понятно дело, без VCS не обошлось. <a href="https://github.com/Lavton/constellation" target="_blank">ссылка на проект на GitHub</a>. Тут вы сможете найти актуальную версию исходников сайта.
      <br/>
    </p>
    <p>
      Для бекенда был выбран Apache+PHP+MySQL (LAMP) по причине распространённости, и, как следствие, относительной дешевизны хостингов. Под Linux устанавливал по <a href="http://help.ubuntu.ru/wiki/web-server" target="_blank">такому мануалу</a>
      <br/>
    </p>
    <p>
      Фронтенд представляет собой адскую помесь с <a href="//getbootstrap.com" target="_blank">Bootstrap</a>'овским CSS и JavaScript (ядро логики - <a href="//angular.ru" target="_blank">angularJs</a>, с большой примесью <a href="http://jquery.com/" target="_blank">jQuery</a>, и вспомогательным <a href="//underscorejs.org" target="_blank">underscore</a>)
    </p>
    <p>Например, большая часть обмена данными - <a href="http://labs.jonsuh.com/jquery-ajax-php-json/" target="_blank">ajax</a>, хотя сами данные нередко используются в angular. Ну и <a href="http://habrahabr.ru/post/154617/" target="_blank">навигация</a> тоже чисто на jquery.
      <br> Как вы поняли, исходники фронтенда просто ужастны! К счастью, всё это работает D. Если вы - боец СО* и сможете их сделать нормальными - это будет просто чудо!)
    </p>
    <p>Когда я возился с виджетом для инстаграмма очень помог <a href="http://inwidget.ru/" target="_blank">inwidget</a>.
      <br/> Чтобы запускать angular, подгружая сам код асинхронно - <a href="https://docs.angularjs.org/guide/bootstrap" target="_blank">ручная загрузка</a>.
      <br>
      <a href="http://vitalets.github.io/checklist-model/" target="_blank">А вот эта штука</a> оказалась крайне полезна для чекбоксов в цикле.
    </p>
    <p>Что касательно книг и подобного, то самые основы я восстанавливал в памяти по <a href="//site-do.ru" target="_blank">site-do</a>,
      <br/> А уж дальше пользовался <i>PHP 5 (Котеров, Костарев)</i>, <i>Ajax в действии (Крейн)</i>, <a href="http://habrahabr.ru/post/155107/" target="_blank">jQuery для начинающих</a>
    </p>
    <p>Для данного сайта используется бесплатный хостинг <a href="http://www.hostinger.ru/" target="_blank">Hostinger</a>. Пока ни одного пререкания к нему нет.</p>
  </div>
  <?php include_once($_SERVER[ 'DOCUMENT_ROOT']. '/own/templates/footer.php'); ?>
  <div id="after-js-container">
    <script type="text/javascript">
    VK.Widgets.Comments("vk_comments", {
      limit: 40,
      width: "665",
      attach: "*",
      autoPublish: "0"
    }, 901231231);
    </script>
  </div>
</body>

</html>