//send ajax on changing radio
if (!(window.own_profile_script)) {
  window.own_profile_script = true;
$("#page-container").on('change', 'input[type=radio][name=group_r]', function() {
    data =  {new_group: this.value, action: "change_group"};
    $.ajax({
      type: "POST",
      url: "/handlers/user.php",
      dataType: "json",
      data:  $.param(data)
    }).done(function(json) {
      if (json.result == "Success") {
        console.log(json);
        /*всплывающая надпись, что всё ОК*/
        var saved = $(".saved");
        $(saved).stop(true, true);
        $(saved).fadeIn("slow");
        $(saved).fadeOut("slow");
      } else {
        alert("No.");
      }
    }).fail(function() {
      alert("Fail.");
    });
});
}
