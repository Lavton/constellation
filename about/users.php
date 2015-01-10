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
      
  <div class="container">
    <?php
      session_start();
      /*смотрим на свой профиль*/
      if (isset($_GET["id"]) && $_GET["id"] == $_SESSION["user_id"]) {
        echo "<h2>Выбрать категорию доступа</h2>";
        echo "<i>данное поле влияет на то, как вы видите страницы.</i> <br/> ";
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
   
    <?php
      }
    ?>

  </div> 

<?php
  include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/footer.php');
?>

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
      } else {
        alert("No.");
      }
    }).fail(function() {
      alert("Fail.");
    });


  console.log(this.value);
});

</script>
</body>
</html>
