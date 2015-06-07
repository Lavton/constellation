(function() {
  /*логика ангулара*/

  function init_angular_o_cand_c($scope, $http) {
    $scope.window = window;
    var fid = window.location.href.split("/")
    var userid = fid[fid.length - 1] * 1;

    $("#page-container").on("_final_select", "input", function(e) {
      $scope.candidate.domain = $(this).val()
      $scope.$apply()
    })

    /*инициализация*/
    $scope.candidate = {};
    $(".user-info").removeClass("hidden")
    window.setPeople(function(flag) {
      $scope.candidate = _.clone(_.find(window.people, function(person) {
        return person.id == userid && person.isFighter == false;
      }))
      if (flag) {
        $scope.$apply();
      }
      initialize();
    });

    function initialize() {
      $scope.app2 = _.after(2, $scope.$apply)
        /*c cервера*/
      var data = {
        action: "get_one_candidate_info",
        id: userid
      }
      $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        _.extend($scope.candidate, json.user);

        $("a.profile_priv").attr("href", json.prev.mid)
        $("a.profile_next").attr("href", json.next.mid)
        if (!json.prev.mid) {
          $("a.profile_priv").hide();
        }

        if (!json.next.mid) {
          $("a.profile_next").hide();
        }
        $scope.app2();
      });
      /*с ВК*/
      var data_vk = {
        user_ids: $scope.candidate.domain,
        fields: ["photo_200", "domain"]
      }
      getVkData($scope.candidate.domain, ["photo_200", "domain"],
        function(response) {
          _.extend($scope.candidate, response[$scope.candidate.domain]);
          $scope.candidate.photo = $scope.candidate.photo_200;
          $scope.app2();
        }
      );
    }

    /*конец инициализации*/

    $scope.goodView = window.goodTelephoneView;

    /* меняет местами просмотр и редактирование*/
    $scope.editPerson = function() {
      $(".user-info").toggleClass("hidden");
      $(".user-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.candidate);
    };

    /*отправляет на сервер изменения*/
    $scope.submit = function() {
      $scope.candidate.domain = $("input.vk_now").val()
      getVkData($scope.candidate.domain, ["photo_200", "domain"],
        function(response) {
          var data = angular.copy($scope.candidate);
          data.vk_id = "" + response[$scope.candidate.domain].uid;
          data.uid = data.vk_id;
          data.action = "set_new_cand_data"
          _.each(data, function(element, index, list) {
            if (!element) {
              data[index] = null;
            }
          })
          _.extend($scope.candidate, response[$scope.candidate.domain])
          $scope.candidate.photo = $scope.candidate.photo_200;
          console.log("data submite")
          console.log(data);
          $http.post('/handlers/user.php', data).success(function(response) {
            window.clearPeople()
            window.setPeople()
            var saved = $(".saved");
            $(saved).stop(true, true);
            $(saved).fadeIn("slow");
            $(saved).fadeOut("slow");
          });
        }
      );
    }

    /*отменяет редактирование*/
    $scope.resetInfo = function() {
      $scope.candidate = angular.copy($scope.master);
    }

    /*удаляет кандидата*/
    $scope.killCandidate = function() {
      if (confirm("Точно удалить профиль?")) {
        var data = {
          action: "kill_candidate",
          id: userid
        }
        $.ajax({ //TODO: make with angular
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(response) {
          if (response.result == "Success") {
            window.clearPeople()
            window.location = "/";
          }
        });
      }
    }
  }

  function init() {
    window.setPeople(function() {
      $("input.vk_input").vkinput()
    });
    window.init_ang("oneCandidateApp", init_angular_o_cand_c, "one-candidate");
  }
  init();
  window.registerInit(init)
})();
