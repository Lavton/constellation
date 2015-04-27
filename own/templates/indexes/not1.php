<?php
/*для зарегистрированных содержание главной страницы*/
if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == UNREG) || (!(isset($_SESSION["vk_id"]))))) {
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
      <p class="lead">
        Поздравляем! вы попали на альфа-версию сайта! <br/>
        Ещё много вы не увидите тут. А если хотите поторопить момент - вливайтесь в разработку!
      </p>
<iframe src='/inwidget/index.php?width=800&inline=7&view=14&toolbar=false' scrolling='no' frameborder='no' style='border:none;width:800px;height:295px;overflow:hidden;'></iframe>
</div>

<?php
}
?>
