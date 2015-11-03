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
      _.extend(person, _.findWhere(window.people, {
        id: person.user * 1
      }))
    })
    _.each(one_event.editors, function(person) {
      _.extend(person, _.findWhere(window.people, {
        id: person.editor * 1
      }))
    })

  })
  var state = {
      "me": true,
      "plan": true
    }
    // отображение. Если is_me, то показывает лишь те мероприятия, на которые записан человек
    // если не is_plan, ты скрывает мероприятия, которые только лишь в планах
  function render(is_me, is_plan) {
    var filter1 = is_me ? _.filter(window.events, function(one_event) {
      return one_event.me_in
    }) : window.events
    var filter2 = !is_plan ? _.reject(filter1, function(one_event) {
      return one_event.planning * 1
    }) : filter1
    var result = _.template(document.getElementById("events_temp").innerHTML)
    document.getElementById("container_event").innerHTML =
      result({
        "events": filter2
      });
  }
  render(state.me, state.plan)
  window.cMe = function(is_me) {
    state.me = is_me
    render(state.me, state.plan)
  }
  window.cPl = function(is_plan) {
    state.plan = is_plan
    render(state.me, state.plan)
  }
})();
