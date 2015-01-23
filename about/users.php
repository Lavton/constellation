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
      <button type="button" class="btn btn-info get-all">а можно всех посмотреть?</button>
    </span> 
     <button type="button" class="hidden btn btn-info vCard-start">выбрать людей для импорта в <abbr title="формат записной книжки для Android, iPhone и т.д.">vCard</abbr></button>
     <button type="button" class="btn btn-default btn-sm hidden vCard-get-all">Выбрать всех</button>
     <button type="button" class="btn btn-default btn-sm hidden vCard-get-none">Снять выбор</button>
     <button type="button" class="btn btn-success hidden vCard-get" disabled="disabled">Получить vCard</button>
    <?php
      }
    ?>

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


  console.log(this.value);
});

</script>

<script>
/*отправляем Ajax чтобы посмотреть всех.*/
$(".get-all").click(function() {
  data =  {action: "all",};
  console.log(data);
  $.ajax({
    type: "POST",
    url: "/handlers/user.php",
    dataType: "json",
    data:  $.param(data)
  }).done(function(json) {
    if (json.result == "Success") {
      $(".vCard-start").removeClass("hidden");
      $(".vCard-start").hide();
      $(".vCard-start").show("slow");

      if ($("table.table")[0]==undefined){
        $("#page-container").append('<table class="table table-bordered">\
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
        //debugger;
      });
    } else {
      console.log("fail1");
      console.log(json);
    }
  }).fail(function() {
    console.log("fail2");
  });
});
</script>

<script type="text/javascript">
$(".vCard-start").click(function() {
  if ($('input[type=checkbox][name=vCard_check]')[0] == undefined) {
    $(".vCard-get-all").removeClass("hidden");
    $(".vCard-get-all").hide();
    $(".vCard-get-all").show("slow");

    $(".vCard-get-none").removeClass("hidden");
    $(".vCard-get-none").hide();
    $(".vCard-get-none").show("slow");

    $(".vCard-get").removeClass("hidden");
    $(".vCard-get").hide();
    $(".vCard-get").show("slow");

    _.each($("#page-container table.table tbody tr td:first-child"), function(element, index, list) {
      var num = $(element).parent().attr("class");
      $(element).html("<input type='checkbox' name='vCard_check' value='"+num+ "'>" +$(element).html());
    });
  }
});

$(".vCard-get-all").click(function() {
  $('input[type=checkbox][name=vCard_check]').each(function(){
    this.setAttribute("checked", "checked")
    $(this).parent().html($(this).parent().html());
    $(".vCard-get").prop('disabled', false);
  });
});
$(".vCard-get-none").click(function() {
  $('input[type=checkbox][name=vCard_check]').each(function(){
    this.removeAttribute("checked")
    $(this).parent().html($(this).parent().html());
    $(".vCard-get").prop('disabled', true);
  });
});
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
  // debugger;
});


</script>
</div>
</body>
</html>
