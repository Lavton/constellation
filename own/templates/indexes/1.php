<?php
/*для незарегистрированных содержание главной страницы*/

if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == UNREG) || (!(isset($_SESSION["vk_id"])))) {
?>
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
<?php
}
?>