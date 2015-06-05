<!DOCTYPE html>
<html>

<head lang="en">
  <title>CПО "СОзвездие" | сайт отряда</title>
  <?php include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/header.php'; include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/php_globals.php'; ?>
</head>

<body>
  <?php include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/menu.php'; ?>
  <div id="page-container">
    <table width="100%">
      <tr>
        <td>
          Вставка ВК:
          <input class="vk_input">
        </td>
        <td>
          Вставка ВК:
          <input class="vk_input">
        </td>
      </tr>
    </table>
  </div>
  <!-- page-container -->
  <?php include_once $_SERVER[ 'DOCUMENT_ROOT'] . '/own/templates/footer.php'; ?>
  <div id="after-js-container">
    <script type="text/javascript" src="jquery.hideseek.js"></script>
    <script type="text/javascript">
    window.setPeople(init_vk_search);

    function init_vk_search() {
      _.each($("input.vk_input"), function(vk_inpt) {
        var uniq = _.uniqueId();
        $(vk_inpt).wrap("<span class='vk_input '></span>");
        $(vk_inpt).removeClass("vk_input")
          .attr("name", "search")
          .attr("placeholder", "введите имя")
          .attr("type", "text")
          .attr("data-list", ".my-nav[my-uniq="+uniq+"]")
          .attr("autocomplete", "off")
          .attr("my-uniq", uniq)
          .addClass("vk_now")
        $(vk_inpt).parent().attr("my-uniq", uniq)
        $(vk_inpt).parent().append('<span class="selectPerson"><img src="" width="50px" height="50px"></span>')
        $(vk_inpt).parent().append("<ul class='my-nav'></ul>")
        $(vk_inpt).parent().children().attr("my-uniq", uniq)
        _.each(window.people, function(element) {
          $(vk_inpt).parent().children("ul").append(
            "<li>" +
            "<span class='name'>" +
            "<table><tr><td>" +
            "<span class='get-this'><img src='" + element.photo + "' class='" + element.uid + "'></img></span>" +
            "</td><td>" +
            element.first_name + "<br>" + element.last_name + " " +
            "</td></tr></table>" +
            "</span><span style='display:none;'>" +
            element.IF + " " +
            element.FI + " " +
            "https://vk.com/" + element.domain +
            "</span>" +
            "</li>")
        });
      $(vk_inpt).hideseek({
        nodata: 'No results found<u>ddd</u>',
        navigation: true,
        hidden_mode: true
      });
      var max_l = 3;
      $(vk_inpt).on("_after", function(e) {
        var lis = $(vk_inpt).parent().children("ul.my-nav").children("li").filter(function() {
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
      })
      console.log("eee")
    }

    $("body").on('click', ".get-this", function(e) {
      console.log(e)
      console.log(this)
      $("span.selectPerson").html($(this).html())
      var uid = $(this).children("img").attr("class") * 1;
      var person = _.findWhere(window.people, {
        "uid": uid
      })
      $("span.selectPerson > img").attr("title", person.IF)

    });
    </script>
  </div>
</body>

</html>