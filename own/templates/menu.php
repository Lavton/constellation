    <!-- Part 1: Wrap all page content here -->
<div id="wrap">

<nav class="navbar navbar-default navbar-static-top navbar-inverse"> 
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
            <a href="/events">
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
          <i class="fa fa-user">&nbsp<span class="menu_login"><?php
            session_start();
            if (isset($_SESSION["user"])) {
              echo '<a href="/users/'.$_SESSION["user_id"].'">&nbsp;'.$_SESSION["user"].'&nbsp;</a>&nbsp;<span class="logout-url">(<a href="/logout">выйти</a>)</span>';
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


  <div class="header-lvl2-container">
      <div class="header lvl2 about">
        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <div class="logo-cell"></div>
              <ul>
                <a href="/about/glossary">
                  <li class="">
                    Глоссарий и история СО
                  </li>
                </a>
                <a href="/about/history">
                  <li class="">
                    История отряда
                  </li>
                </a>
                <?php
                session_start();
                if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] > 2)) {
                  ?>
                <a href="/about/faces" class="about faces">
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
                <a href="/">
                  <li class="">
                    Актуальные
                  </li>
                </a>
                <a href="/">
                  <li class="">
                    Грядущие
                  </li>
                </a>
                <a href="/">
                  <li class="">
                    Прошедшие
                  </li>
                </a>
              </ul>
            </div>
          </div>
        </div>
      </div>

  </div>
</nav>
<div class="page-cont">
