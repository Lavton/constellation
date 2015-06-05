<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/header.php'; include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/php_globals.php'; ?>
</head>

<body>
  <?php include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/menu.php'; ?>
  <div id="page-container">
    Вставка ВК:
    <input class="vk_input"> <img src="" width="50px" height="50px">
    <br>
    <form id="search-highlight" method="post" action="#">
      <input type="text" name="term" id="term" />
      <input type="submit" name="submit" id="submit" value="Search" />
    </form>
    <p class="results"></p>
    <div class="main">
      Контент
      <ul>
        <li>Привет</li> Hello
      </ul>
    </div>
    <article>
      <label for="search">Top 10 Most Popular Frameworks</label>
      <input id="search-navigation" name="search" placeholder="Start typing here" type="text" data-list=".navigation_list" data-nodata="No results found" autocomplete="off">
      <ul class="vertical navigation_list hidden_mode_list">
        <li><a href="http://www.asp.net/">ASP.NET</a></li>
        <li><a href="http://flask.pocoo.org/">Flask</a></li>
        <li><a href="http://codeigniter.com/">CodeIgniter</a></li>
        <li><a href="http://framework.zend.com/">Zend</a></li>
        <li><a href="http://rubyonrails.org/">Ruby on Rails</a></li>
        <li><a href="http://angularjs.org/">AngularJS</a></li>
        <li><a href="http://www.djangoproject.com/">Django</a></li>
        <li><a href="http://www.yiiframework.com/">Yii</a></li>
        <li><a href="http://symfony.com/">Symfony</a></li>
        <li><a href="http://cakephp.org/">CakePHP</a></li>
      </ul>
    </article>
  </div>
  <!-- page-container -->
  <?php include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/footer.php'; ?>
  <div id="after-js-container">
    <script type="text/javascript" src="jquery.hideseek.js"></script>
    <script type="text/javascript">
    window.setPeople(init_vk_search);

    function init_vk_search() {
      $("input.vk_input").wrap("<span class='vk_input'></span>");
      $("input.vk_input").removeClass("vk_input")
        .attr("name", "search")
        .attr("placeholder", "введите имя")
        .attr("type", "text")
        .attr("data-list", ".my-nav")
        .attr("autocomplete", "off")
        .addClass("vk_now")

      $("span.vk_input").append("<ul class='my-nav'></ul>")
        //       $("span.vk_input > ul").append(_.template(
        //         "<% _.each(people, function(person) {%>\
        // <li><span class='name'><%= person.first_name %><br><%= person.last_name %></li></span>\
        // <span class='hideThis'><%= person.IF %> <%= person.FI %> <%= person.domain %></span>\
        // <% }); %>", {
        //           "people": window.people
        //         }))
      _.each(window.people, function(element) {
        $("span.vk_input > ul").append(
          "<li><a href='/'>" +
          "<span class='name'>" +
          "<table><tr><td>" +
          "<img src='" + element.photo + "' class='get-this " + element.uid + "'></img>" +
          "</td><td>" +
          element.first_name + "<br>" + element.last_name + " " +
          "</td></tr></table>" +
          "</span><span style='display:none;'>" +
          element.IF + " " +
          "https://vk.com/"+element.domain + " " +
          element.FI +
          "</span>" +
          "</a></li>")
      });
      $(".hideThis").css('display', 'none')
      $('input.vk_now').hideseek({
        nodata: 'No results found<u>ddd</u>',
        navigation: true,
        // hidden_mode: true
      });
      var max_l = 3;
      $('input.vk_now').on("_after", function(e) {
        console.log(e)
        var lis = $("span.vk_input > ul.my-nav > li").filter(function() {
          return $(this).css('display') == 'list-item';
        })
        var curr_l = 0;
        _.each(lis, function(element) {
          curr_l += 1
          if (curr_l > max_l) {
            $(element).css('display', 'none')
          }
        })
      });
      console.log("eee")
    }

    $("body").on('click', ".get-this", function(e) {
      console.log(e)
      console.log(this)
    });
    </script>
    <script type="text/javascript">
    $(document).ready(function() {
      $('#search-navigation').hideseek({
        nodata: 'No results found',
        navigation: true,
        // hidden_mode: true
      });
      $('#search-navigation').on("_after", function(e) {
        console.log(e)
      });
    });
    </script>
  </div>
</body>

</html>