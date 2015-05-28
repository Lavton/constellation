<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
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
    <div class="text-center">
      <h1>Напарники</h1>
      <h2>Идеи и мысли</h2>
      <ul>
        <li>Учитывайте особенности характера и темперамент</li>
        <li>Недопускайте переутомления своего напарника</li>
        <li>Между вами не должно быть секретов</li>
        <li>Все ссоры между вами - лишь между вами. Никогда не на детях</li>
        <li>Равноправие и единство требований. Даже если напраник поступил совсем не так, как вы ожидали - 100 раз подумайте, прежде чем исправлять</li>
        <li>Распределение обязанностей: подъём, зарядка, дисциплина и т.п. - лучше перед сменой</li>
        <li>Заранее договоритесь, где что лежит из общих вещей</li>
        <li>Если вы чувствуете, что не успеваете за напарником, просто попросите отдать вам какое-то мероприятие</li>
        <br>
        <li>Любите своего напарника!</li>
      </ul>
    </div>
      <div style="width: 665px;
  margin: 0 auto;"> <br><hr>
      <div id="vk_like"></div>
    <div id="vk_comments"></div>
  </div>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
      <script type="text/javascript">
VK.Widgets.Like("vk_like", {type: "fill"},787444652)
</script>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 10, width: "665", attach: "*", autoPublish: "0"}, 787444652);
</script>

</div>
</body>
</html>
