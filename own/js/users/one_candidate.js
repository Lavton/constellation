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
    $("input.vk_input").on("_final_select", function() {
      console.log("_final_select")
    })
    $("input.vk_input").on("_after", function() {
      console.log("_after")
    })

    $(".user-info").removeClass("hidden")
    window.setPeople(function(flag) {
      console.log("setting")
      console.log(window.people)
      console.log(flag)
      $scope.candidate = _.clone(_.find(window.people, function(person) {
        return person.id == userid && person.isFighter == false;
      }))
      $scope.candidate.domain = "https://vk.com/" + $scope.candidate.domain
      if (flag) {
        $scope.$apply();
      }
      initialize();
    });
    $(".user-info").removeClass("hidden")

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
          $scope.candidate.domain = "https://vk.com/" + $scope.candidate.domain
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
      get_vk(function() {
        var data = angular.copy($scope.candidate);
        data.vk_id = "" + data.uid;
        data.action = "set_new_cand_data"
        _.each(data, function(element, index, list) {
          if (!element) {
            data[index] = null;
          }
        })
        $http.post('/handlers/user.php', data).success(function(response) {
          window.clearPeople()
          window.setPeople()
          var saved = $(".saved");
          $(saved).stop(true, true);
          $(saved).fadeIn("slow");
          $(saved).fadeOut("slow");
        });
      });
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

    // $("#page-container").on("focusout", "input.vk-domain", function() {
    //   get_vk()
    // });



    function get_vk(callback) {
      var data_vk = {
        user_ids: $scope.candidate.domain,
        fields: ["photo_200", "domain"]
      }
      getVkData($scope.candidate.domain, ["photo_200", "domain"],
        function(response) {
          var user_vk = response[$scope.candidate.domain];
          if (user_vk) {
            $scope.candidate.domain = user_vk.domain
            $scope.candidate.photo_200 = user_vk.photo_200;
            $scope.candidate.vk_id = user_vk.uid;
            $scope.candidate.first_name = user_vk.first_name;
            $scope.candidate.last_name = user_vk.last_name;
          }
          $scope.$apply();
          if (callback) {
            callback();
          }
        }
      );
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