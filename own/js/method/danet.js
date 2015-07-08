(function() {
  /*логика ангулара*/
  function init_angular_s_c($scope, $http) {
    /*инициализация по умолочанию*/
    $scope.window = window;
    $scope.Object = Object;
    $scope.num_of_select = 0;

    // список списков страниц
    $scope.getPages = function() {
      $("button.getList").attr("disabled", "true")
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
  }


  function init() {
    window.init_ang("parseDNApp", init_angular_s_c, "parse-DN");
  }
  init();
  window.registerInit(init)

})();
