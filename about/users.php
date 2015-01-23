<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
  ?>

</head>
<body>
  <?php
    include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/menu.php');
  ?>
      
  <div id="page-container">
    <?php
      session_start();
      /*смотрим на свой профиль*/
      if (isset($_GET["id"]) && $_GET["id"] == 0) {
        echo "<h2>Выбрать категорию доступа</h2>";
        echo "<i>данное поле влияет на то, как вы видите страницы.</i> ";
        echo '<span class="saved">  (Изменения сохранены)</span><br/>';
        foreach ($_SESSION["groups_av"] as $key => $value) {
          if ($_SESSION["current_group"] == $key) {
            echo '<input type="radio" checked name="group_r" value="'.$key.'"> '.$value.'<br/>';
          } else {
            echo '<input type="radio" name="group_r" value="'.$key.'"> '.$value.'<br/>';
          }
        }

      /*смотрим на чужой профиль (доступно >=бойцам)*/
      } elseif (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
        echo '<h2>Просмотреть профиль</h2>';
        echo "(не сейчас, а когда он будет)";
      /*не боец попытался посмотреть профиль*/
      } elseif (isset($_GET["id"])) {
        echo "Access denied";
        include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
        echo'</div></body>
        </html>';
        exit();
      }
    ?>

    <?php
    /*не смотрим конкретный профиль*/
      if (!isset($_GET["id"])){
        /*если не боец, то нельзя посмотреть людей в отряде*/
        if (!(isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER))) {
          echo "Access denied";
          include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
          echo'</div></body>
          </html>';
          exit();
        }
        /*иначе - смотри людей)*/
    ?>
    расширенный вариант нумеровочки со страничками каждого бойца.<br/>
    и смены, которые мы отработали, может что ещё.. <br/>
    <span title='запрос может занять некоторое время. Ищите конкретного человека? Воспользуйтесь поиском!'>
      <button type="button" class="btn btn-info get-all unclick">а можно всех посмотреть?</button>
    </span> 
     <button type="button" class="own-hidden btn btn-info vCard-start unclick">выбрать людей для импорта в <abbr title="формат записной книжки для Android, iPhone и т.д.">vCard</abbr></button>
     <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-all">Выбрать всех</button>
     <button type="button" class="btn btn-default btn-sm own-hidden vCard-get-none">Снять выбор</button>
     <input type="text" class="vCard-category own-hidden" placeholder="назначить группу для контактов" size=30 />
     <button type="button" class="btn btn-success own-hidden vCard-get" disabled="disabled">Получить vCard</button>
    <?php
      }
    ?>
    <div class="table-container"></div>
  </div> 

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>
<div id="after-js-container">
<script type="text/javascript">
//send ajax on changing radio
$('input[type=radio][name=group_r]').change(function() {
    data =  {new_group: this.value, action: "change_group"};
    $.ajax({
      type: "POST",
      url: "/handlers/user.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(json) {
      if (json.result == "Success") {
        console.log(json);
        /*всплывающая надпись, что всё ОК*/
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      } else {
        alert("No.");
      }
    }).fail(function() {
      alert("Fail.");
    });
});

</script>

<!-- скрипт для сохранения файла из js -->
<script src="/standart/js/FileSaver.js"></script>

<script>
/*отправляем Ajax чтобы посмотреть всех бойцов.*/
$(".get-all").click(function() {
  /*двойное назначение кнопки:
  Если не нажимали - добавляет таблицу со всеми бойцами и проч.*/
  if ($(".get-all").hasClass("unclick")) {
  data =  {action: "all",};
  $.ajax({
    type: "POST",
    url: "/handlers/user.php",
    dataType: "json",
    data:  $.param(data)
  }).done(function(json) {
    if (json.result == "Success") {
      $(".get-all").toggleClass("unclick");
      $(".get-all").text("ААА! УБЕРИТЕ ИХ!!!")
      $(".vCard-start").addClass("unclick");
      $(".vCard-start").show("slow");
      if ($("table.table")[0]==undefined){
        $(".table-container").append('<table class="table table-bordered">\
          <thead><tr>\
          <th>#</th>\
          <th>имя</th>\
          <th>год вступления</th>\
          </tr></thead>\
          <tbody>\
        </tbody></table>')
      }
      var table_body = $("#page-container table.table tbody")[0];
      _.each(json.users, function(element, index, list) {
        $(table_body).append("<tr class='"+element.id+"'><td>"
          +element.id+
          "</td><td>"+
          "<strong>"+
          " " + element.name + " " + element.surname + 
          "</strong>"+
          "<span class='text-right'><small><a href='users/"+element.id+"'>Профиль</a>/" + 
          "<a target='_blank' href='//vk.com/"+element.vk_id +"'>VK</a></small></span></td><td>" 
          + element.year_of_entrance + 
          "</td></tr>");
      });
    } else {
      console.log("fail1");
    }
  }).fail(function() {
    console.log("fail2");
  });
  /*Если нажимали - скрывает таблицу*/
  } else {
    $(".get-all").toggleClass("unclick");
    $(".get-all").text("а можно всех посмотреть?");
    $(".table-container").html("");
    if (!$(".vCard-start").hasClass("unclick")) {
      $(".vCard-start").trigger("click");
    }
    $(".vCard-start").hide("slow");
  }
});
</script>

<script type="text/javascript">

$(".vCard-start").click(function() {
  /*Если не нажимали - делает видимыми кнопки для экспорта vCard*/
  if ($(".vCard-start").hasClass("unclick")) {
    $(".vCard-start").text("cкрыть импорт в vCard");
    $(".vCard-get-all").show("slow");
    $(".vCard-get-none").show("slow");
    $(".vCard-get").show("slow");
    $(".vCard-category").show("slow");
    _.each($("#page-container table.table tbody tr td:first-child"), function(element, index, list) {
      /*вставляем чекбокс везде перед номером*/
      var num = $(element).parent().attr("class");
      $(element).html("<input type='checkbox' name='vCard_check' value='"+num+ "'>" +$(element).html());
    });
    /*второе назначение - скрыть всё.*/
  } else {
    $(".vCard-start").html("выбрать людей для импорта в <abbr title='формат записной книжки для Android, iPhone и т.д.'>vCard</abbr>");
    $(".vCard-get-all").hide("slow");
    $(".vCard-get-none").hide("slow");
    $(".vCard-get").hide("slow");
    $(".vCard-category").hide("slow");
    _.each($("#page-container table.table tbody tr td:first-child"), function(element, index, list) {
        var num = $(element).parent().attr("class");
        $(element).html(num);
    });
  }
  $(".vCard-start").toggleClass("unclick");
});

/*сделать все выбранными*/
$(".vCard-get-all").click(function() {
  $('input[type=checkbox][name=vCard_check]').each(function(){
    this.setAttribute("checked", "checked")
    $(this).parent().html($(this).parent().html());
    $(".vCard-get").prop('disabled', false);
  });
});
/*убрать выбор везде*/
$(".vCard-get-none").click(function() {
  $('input[type=checkbox][name=vCard_check]').each(function(){
    this.removeAttribute("checked")
    $(this).parent().html($(this).parent().html()); //перерисовка
    $(".vCard-get").prop('disabled', true);
  });
});

/*немного магии при выборе конкретного, чтобы всё работало*/
$('#page-container').on('change', 'input[type=checkbox][name=vCard_check]', function(event) // вешаем обработчик на все ссылки, даже созданные после загрузки страницы
{
  if (this.hasAttribute("checked")) {
    this.removeAttribute("checked");
    if ($('input[type=checkbox][name=vCard_check][checked]')[0] == undefined) {
      $(".vCard-get").prop('disabled', true);
    }
  } else {
    this.setAttribute("checked", "checked");
    $(".vCard-get").prop('disabled', false);
  }
});

/*генерация vCard*/
$(".vCard-get").click(function() {
  var ids = [];
  _.each($('input[type=checkbox][name=vCard_check][checked]'), function(element, index, list) {
    ids.push($(element).val()*1);
  });
  data = {action: "get_full_info", ids: ids}
  $.ajax({
      type: "POST",
      url: "/handlers/user.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(json) {
      if (json.result == "Success") {
        var card = "";
        _.each(json.users, function(element, index, list) {
          var this_card = "";
          this_card += "BEGIN:VCARD\n";
          this_card += "VERSION:3.0\n";
   
          this_card += "FN:" + element.name + " " + element.surname + "\n";
          this_card += "N:" + element.surname;
          if (element.maiden_name != null) {
            this_card += ","+element.maiden_name;
          }
          this_card += ";"+element.name+";";
          if (element.second_name != null) {
            this_card += element.second_name;
          }
          this_card +=";;\n";
          
          if (element.birthdate != null) {
            this_card += "BDAY:"+element.birthdate+"\n";
          }

          if (element.phone != null) {
            var tel = element.phone;
            this_card += "TEL;TYPE=MAIN:"+
            "+7-"+tel[0]+tel[1]+tel[2]+"-"+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+tel[8]+tel[9]+
            "\n";
          }
          if (element.second_phone != null) {
            var tel = element.second_phone;
            this_card += "TEL;TYPE=CELL:"+
            "+7-"+tel[0]+tel[1]+tel[2]+"-"+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+tel[8]+tel[9]+
            "\n";
          }

          if (element.email != null) {
            this_card += "EMAIL;TYPE=INTERNET:"+element.email+"\n";
          }

          if ($(".vCard-category").val() != "") {
            this_card += "CATEGORIES:"+$(".vCard-category").val()+"\n";
          }
          this_card += "END:VCARD\n\n";
          card += this_card;
        });

        /*запись полученной строки в файл*/
        var blob = new Blob([card], {type: "text/plain;charset=utf-8"});
        saveAs(blob, "contacts.vcf");
      } else {
        alert("No.");
      }
    }).fail(function() {
      alert("Fail.");
    });
});

</script>
</div>
</body>
</html>
