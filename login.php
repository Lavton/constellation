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

  <div id="page-container">
    <!-- Put this script tag to the <head> of your page -->

<!-- Put this div tag to the place, where Auth block will be -->
<div id="auth_wrapper"><div id="vk_auth"></div></div>

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
<div id="after-js-container">
  <script type="text/javascript" src="//vk.com/js/api/openapi.js"></script>

  <script type="text/javascript">
  /*при ajax загрузке не всегда опенАПИ к этому моменту подгружается.
  Ждём, пока это не произойдёт в цикле.*/
    var intID = setInterval(function(){
      if (typeof VK !== "undefined") {
        VK.init({apiId: 4602552});
        VK.Widgets.Auth("vk_auth", {width: "300px", onAuth: function(data) {
          console.log(data);
          alert('user '+data['uid']+' authorized');
        }
       });
        // debugger;
        // $("#vk_auth").attr("style") = "";
        clearInterval(intID);
      }
    }, 50);
  </script>

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
</div>
</body>
</html>
