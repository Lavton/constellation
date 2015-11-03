(function() {
  window.goodTelephoneView = function(tel) {
    if (tel) {
      tel += ""
    }
    return tel ? "+7 (" + tel[0] + tel[1] + tel[2] + ") " + tel[3] + tel[4] + tel[5] + "-" + tel[6] + tel[7] + "-" + tel[8] + tel[9] : ""
  }


  function setPeople(callback) {
    if ((!(_.isArray(window.people))) || (!window.people.length)) {
      var people = [];

      function supports_html5_storage() {
        try {
          return 'localStorage' in window && window['localStorage'] !== null;
        } catch (e) {
          return false;
        }
      }
      var hasLocal = supports_html5_storage();
      var expire_time = 1000 * 60 * 60 * 24 * 5; // в мс
      if ((!hasLocal) || (hasLocal && !window.localStorage.getItem("people"))) {
        if (hasLocal) {
          window.localStorage.setItem("people_ts", _.now());
        }
        var data = {
          "action": "get_common_inf"
        }
        $.ajax({
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          vk_ids = [];
          _.each(json.users, function(element, index, list) {
            vk_ids.push(element.uid);
          });
          getVkData(vk_ids, ["photo_50", "domain"],
            function(response) {
              _.each(json.users, function(element, index, list) {
                var user = _.pick(response[element.uid], 'uid', "domain", "photo_50");
                _.extend(user, element);
                user.id = user.id * 1;
                user.uid = user.uid * 1;
                user.isCandidate = Boolean(user.isCandidate * 1);
                user.isFighter = Boolean(user.isFighter * 1);

                // строковое представление понадобится для поиска
                user.IF = user.first_name + " " + user.last_name;
                user.FI = user.last_name + " " + user.first_name;
                user.photo = user.photo_50;
                people.push(user);
              });
              window.people = people;
              if (hasLocal) {
                window.localStorage.setItem("people", JSON.stringify(window.people))
              }
              if (callback) {
                callback(true);
              }
            });
        });
      } else {
        if (hasLocal) {
          console.log("cached")
          window.people = JSON.parse(window.localStorage.getItem("people"))
          if (callback) {
            callback(false);
          }
        }
      }
    } else {
      if (callback) {
        callback(false)
      }
    }
  }
  var result = _.template($("#people_temp").html())
  setPeople(function() {
    $("#container").append(result({
      "users": window.people
    }));
  })
})();
