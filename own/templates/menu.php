<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
?>
<!-- для меню -->
<nav class="navbar navbar-default navbar-static-top navbar-inverse"> 
  <!-- основное меню -->
  <div class="header lvl1">
    <div class="container">
      <div class="row">
        <div class="col-xs-9">
          <div class="logo-cell">
            <a href="/">
              <div class="logo-container">
                СПО "СОзвездие"
              </div>
            </a>
          </div>
          <ul>
            <a href="/method">
              <li class=""> 
                База знаний <i class="fa fa-caret-down"></i>
              </li>
            </a>
            <a href="/events" class="events index">
              <li class="">
                Мероприятия <i class="fa fa-caret-down"></i>
              </li>
            </a>
            <a href="/about" class="about index">
              <li class="about-us">
                О нас <i class="fa fa-caret-down"></i>
              </li>
            </a>
          </ul>
        </div>
        <div class="col-xs-3 login">
          <i class="">&nbsp<span class="menu_login"><?php
            require_once $_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php';
            check_session();
            session_start();
            if (isset($_SESSION["user"])) {
              echo '<a href="/about/users/0">&nbsp;'.$_SESSION["user"].'&nbsp;</a>&nbsp;<span class="logout-url">(<a href="/logout">выйти</a>)</span>';
            } elseif (isset($_SESSION["vk_id"])) {
               echo '<a href="/about/users/0"><img src="'.$_SESSION["photo"].'"/></a>&nbsp;<span class="logout-url">(<a href="/logout">выйти</a>)</span>';
            } else {
              echo '<a href="/login">Войти</a>';
            }
            ?>
            </span>
          </i>
        </div>
      </div>
    </div>
  </div>

  <!-- подменю. Не всегда активно. -->
  <div class="header-lvl2-container">
      <div class="header lvl2 about">
        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <div class="logo-cell"></div>
              <ul>
                <a href="/about/glossary">
                  <li class="">
                    Об отрядах
                  </li>
                </a>
                <a href="/about/history">
                  <li class="">
                    Наша история
                  </li>
                </a>
                <?php
                /*отображаем страницу со всеми людьми лишь бойцам+*/
                session_start();
                if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) {
                  ?>
                <a href="/about/users" class="about faces">
                  <li class="">
                    Отряд в лицах
                  </li>
                </a>
                <?php
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
      </div>

<div class="header-lvl2-container">
      <div class="header lvl2 events">
        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <div class="logo-cell"></div>
              <ul>
                <a href="#">
                  <li class="">
                    Актуальные
                  </li>
                </a>
                <a href="#">
                  <li class="">
                    Грядущие
                  </li>
                </a>
                <a href="#">
                  <li class="">
                    Прошедшие
                  </li>
                </a>
                <a href="/events/shift" class="events shift">
                  <li class="">
                    Смены
                  </li>
                </a>
              </ul>
            </div>
          </div>
        </div>
      </div>

  </div>
</nav>