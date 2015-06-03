var result_text_camod = null
$('body').on('click', "img.camod", function(e) {
  var mm_pic = $("img.camod")
  var x_abs = e.offsetX;
  var y_abs = e.offsetY;
  var w = mm_pic.width();
  var h = mm_pic.height();
  var x_o = x_abs / w;
  var y_o = y_abs / h;
  console.log("click", x_o, y_o);
  if (!result_text_camod) {
    result_text_camod = []
    $.ajax({
      type: "POST",
      url: "/handlers/methods.php",
      dataType: "json",
      data: $.param({
        "action": "get_camod"
      })
    }).done(function(json) {
      console.log(json)
      result_text_camod = json["cam"];
      if (!result_text_camod) {
        result_text_camod = [];
      }
      for (var i = 0; i < result_text_camod.length; i++) {
        if ((x_o >= result_text_camod[i].x_o) && (y_o >= result_text_camod[i].y_o) && (x_o <= result_text_camod[i].x_k) && (y_o <= result_text_camod[i].y_k)) {
          $(".res_div").show();
          $(".res_div > div").html(result_text_camod[i].comments);
          console.log(result_text_camod[i].comments)
        }
      }
    });
  } else {
    for (var i = 0; i < result_text_camod.length; i++) {
      if ((x_o >= result_text_camod[i].x_o) && (y_o >= result_text_camod[i].y_o) && (x_o <= result_text_camod[i].x_k) && (y_o <= result_text_camod[i].y_k)) {
        $(".show_div_camod").show();
        $(".show_div_camod > div").html(result_text_camod[i].comments);
        console.log(result_text_camod[i].comments)
      }
    }
  }
});

$('body').on('click', ".OK_camod_button", function(e) {
  $(".show_div_camod").hide();
  $(".show_div_camod > div").html("");
});