(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.Object = Object;
    $scope.num_of_select = 0;

    // список списков страниц
    $scope.getListPages = function() {
      // $("button.getList").attr("disabled", "true")
      $scope.get_list = true;
      var data = {
        action: "all_pages",
      };
      $.ajax({
        type: "POST",
        url: "/handlers/parseDN.php",
        dataType: "json",
        data: $.param(data)
      }).done(function(json) {
        console.log(json)
        $scope.listPages = json;
        $scope.get_pages = true;
        $scope.$apply();
      });
    }

    // добавить списка страниц
    $scope.swithCheck = function(pages) {
      if (pages.state) {
        pages.state = false;
        $scope.num_of_select -= 1;
      } else {
        pages.state = true;
        $scope.num_of_select += 1;
      }
    }

    // начать парсить
    $scope.beginParse = function() {
      $scope.get_pages = false;
      $scope.selectPages = _.filter($scope.listPages, function(pages) {
        return pages.state;
      });
      getPages();
    }

    // получить рандомный инт
    function getRandomInt(min, max) {
      return Math.floor(Math.random() * (max - min)) + min;
    }

    // получить список страниц
    function getPages() {
      var time_in_sec = 30;
      $scope.sitPages = [];
      $scope.situations = [];
      $scope.con_situations = "";
      console.log("we are getting pages")
      $scope.time_left = time_in_sec;

      var intSit = setInterval(function() {
        if (($scope.selectPages.length <= 0) && ($scope.sitPages.length <= 0)) {
          clearInterval(intSit);
          return;
        }
        if ($scope.sitPages.length > 0) {
          var dataSit = {
            action: "situation",
            url: $scope.sitPages.shift()
          };
          var rand_time=7*1000+getRandomInt(-4*1000, 4*1000);
          setTimeout(function() {
            $.ajax({
              type: "POST",
              url: "/handlers/parseDN.php",
              dataType: "json",
              data: $.param(dataSit)
            }).done(function(json) {
              console.log("rand time =", rand_time);
              $scope.con_situations = "";
              $scope.situations.push(json);
              _.each($scope.situations, function(situation) {
                $scope.con_situations += situation[0] + "\n" + situation[1] + "\n" + situation[2] + "\n\n";
              })
              $scope.$apply();
            });
          }, rand_time);
        }
      }, 10 * 1000);

      var intSec = setInterval(function() {
        $scope.time_left -= 1;
        $scope.$apply();
        if ($scope.time_left <= 2) {
          clearInterval(intSec);
        }
      }, 1000);

      var intID = setInterval(function() {
        console.log("new parse")
        $scope.time_left = time_in_sec;
        var intSec = setInterval(function() {
          $scope.time_left -= 1;
          $scope.$apply();
          if ($scope.time_left <= 2) {
            clearInterval(intSec);
          }
        }, 1000);
        var thisPage = $scope.selectPages.shift();
        $scope.$apply();
        var data = {
          action: "pages",
          url: thisPage.url
        };
        $.ajax({
          type: "POST",
          url: "/handlers/parseDN.php",
          dataType: "json",
          data: $.param(data)
        }).done(function(json) {
          console.log(json)
          $scope.sitPages = $scope.sitPages.concat(json);
          $scope.$apply();
        });
        if ($scope.selectPages.length <= 0) {
          clearInterval(intID);
          clearInterval(intSec);
          $scope.time_left = "ЗАВЕРШЕНО"
        }
      }, time_in_sec * 1000);
    }
  }


  function init() {
    window.init_ang("parseDNApp", init_angular_s_c, "parse-DN");
  }
  init();
  window.registerInit(init)

})();
