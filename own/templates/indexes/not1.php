<?php
/*для зарегистрированных содержание главной страницы*/
if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"]*1 == UNREG) || (!(isset($_SESSION["uid"]))))) {
?>
  <div class="starter-template">
    <h1>CПО "СОзвездие"</h1>
    <p class="lead">Мы -
      <ul>
        <u>
          <li><b>С</b>туденческий - большинство из нас студенты ВУЗов</li>
          <li><b>П</b>едагогический - мы ездим в лагеря вожатыми и воспитателями</li>
          <li><b>О</b>тряд - вместе не только в лагере, но и весь год!</li>
        </u>
      </ul>
    </p>
    <iframe src='/standart/inwidget/index.php?width=800&inline=7&view=14&toolbar=false' scrolling='no' frameborder='no' style='border:none;width:800px;height:295px;overflow:hidden;'></iframe>
  </div>
  <?php
}
?>
