$('#page-container').on('click', ".pre-add-new-event", function() {
  var today = new Date();
  var start_date = today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate();
  var finish_date = today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate();
  var data = {
    action: "add_new_event", 
    start_date: start_date,
    finish_date: finish_date
  }
  $.ajax({ //TODO: make with angular
    type: "POST",
    url: "/handlers/event.php",
    dataType: "json",
    data:  $.param(data)
  }).done(function(response) {
    if (response.result == "Success") {
      window.location="/events/event/"+response.id;
    }
  });
});