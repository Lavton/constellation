<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | будущий сайт отряда</title>
  <?php
    include('own/templates/header.php');
  ?>

</head>
<body>
  <?php
    include('own/templates/menu.php');
  ?>
      
  <div class="container">
<?php
  session_start();
  if ($_GET["id"] == $_SESSION["user_id"]) {
    echo "<h2>Выбрать категорию доступа</h2>";
    foreach ($_SESSION["groups_av"] as $key => $value) {
      if ($_SESSION["current_group"] == $key) {
        echo '<input type="radio" checked name="group_r" value="'.$key.'"> '.$value.'<br/>';
      } else {
        echo '<input type="radio" name="group_r" value="'.$key.'"> '.$value.'<br/>';
      }
    }
  } else {
    echo '<h2>Просмотреть профиль</h2>';
    echo "(не сейчас, а когда он будет)";
  }
?>
  </div> 

<?php
  include('own/templates/footer.php');
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
