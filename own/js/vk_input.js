(function($) {
  "use strict";
  var defaults = {};
  // актуальные настройки, глобальные
  var options;
  $.fn.vkinput = function(params) {
    // при многократном вызове настройки будут сохранятся
    // и замещаться при необходимости
    var options = $.extend({}, defaults, options, params);
    $(this).click(function() {
      $(this).css('color', options.color);
    });
    return this.each(function() {
      var $this = $(this);
      var state_of_fin = false;
      /*окончательная подстановка. Вызываем соотв. событие*/
      function paste_final(vk_inpt, person) {
        state_of_fin = true;
        vk_inpt.val(person.uid)
        vk_inpt.parent().children("span.selectPerson").html(
          "<img src='" + person.photo + "' title='" + person.IF + "'></img>"
        );
        var lis = vk_inpt.parent().children("ul.my-nav").children("li").filter(function() {
          return $(this).css('display') == 'list-item';
        })
        _.each(lis, function(element) {
          $(element).css('display', 'none')
        })

        vk_inpt.trigger('_final_select');
        vk_inpt.trigger('keyup')
        vk_inpt.trigger('keydown')
        vk_inpt.trigger('keypress')

      }
      var uniq = _.uniqueId();
      $this.wrap("<span class='vk_input'></span>");
      var $parent = $this.parent();
      $this.removeClass("vk_input")
        .attr("name", "search")
        .attr("placeholder", "введите имя")
        .attr("type", "text")
        .attr("data-list", ".my-nav[my-uniq=" + uniq + "]")
        .attr("autocomplete", "off")
        .attr("my-uniq", uniq)
        .addClass("vk_now")
      $parent.attr("my-uniq", uniq)
        /*справа отображается картинка выбранного человека*/
      $parent.append('<span class="selectPerson"><img src="" width="50px" height="50px"></span>')
        /*для hideseek выводим всю инфу в список*/
      $parent.append("<ul class='my-nav'></ul>")
      $parent.children().attr("my-uniq", uniq)
      _.each(window.people, function(element) {
        $parent.children("ul").append(
          "<li>" +
          "<span class='name'>" +
          "<table><tr><td>" +
          "<span class='get-this' my-uniq='" + uniq + "'><img src='" + element.photo + "' class='" + element.uid + "'></img></span>" +
          "</td><td>" +
          element.first_name + "<br>" + element.last_name + " " +
          "</td></tr></table>" +
          "</span><span style='display:none;' class='" + element.domain + "'>" +
          element.IF + " " +
          element.FI + " " +
          element.maiden_name + " " +
          element.nickname + " " +
          "https://vk.com/" + element.domain + " " +
          "https://vk.com/id" + element.uid + " " +
          "</span>" +
          "</li>")
      });
      /*инициализируем hideseek. Именно он выполняет черновую работу*/
      $this.hideseek({
        nodata: 'Поиск не дал результатов. <br>Введите ссылку на человека ВКонтакте <br>и кликните по пустому квадрату справа',
        navigation: true,
        hidden_mode: true,
        callback_nav: function(inpt, curr_l) {
          /*эта функция вызывается при клике на Enter в режиме навигации*/
          if (curr_l.length) { /*проверка, что именно в режиме навигации*/
            /*показываем найденное справа и вставляем строку*/
            var uid = curr_l.find("img").attr("class") * 1;
            var person = _.findWhere(window.people, {
              "uid": uid
            })
            paste_final($this, person)
          }
        }
      });

      /*hideseek отображает все элементы. Скроем лишние в ручную*/
      var max_l = 4;
      $this.on("_after", function(e) {
        var lis = $parent.children("ul.my-nav").children("li").filter(function() {
          return $(this).css('display') == 'list-item';
        })
        var curr_l = 0;
        _.each(lis, function(element) {
            curr_l += 1
            if (curr_l > max_l) {
              $(element).css('display', 'none')
            }
          })
          /*тут же - если мы всё ещё ищем человека -> никто не выбран -> уберём фото справа*/
        if (state_of_fin == true) {
          state_of_fin = false
        } else {
          if (curr_l > 1) {
            $("span.selectPerson[my-uniq=" + uniq + "]").html("<img width='50px' height='50px'></img>")
          }
        }
      });

      /*при клике на картинку кого-либо мы его выбираем*/
      $("body").on('click', ".get-this[my-uniq=" + uniq + "]", function(e) {
        var uid = $(this).children("img").attr("class") * 1;
        var person = _.findWhere(window.people, {
          "uid": uid
        })
        paste_final($this, person)
      });

      /*при клике на картинку справа мы ищем человека с input'a*/
      $("body").on('click', "span.selectPerson[my-uniq=" + uniq + "]", function(e) {
        window.addPeople($this.val(), function(vk_resp) {
          if (vk_resp[$this.val()]) {
            var person = _.findWhere(window.people, {
              "uid": vk_resp[$this.val()].uid * 1
            })
            paste_final($this, person)
          }
        })
      });
    });
  };
})(jQuery);
