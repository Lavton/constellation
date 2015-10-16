<?php
/*для незарегистрированных содержание главной страницы*/

if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"]*1 == UNREG) || (!(isset($_SESSION["vk_id"])))) {
?>
<div class="starter-template">
  <div class="row own-row">
    <div class="col-xs-4">
      <div class="in-w"><iframe src='/standart/inwidget/index.php?view=16&inline=4' scrolling='no' frameborder='no' style='border:none;width:260px;height:400px;overflow:hidden;'></iframe>
      </div>
    </div>
    <div class="col-xs-4">
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
      <p class="lead">Хочешь к нам? приходи на ШВМы! 
        Любые вопросы можно задать в группе <a href="https://vk.com/sozvezdie_school" target="_blank">ВК</a>
      </p>
      <p class="lead"> Если вы - представитель лагеря и ищете молодых и активных вожатых - пишите <a href="https://vk.com/page-19748633_38662439" target="blank">сюда</a> </p>
    </div>
    <div class="col-xs-4">
      <div class="vk-g"><div id="vk_groups"></div></div>
    </div>
  </div>
</div>
<?php
}
?>
