<?php
/*для зарегистрированных содержание главной страницы*/
if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"] == UNREG) || (!(isset($_SESSION["vk_id"]))))) {
?>
<iframe src="https://www.google.com/calendar/embed?src=kn2p9ovqid67tekk0ecbbnutsc%40group.calendar.google.com&ctz=Europe/Moscow" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
<div class="news">
  <h1> для зарегистрированных пользователей:<br/>Новости</h1>
  возможно это будет вкладками, возможно как-то ещё
  <div class="jumbotron">
    <!-- Example row of columns -->
    <div class="row">
      <div class="col-md-4">
        <h2>Отрядные мероприятия</h2>
        <p>Тут новости по мероприятиям. К которым сейчас вот готовимся. Линки ведут в обсуждения мероприятий</p>
        <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
      </div>
      <div class="col-md-4">
        <h2>Интересные события в СПб</h2>
        <p>Тут любой боец может написать про что-нибудь интересное, на что он хочет пригласить СО*</p>
        <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
     </div>
      <div class="col-md-4">
        <h2>Для обучения</h2>
        <p>А тут - различные полезные курсы и обучающие мероприятия. Да, самостоятельным полем. Потому что мы педагогический отряд, нам надо учиться!</p>
        <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
      </div>
    </div>
    <hr>
  </div>
</div>
<?php
}
?>