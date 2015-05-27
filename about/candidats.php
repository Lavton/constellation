<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>
      
  <div id="page-container">
    <?php
      check_session();
      session_start();
      /*настройки своего профиля*/
      if (isset($_GET["id"]) && $_GET["id"] == 0) {
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/users/own.php');

      /*смотрим на чужой профиль (доступно >=бойцам)*/
      } elseif (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/users/one_candidate.php');
      /*не боец попытался посмотреть профиль*/
      } elseif (isset($_GET["id"])) {
        echo "Access denied";
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
        echo'</div></body>
        </html>';
        exit();
      }
    /*не смотрим конкретный профиль*/
      if (!isset($_GET["id"])){
        /*если не боец, то нельзя посмотреть людей в отряде*/
        if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
          ?>
          Наш чудесный комсостав - это люди, которые управляют жизнью отряда:
          <ul>
            <li><a href="https://vk.com/id87681348" target="_blank">Петрова Дарья</a> - Командир, вопросы по официальной части</li>
            <li><a href="https://vk.com/id20699608" target="_blank">Писарева Виктория</a> - Комиссар, вопросы по мероприятиям, событиям и прочим веселостям</li>
            <li><a href="https://vk.com/piypiupiy" target="_blank">Пиу Ксения</a> - Методист, вопросы по профессиональной части (обучению)</li>
            <li><a href="https://vk.com/anelcin" target="_blank">Стецова Екатерина</a> - Комендант, вопросы по хозяйственной части</li>
          </ul>
          <?php
        } else {
        /*иначе - смотри людей)*/
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/users/all_candidats.php');
        }
      }
    ?>
  </div><!-- page-container -->
<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  <script type="text/javascript">
      document.title = 'Наши кандидаты | СПО "СОзвездие"';
  </script>
  <!-- скрипт для сохранения файла из js -->
  <script src="/standart/js/FileSaver.js"></script>
  <script type="text/javascript" src="/own/js/users/all_candidats.js"></script>
  <script type="text/javascript" src="/own/js/users/own_profile.js"></script>
  <script type="text/javascript" src="/own/js/users/one_candidate.js"></script>
  <?php
  if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
  ?>
  <script type="text/javascript">
    var intofID = setInterval(function(){
      if ((typeof(get_candidate_info) != "undefined") && (get_candidate_info != undefined)) {
        clearInterval(intofID);
        get_candidate_info(<?=$_GET["id"]?>);
      }
    }, 50);
  </script>
  <?php
  }
  ?>
  <?php
  if (isset($_GET["id"]) && ($_GET["id"] == 0)) {
  ?>
  <script type="text/javascript">
    var intofID = setInterval(function(){
      if ((typeof(get_own_info) != "undefined") && (get_own_info != undefined)) {
        clearInterval(intofID);
        get_own_info(<?=$_SESSION["candidate_id"]?>);
      }
    }, 50);
  </script>
  <?php
  }
  ?>

</div>
</body>
</html>
