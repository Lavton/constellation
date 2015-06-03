function setClicking(initSel, postUrl, action) {
  var result_text = null
  $('body').on('click', initSel+" img.for_click", function(e) {
    var mm_pic = $(initSel+" img.for_click")
    var x_abs = e.offsetX;
    var y_abs = e.offsetY;
    var w = mm_pic.width();
    var h = mm_pic.height();
    var x_o = x_abs / w;
    var y_o = y_abs / h;
    console.log("click", x_o, y_o);
    if (!result_text) {
      result_text = []
      $.ajax({
        type: "POST",
        url: postUrl,
        dataType: "json",
        data: $.param({
          "action": action
        })
      }).done(function(json) {
        console.log(json)
        result_text = json["obj"];
        if (!result_text) {
          result_text = [];
        }
        for (var i = 0; i < result_text.length; i++) {
          if ((x_o >= result_text[i].x_o) && (y_o >= result_text[i].y_o) && (x_o <= result_text[i].x_k) && (y_o <= result_text[i].y_k)) {
            $(initSel+" .res_div").show();
            $(initSel+" .res_div > div").html(result_text[i].comments);
            console.log(result_text[i].comments)
          }
        }
      });
    } else {
      for (var i = 0; i < result_text.length; i++) {
        if ((x_o >= result_text[i].x_o) && (y_o >= result_text[i].y_o) && (x_o <= result_text[i].x_k) && (y_o <= result_text[i].y_k)) {
          $(initSel+" .show_div").show();
          $(initSel+" .show_div > div").html(result_text[i].comments);
          console.log(result_text[i].comments)
        }
      }
    }
  });

  $('body').on('click', initSel+" .OK_button", function(e) {
    $(initSel+" .show_div").hide();
    $(initSel+" .show_div > div").html("");
  });
}