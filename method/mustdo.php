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
    <p>Не так-то просто поехать в лагерь! Ведь вы будете работать с детьми!<br>
      Вот лишь некоторые документы, которые вам понадобится собрать:
    </p>
    <ul>
      <li><b>Паспорт</b> - нужна копия первых двух страниц</li>
      <li><b>ИНН</b> - «индивидуальный номер налогоплательщика». Узнайте адрес филиала и приходите. <a href="http://www.nalog.ru/rn78/apply_fts/" target="_blank">ФНС</a>. Нужна копия</li>
      <!-- TODO: как конкретно получать? -->
      <li><b>СНИЛС</b> - "Страховой номер индивидуального лицевого счёта" - пенсионное. Сделайте в <a href="http://account.spb.ru/pfrs/pages/0" target="_blank">Пенсионном фонде</a>. Нужна копия</li>
      <li><b>Справка об отсутствии судимости</b> - (ВСЕМ!) </li>
      <details> Адрес:  <br>
Ст. М. Чернышевская, Ул. Чайковского, д. 14  <br>
На двери табличка "Приемная ФСБ" . <br>
Часы Работы: <br>
Пн, Чтв: 15:00 - 17:00 <br>
Вт, Ср: 10:00-13:00 и 15:00 - 17:00 <br>
Выдача справок:  <br>
Пн-Чтв: 10:00-13:00 и 15:00 - 17:00 <br>
По птн приема нет <br>
Ждать готовой справки 1 мес.!!!, узнавать по тел: 573-34-26 в те же часы работы. <br>
<br>
С собой иметь паспорт, копию паспорта, заявление заполняется на месте.
      </details>
      <li><b>Справка с места учёбы</b> - берётся в деканате</li>
      <li><b>Трудовая книжка</b> - если нет, можно купить в канцелярском</li>
      <li><b>Медицинская книжка</b> - со всеми анализами.
        <ol>
          <li>Направления берутся у командира (после экзамена)</li>
          <li>как купить медкнижку <br>
            Берёшь свою фотографию 3х4, паспорт,  150 рублей и направление на сан.минимум (без направления 500 рублей), приходишь в районную СЭС и говоришь, что тебе нужно сделать санкнижку. Тебя отправляют в нужный кабинет, где ты либо пытаешься сделать её по направлению на санминимум, либо говоришь "продайте мне ее вместе с сан-минимумом за 400  с чем-то там рублей". Дальше заполняешь заявление, которое оплачивается в Сбербанке. После принесения квитанции об оплате тебе сообщат, через сколько дней надо приходить за уже готовой санкнижкой. 
          </li>
          <li>Исследования на гельминты и простейшие, возбудители кишечных инфекций (диз.группа), брюшной тиф и энтеробиоз<br>
           делаются тоже в СЭС. В тот день, когда пойдешь забирать книжку, сразу возьми с собой заветную баночку с какашечкой и до похода в СЭС возьми в своей поликлинике кровь из вены (для брюшного тифа). Дай им направления на эти исследования. Все рекомендации по тому, как именно делаются те или иные анализы дадут уже в СЭС. 
          </li>
          <li>Справка из тубдиспанцера<br>
            Берёшь справку ФЛГ (действительна 1 год), анализы крови и мочи (не позднее месяца назад сделанные в поликлинике), идёшь с ними в тубдиспансер твоего района. Там нужно попасть ко врачу-фтизиатору, чтобы он посмотрел твою ФЛГ-справку и на основании ее действительности выдал тебе еще одну справку, которая и называется справка из туб-диспансера. Для того, чтобы запись оказалась в санкнижке, нужна еще Реакция Манту (действительна 1 год), но нам с вами достаточно и отдельной справки, в которой написано, что на учете в туб.диспансере мы не стоим. 
          </li>
          <li>Справка из КВД<br>
            Приходишь в КВД своего района ко врачу-дерматовенерологу с направлением. Если он не дотошый, то он может дать справку сразу или сказать "приходите завтра, справка будет" (отправив в другой кабинет сдавать кровь из пальца). Но если он дотошный, то он отправит тебя делать два анализа, на основании которых, вообще-то и дается справка от дерматовенеролога. Эти анализы - на сифилис и гонорею. в таком случае, делаешь анализы, потом уже он дает нужную справку. В последнем случае справка делается 2-3 дня.
          </li>
          <li>ФЛГ , кал на я/г, отоларинголог (он же ЛОР и у него же мазок из зева и носа), стоматолог<br>
             в своей поликлинике
          </li>
          <li>
            Справки от психиатра и нарколога <br> - в псих. и наркологическом диспансерах своего района.
          </li>
          <li>
            Прививки <br>
            в прививочном кабинете своей поликлинике
          </li>
          <li>Терапевт<br>
            (берётся после прохождения всего вышеизложенного). в своей поликлинике. Нужно просто узнать часы работы и умудриться в них прийти на приём. На вопрос терапевта о том, что вообще тебе нужно, говоришь, что нужна справка о том, что ты здоров, как бык, и можешь работать вожатым. 
          </li>
          <li>Обучение и аттестация в СЭС (сан.минимум).<br>
           Для его прохождения нужны сделанные прививки (краснуха,клещ.энцефалит, корь и дифтерия) и все анализы. Иначе к аттестации не допускают (!). Сан.минимум, в идеале - это лекция+аттестационный тест. Сие мероприятие бывает по определенным дням (раз в неделю). Так что сразу же, как появишься в СЭС, узнай, когда проходит аттестация и приходи в этот день с уже сданными анализами и сделанными прививками.</li>
          <li> Справка от инфекциониста - <br>
берётся точно так же, в районной поликлинике. Только максимально близко по времени к началу смены (по правилам - за сутки, но это не всегда возможно в зависимости от дня недели отъезда). Тоже нужно заранее узнать дни и часы приема, чтобы не обломиться потом.  
          </li><br>
          <li>
            Сроки действия:
            <details>
Врачи-специалисты (в своей поликлинике) - действительны 1 год; <br>
Справка из КВД действительна 6 месяцев; <br>
Справка от врача-фтизиатра из тубдиспансера своего района действительна 1 год; <br>
Исследования: <br>
---------- ФЛГ (1 раз в год в своей поликлинике); <br>
---------- анализ кала на гельминты - срок действия справки 1 год; <br>
---------- кишечные простейшие - срок действия справки 1 год; <br>
---------- мазок из зева и носа - срок действия справки 10 дней; <br>
---------- на возбудителей кишечных инфекций (дизгруппа) (2 недели) ;<br> 
---------- на энтеробиоз (3 месяца) ; <br>
прививки: <br>
---------- против дифтерии (1 раз в 10 лет ревакцинация); <br>
---------- против кори (для взрослых от 18 до 35 лет, не имеющих сведений о прививках, не привитых и не болевших корью ранее); <br>
---------- против краснухи (женщины с 18 до 25 лет, не болевших, не беременных);<br>
<br>
Справка об отсутствии контакта с инфекционными больными (у инфекциониста в своей поликлинике в последний рабочий день до отъезда). <br>
            </details>
          </li>
          <br>
          <li>Документ, позволяющий проходить мед.обследования по направлениям бесплатно<br>
             Приказ Минздравсоцразвития России №302н от 12 апреля 2011 г.
          </li>
        </ol>

      </li>
    </ul>
    <a href="https://vk.com/page-19748633_38533788" target="_blank">&copy;</a>
  </div> <!-- /container -->

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
  </div>
</body>
</html>
