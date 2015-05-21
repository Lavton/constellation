<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>
    

  <div id="page-container">
    <p>В каждом возрасте свои особенности. Не забывайте об этом.
    </p>
    <p><em>когда-нибудь тут появятся ещё интеллект-карты</em></p>
    <ul>
    </ul>
    <ul>
      <li><a href="resources/small.pdf" target="_blank">про младших и младше-средних</a></li>
      <li><a href="resources/oldest.pdf" target="_blank"> про старших детей</a></li>
      <li><a href="resources/diff_ages.pdf" target="_blank">про разновозрастных детей</a></li>
    </ul>
    <?php /*смотрим всех*/
    if ((isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= OLD_FIGHTER))) {
    ?>

    <hr>
(для стариков, экс- и нынешнего комсостава, поток мыслей, которые я почерпнул из Аверена, книги по возрастной психологии)<br>
Хочется систематизации и интеллект-карты))
<div class="row own-row">
  <div class="col-xs-4">
    Дошкольники
    <ul>
      <li>Рисование - способ речи</li>
      <li>Страхи - персонажей, смерти</li>
      <li>Безусловная любовь</li>
      <li>Игры без взрослых не интересны</li>
      <li>Зрение > Слуха</li>
      <li>Игра = + к воображению, памяти, вниманию, мышлению, общению</li>
      <li>Взрослый - целостная личность, обладающая знаниями и умениями</li>
      <li>Уважение, взаимопомощь и сопереживание</li>
      <li>Добро и зло</li>
      <li>"Я сам"</li>
      <li>Родители сказали ехать</li>
      <li>Мораль: наказание, личная выгода</li>
    </ul>
  </div>
  <div class="col-xs-4">
    Младшие школьники
    <ul>
      <li>Мальчики - материальное поощрение, в одиночку</li>
      <li>Девочки - эмоциональное поощрение, в смешанной группе</li>
      <li>Ставить цели</li>
      <li>Произвольность психических процессов</li>
      <li>Планирование в уме</li>
      <li>Анализ</li>
      <li>Рефлексия</li>
      <li>Наблюдательность</li>
      <li>Сравнение</li>
      <li>Внимательность</li>
      <li>Внешние действия > Внутренних по вниманию</li>
      <li>Абстрагирование</li>
      <li>Конкретные операции</li>
      <li>Страх несоответсвия</li>
      <li>Страх сделать неправильно</li>
      <li>Не оценивает поступки</li>
      <li>Страх стихии</li>
      <li>Магическое мышление, суеверия</li>
      <li>Внушаемость</li>
      <li>Эгоцентризм (нет причин следствий)</li>
      <li>Интерес - от нас. Свобода и фантазия в играх</li>
      <li>Радость - следствие достижении цели</li>
      <li>Цели ставит взрослый. Надо учить ребёнка</li>
      <li>3-4 класс - общественное мнение</li>
      <li>4-5 класс - общение со сверстниками</li>
      <li>4-5 класс - чувство взрослости</li>
      <li>Мораль: одобрение другими (обществом), авторитет, закон</li>
    </ul>
  </div>
  <div class="col-xs-4">
    Подростки
    <ul>
      <li>Влияние среды</li>
      <li>Амбивалентность психической жизни</li>
      <li>Пубертат</li>
      <li>Нежелание "игр"</li>
      <li>"Серьёзная игра"</li>
      <li>Потребность найти и защитить своё место в социуме</li>
      <li>Самооценка</li>
      <li>Общение</li>
      <li>Самосознание</li>
      <li>Негативизм</li>
      <li>Половое, позновательное, социальное развитие</li>
      <li>Девочки: совместное общение > деятельности</li>
      <li>Умение обобщать, классификация</li>
      <li>Формирование своего "я"</li>
      <li>Чувство взрослости</li>
      <li>Групповая идентификация</li>
      <li>Одобрение себя</li>
      <li>Младшие - общение с товарищами</li>
      <li>Старшие - личностные качества</li>
      <li>Страх изменения</li>
      <li>Страх смерти</li>
      <li>Страхи межличностные</li>
      <li>Потребность в доброте</li>
      <li>Общественные нормы, ценности -> универсальные принципы</li>
      <li>Агрессивность</li>
    </ul>
  </div>


</div>

    <?php
    }
    ?>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  </div>
</body>
</html>
