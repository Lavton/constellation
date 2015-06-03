'use strict';

function get_candidate_info(userid) {
  /*логика ангулара*/
  function init_angular_o_cand_c($scope, $http, $locale) {
    $locale.id = 'ru-ru' //TODO make it works(
    $scope.goodView = function(tel) {
      return tel ? "+7 (" + tel[0] + tel[1] + tel[2] + ") " + tel[3] + tel[4] + tel[5] + "-" + tel[6] + tel[7] + "-" + tel[8] + tel[9] : ""
    }
    $scope.id = userid;
    $scope.candidate = {};
    $scope.editPerson = function() {
      $(".user-info").toggleClass("hidden");
      $(".user-edit").toggleClass("hidden");
      $scope.master = angular.copy($scope.candidate);
    };
    $(".user-info").removeClass("hidden")
    var inthrefID = setInterval(function() {
      var fid = window.location.href.split("/")
      var userid = fid[fid.length - 1] //TODO сделать тут нормально!
      if (userid != "candidats") {
        clearInterval(inthrefID);
        var data = {
            action: "get_one_candidate_info",
            id: userid
          }
          // debugger;
        $scope.candidate.photo_200 = "http://vk.com/images/camera_b.gif"
        $.ajax({
          type: "POST",
          url: "/handlers/user.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          $scope.candidate = json.user;
          console.log($scope.candidate);
          $("a.profile_priv").attr("href", json.prev.mid)
          $("a.profile_next").attr("href", json.next.mid)
          if (!json.prev.mid) {
            $("a.profile_priv").hide();
          }

          if (!json.next.mid) {
            $("a.profile_next").hide();
          }
          $scope.candidate.domain = "id" + $scope.candidate.vk_id
          $scope.$apply();

          get_vk();
        });
      }
    }, 100);

    $scope.submit = function() {
      get_vk(function() {
        var data = angular.copy($scope.candidate);
        data.vk_id = "" + data.vk_id;
        data.action = "set_new_cand_data"
        _.each(data, function(element, index, list) {
          if (!element) {
            data[index] = null;
          }
        })
        $http.post('/handlers/user.php', data).success(function(response) {
          var saved = $(".saved");
          $(saved).stop(true, true);
          $(saved).fadeIn("slow");
          $(saved).fadeOut("slow");
        });
      });
    }
    $scope.resetInfo = function() {
      $scope.candidate = angular.copy($scope.master);
    }

    $scope.killCandidate = function() {
      var fid = window.location.href.split("/")
      var userid = fid[fid.length - 1] //TODO сделать тут нормально!
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
            window.location = "/";
          }
        });
      }
    }

    $("#page-container").on("focusout", "input.vk-domain", function() {
      get_vk()
    });



    function get_vk(callback) {
      var data_vk = {
        user_ids: $scope.candidate.domain,
        fields: ["photo_200", "domain"]
      }
      getVkData($scope.candidate.domain, ["photo_200", "domain"],
        function(response) {
          var user_vk = response[$scope.candidate.domain];
          $scope.candidate.domain = user_vk.domain
          $scope.candidate.photo_200 = user_vk.photo_200;
          $scope.candidate.vk_id = user_vk.uid;
          $scope.candidate.first_name = user_vk.first_name;
          $scope.candidate.last_name = user_vk.last_name;

          $scope.$apply();
          if (callback) {
            callback();
          }
        }
      );
    }
  }








  if (window.candidats == undefined) {
    window.candidats = {}
  }
  window.candidats.one_angular_conroller = null;
  var fid = window.location.href.split("/")
  var userid = fid[fid.length - 1] //TODO сделать тут нормально!

  if (!window.candidats.one_script) {
    window.candidats.one_script = true;
    var intID = setInterval(function() {
      var fid = window.location.href.split("/")
      var userid = fid[fid.length - 1] //TODO сделать тут нормально!
      if ((typeof(angular) !== "undefined") && (userid != "candidats")) {
        if (window.candidats.one_angular_conroller == null) {
          window.candidats.one_angular_conroller = angular.module('one_cond_app', [], function($httpProvider) { //магия, чтобы PHP понимал запрос
            // Используем x-www-form-urlencoded Content-Type
            $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
            // Переопределяем дефолтный transformRequest в $http-сервисе
            $httpProvider.defaults.transformRequest = [function(data) {
              var param = function(obj) {
                var query = '';
                var name, value, fullSubName, subValue, innerObj, i;
                for (name in obj) {
                  value = obj[name];
                  if (value instanceof Array) {
                    for (i = 0; i < value.length; ++i) {
                      subValue = value[i];
                      fullSubName = name + '[' + i + ']';
                      innerObj = {};
                      innerObj[fullSubName] = subValue;
                      query += param(innerObj) + '&';
                    }
                  } else if (value instanceof Object) {
                    for (subName in value) {
                      subValue = value[subName];
                      fullSubName = name + '[' + subName + ']';
                      innerObj = {};
                      innerObj[fullSubName] = subValue;
                      query += param(innerObj) + '&';
                    }
                  } else if (value !== undefined && value !== null) {
                    query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
                  }
                }
                return query.length ? query.substr(0, query.length - 1) : query;
              };
              return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
            }];
          });
          //запускаем ангулар
          window.candidats.one_angular_conroller.controller('oneCandidateApp', ['$scope', '$http', '$locale', init_angular_o_cand_c]);
          angular.bootstrap(document, ['one_cond_app']);
          window.candidats.was_init_one = true;

        } else {
          angular.bootstrap(document, ['one_cond_app']);
        }
        clearInterval(intID);
      }
    }, 50);
  } else {
    angular.bootstrap(document, ['one_cond_app']);
  }

}

//TODO phone input