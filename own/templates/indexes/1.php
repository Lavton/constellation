<?php
/*для незарегистрированных содержание главной страницы*/

if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"]*1 == UNREG) || (!(isset($_SESSION["vk_id"])))) {
?>
<div class="starter-template">
    <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
      <div id="myTabContent" class="tab-content">
        <div role="tabpanel" class="tab-pane fade active in" id="adm" aria-labelledby="adm-tab">
          <!-- работадателю начало -->
          <div id="carousel-adm" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
              <li data-target="#carousel-adm" data-slide-to="0" class="active"></li>
              <li data-target="#carousel-adm" data-slide-to="1"></li>
              <li data-target="#carousel-adm" data-slide-to="2"></li>
            </ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
              <div class="item active">
                <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide">
                <div class="carousel-caption">
                  <div class="container">
                    <div class="carousel-caption">
                      <h1>С</h1>
                      <h2>Студенческий </h2>
                      <p>Мы - сообщество студентов, собравшихся вместе для вожатства, но кроме этого мы занимаемся множеством других вещей - поем, танцуем, инсценируем, развиваемся.</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="item">
                <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide">
                <div class="carousel-caption">
                  <div class="container">
                    <div class="carousel-caption">
                      <h1>П</h1>
                      <h2>Педагогический</h2>
                      <p>Мы - проходим курс Школ Вожатского Мастерства, по прохождении которого получаем сертификат, но на этом наше обучение не останавливается, ведь совершенству нет предела.</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="item">
                <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide">
                <div class="carousel-caption">
                  <div class="container">
                    <div class="carousel-caption">
                      <h1>О</h1>
                      <h2>Отряд</h2>
                      <p>Мы - отряд, а это значит, что мы всегда вместе. У нас есть традиции и устои, истории и легенды. А познакомить с ними мы хотим именно ТЕБЯ!</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Controls -->
            <a class="left carousel-control" href="#carousel-adm" role="button" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#carousel-adm" role="button" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
          <!-- работадателю конец -->
        </div>
      </div>
    </div>
<!--   <div class="row own-row">
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
 --></div>
<?php
}
?>
