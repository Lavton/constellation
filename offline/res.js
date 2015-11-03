(function() {
  window.goodTelephoneView = function(tel) {
    if (tel) {
      tel += ""
    }
    return tel ? "+7 (" + tel[0] + tel[1] + tel[2] + ") " + tel[3] + tel[4] + tel[5] + "-" + tel[6] + tel[7] + "-" + tel[8] + tel[9] : ""
  }
  window.people = JSON.parse(window.localStorage.getItem("people"))
  var result = _.template($("#people_temp").html())
  $("#container").append(result({
    "users": window.people
  }));
})();
