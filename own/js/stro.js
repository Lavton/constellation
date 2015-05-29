var result_text_stro = null
$('body').on('click', "img.stro", function(e) {
    var mm_pic = $("img.stro")
    var x_abs = e.offsetX;
    var y_abs = e.offsetY;
    var w = mm_pic.width();
    var h = mm_pic.height();
    var x_o = x_abs/w;
    var y_o = y_abs/h;
    console.log("click", x_o, y_o);
  if (!result_text_stro) {
    result_text_stro = []
      $.ajax({
      type: "POST",
      url: "/handlers/abouts.php",
      dataType: "json",
      data:  $.param({"action": "get_stro"})
    }).done(function(json) {
      console.log(json)
      result_text_stro = json["stro"];
      if (! result_text_stro) {
        result_text_stro = [];
      }
    for (var i = 0; i < result_text_stro.length; i++) {
      if ((x_o>=result_text_stro[i].x_o) && (y_o>=result_text_stro[i].y_o) && (x_o<=result_text_stro[i].x_k) && (y_o<=result_text_stro[i].y_k)) {
        $(".res_div").show();
        $(".res_div > div").html(result_text_stro[i].comments);
        console.log(result_text_stro[i].comments)
      }
    }
    });
  } else {
    for (var i = 0; i < result_text_stro.length; i++) {
      if ((x_o>=result_text_stro[i].x_o) && (y_o>=result_text_stro[i].y_o) && (x_o<=result_text_stro[i].x_k) && (y_o<=result_text_stro[i].y_k)) {
        $(".show_div_stro").show();
        $(".show_div_stro > div").html(result_text_stro[i].comments);
        console.log(result_text_stro[i].comments)
      }
    }
  }
});

$('body').on('click', ".OK_stro_button", function(e) {
  $(".show_div_stro").hide();
  $(".show_div_stro > div").html("");
});

