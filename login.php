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

<form action="response.php" class="js-ajax-php-json" method="post" accept-charset="utf-8">
  <input type="text" name="favorite_beverage" value="" placeholder="Favorite restaurant" />
  <input type="text" name="favorite_restaurant" value="" placeholder="Favorite beverage" />
  <select name="gender">
    <option value="male">Male</option>
    <option value="female">Female</option>
  </select>
  <input type="submit" name="submit" value="Submit form"  />
</form>

<div class="the-return">
  [HTML is replaced when successful.]
</div>
  <div class="container">
    <form class="form-signin" role="form">
      <h2 class="form-signin-heading">Please sign in</h2>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="inputUser" class="form-control" placeholder="Username" required="" autofocus="">
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>
  </div>

  <script type="text/javascript" src="standart/js/jquery.js"></script>
  <script type="text/javascript" src="standart/js/underscore.js"></script>
  <script type="text/javascript" src="standart/js/backbone.js"></script>
  <script type="text/javascript" src="standart/js/jstree.js"></script>
  <script type="text/javascript" src="standart/js/bootstrap.js"></script>

<script type="text/javascript">
// $("document").ready(function(){
  $(".js-ajax-php-json").submit(function(){
    var data = {
      "action": "test"
    };
    data = $(this).serialize() + "&" + $.param(data);
    console.log($(this).serialize())
    $.ajax({
      type: "POST",
      dataType: "json",
      url: "response.php",
      data: data,
      success: function(data) {
        $(".the-return").html(
          "Favorite beverage: " + data["favorite_beverage"] + "<br />Favorite restaurant: " + data["favorite_restaurant"] + "<br />Gender: " + data["gender"] + "<br />JSON: " + data["json"]
        );

        alert("Form submitted successfully.\nReturned json: " + data["json"]);
      }
    });
    return false;
  });
// });
</script>


<script>
$(".btn").click(function() {
  console.log("Hello");
  data =  {username: $("#inputUser").val(),};
  $.ajax({
    type: "POST",
    // dataType: "json",
    url: "/r_login.php",
    dataType: "json",
    data:  $.param(data), success: function(data) {
        $(".the-return").html(
          "JSON: " + data["json"]
        );

        alert("Form submitted successfully.\nReturned json: " + data["json"]);
      }
  });
});
</script>

</body>
</html>
