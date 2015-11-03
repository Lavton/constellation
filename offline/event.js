(function() {
  window.goodTelephoneView = function(tel) {
    if (tel) {
      tel += ""
    }
    return tel ? "+7 (" + tel[0] + tel[1] + tel[2] + ") " + tel[3] + tel[4] + tel[5] + "-" + tel[6] + tel[7] + "-" + tel[8] + tel[9] : ""
  }
  window.formatDate = function(date) {
    if (!date) {
      return "";
    }
    date = new Date(date);
    Number.prototype.toMonthName = function() {
      var month = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
      ];
      return month[this];
    };
    return date.getDate() + " " + date.getMonth().toMonthName() + " " + date.getFullYear();
  }


  window.events = JSON.parse(window.localStorage.getItem("events"))
  window.people = JSON.parse(window.localStorage.getItem("people"))
  _.each(window.events, function(one_event) {
    _.each(one_event.appliers, function(person) {
      _.extend(person, _.findWhere(window.people, {id: person.user*1}))
    })
    _.each(one_event.editors, function(person) {
      _.extend(person, _.findWhere(window.people, {id: person.editor*1}))
    })

  })
  var result = _.template($("#events_temp").html())
  $("#container_event").append(result({
    "events": window.events
  }));


})();
