<?php
/*для незарегистрированных содержание главной страницы*/

if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"]*1 == UNREG) || (!(isset($_SESSION["vk_id"])))) {
?>
  <div class="starter-template">
  <h3>Наши ШВМ - каждый вторник в 316 аудитории <a href="https://pp.vk.me/c629217/v629217287/3aab5/1RmsBFdycvw.jpg" target="_blank">ГК!</a><br> Приходи, ждем всех!</h3>
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
              <li data-target="#carousel-adm" data-slide-to="3"></li>
            </ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
              <div class="item active">
                <!-- <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide"> -->
                <img class="first-slide" src="/own/templates/indexes/images/S.jpg" alt="First slide">
  
                <div class="carousel-caption">
                  <div class="container">
                    <div class="carousel-caption">
                      <h1> <span>С</span></h1>
                      <h2> <span>Студенческий</span> </h2>
                      <p><span>Мы - сообщество студентов, собравшихся вместе для вожатства, но кроме этого мы занимаемся множеством других вещей - поем, танцуем, инсценируем, развиваемся.</span></p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="item">
                <!-- <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide"> -->
                <img class="first-slide" src="/own/templates/indexes/images/P.jpg" alt="First slide">
                <div class="carousel-caption">
                  <div class="container">
                    <div class="carousel-caption">
                      <h1><span>П</span></h1>
                      <h2><span>Педагогический</span></h2>
                      <p><span>Мы - проходим курс Школ Вожатского Мастерства, по прохождении которого получаем сертификат, но на этом наше обучение не останавливается, ведь совершенству нет предела.</span></p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="item">
                <!-- <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide"> -->
                <img class="first-slide" src="/own/templates/indexes/images/O.jpg" alt="First slide">
                <div class="carousel-caption">
                  <div class="container">
                    <div class="carousel-caption">
                      <h1><span>О</span></h1>
                      <h2><span>Отряд</span></h2>
                      <p><span>Мы - отряд, а это значит, что мы всегда вместе. У нас есть традиции и устои, истории и легенды. А познакомить с ними мы хотим именно ТЕБЯ!</span></p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="item">
                <!-- <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide"> -->
                <img class="first-slide" src="/own/templates/indexes/images/all.jpg" alt="First slide">
                <div class="carousel-caption">
                  <div class="container">
                    <div class="carousel-caption">
                      <h1><span>хотите узнать больше?</span></h1>
                      <p><span>Если Вы заинтересовались в нас, как в работниках - наш командир с радостью ответит на все вопросы</span><br>
                        <a class="btn btn-lg btn-primary" href="/about/command_staff#commander" role="button">связаться с командиром</a></p>
                      <hr>
                      <p><span>Если же ты живёшь и грёзишь работать вожатым, пиши нашему методисту! Он обязательно расскажет всё, что тебя интересует</span><br>
                        <a class="btn btn-lg btn-primary" href="/about/command_staff#methodist" role="button">связаться с методистом</a></p>
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
<iframe src='/standart/inwidget/index.php?width=800&inline=7&view=14&toolbar=false' scrolling='no' frameborder='no' style='border:none;width:800px;height:295px;overflow:hidden;'></iframe>
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
