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
    <form class="form-signin" role="form">
      <h2 class="form-signin-heading">Please sign in</h2>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="inputUser" class="form-control" placeholder="Username" required="" autofocus="">
<!--       <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
 -->      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>
  </div>

<?php
  include('own/templates/footer.php');
?>
  <script>
  /*отправляем Ajax при заполнении формы.*/
  $(".btn").click(function() {
    console.log("Hello");
    data =  {username: $("#inputUser").val(),};
    $.ajax({
      type: "POST",
      url: "/handlers/login.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(json) {
      if (json.result == "Success") {
        console.log(json);
        $(".menu_login").html('<a href="/users/'+json.user_id+'">&nbsp;'+json.name+'&nbsp;</a>&nbsp;<span class="logout-url">(<a href="/logout">выйти</a>)</span>');
        window.location = "/";
      } else {
        alert("No user with such data.");
      }
    }).fail(function() {
      alert("No user with such data.");
    });
  });
  </script>

</body>
</html>
