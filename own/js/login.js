(function() {
  window.clearPeople();

  /*при ajax загрузке не всегда опенАПИ к этому моменту подгружается.
      Ждём, пока это не произойдёт в цикле.*/
  var intID = setInterval(function() {
      if (typeof VK !== "undefined") {
        VK.init({
          apiId: 4602552
        });
        VK.Widgets.Auth("vk_auth", {
          width: "300px",
          onAuth: function(data) {
            var odata = _.pick(data, 'uid', 'hash', 'first_name', 'last_name', 'photo_rec');
            odata.action = "vk_auth";
            $.ajax({
              type: "POST",
              url: "/handlers/login.php",
              dataType: "json",
              data: $.param(odata)
            }).done(function(json) {
              console.log(json)
              window.clearPeople()
              window.location.href = "/";
            });
          }
        });
        clearInterval(intID);
      }
    },
    50);
})();
