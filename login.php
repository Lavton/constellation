<!DOCTYPE html>
<html>
<head lang="en">
  <title>CПО "СОзвездие" | логин</title>
  <?php
    include('own/templates/header.php');
  ?>


</head>
<body>
  <?php
    include('own/templates/menu.php');
  ?>

  <div id="page-container">
    <div id="auth_wrapper"><div id="vk_auth"></div></div>
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
          var odata = _.pick(data, 'uid', 'hash', 'first_name', 'last_name', 'photo_rec');
          odata.action = "vk_auth";
          $.ajax({
            type: "POST",
            url: "/handlers/login.php",
            dataType: "json",
            data:  $.param(odata)
          }).done(function(json) {
            window.location = "/";
          });
        }
       });
        clearInterval(intID);
      }
    }, 50);
  </script>
</div>
</body>
</html>
