<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="/own/images/icon.ico">
    <title>CПО "СОзвездие" | будущий сайт отряда</title>
    <link rel="stylesheet" href="standart/css/bootstrap.css"/>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="own/css/common_style.css">
    <link href="/own/css/signin.css" rel="stylesheet">
</head>

<body>
  <?php
    include('menu.html')
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

  <script type="text/javascript" src="standart/js/jquery.js"></script>
  <script type="text/javascript" src="standart/js/underscore.js"></script>
  <script type="text/javascript" src="standart/js/backbone.js"></script>
  <script type="text/javascript" src="standart/js/jstree.js"></script>
  <script type="text/javascript" src="standart/js/bootstrap.js"></script>

  <script>
  $(".btn").click(function() {
    console.log("Hello");
    data =  {username: $("#inputUser").val(),};
    $.ajax({
      type: "POST",
      url: "/r_login.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(json) {
      console.log(json);
      console.log('success');
    }).fail(function() {
      alert("No user with such data.");
    });;
  });
  </script>

</body>
</html>
