'use strict';
window.angular_conroller = null;

function loadScript(url, callback)
{

    // Adding the script tag to the head as suggested before
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    // Then bind the event to the callback function.
    // There are several events for cross browser compatibility.
    script.onreadystatechange = callback;
    script.onload = callback;

    // Fire the loading
    if ($("#footer-js script[src='"+url+"']")[0] == undefined) {
      document.getElementById("footer-js").appendChild(script);  
    } else {
      callback();
    }
}

var intID = setInterval(function(){
  if (typeof(angular) !== "undefined") {

/*отправляем Ajax чтобы посмотреть всех бойцов.*/
$('#page-container').on('click', ".get-all", function() {
  /*двойное назначение кнопки:
  Если не нажимали - добавляет таблицу со всеми бойцами и проч.*/
  if ($(".get-all").hasClass("unclick")) {
  var data =  {action: "all",};
  $.ajax({
    type: "POST",
    url: "/handlers/user.php",
    dataType: "json",
    data:  $.param(data)
  }).done(function(json) {
    if (json.result == "Success") {
      $(".get-all").toggleClass("unclick");
      $(".get-all").text("ААА! УБЕРИТЕ ИХ!!!")
      $(".search_wrap").removeClass("hidden");
      $("input.search").focus();
      $(".vCard-start").addClass("unclick");
      $(".vCard-start").show("slow");
      console.log("heRe");
      
      loadScript("/standart/js/checklist-model.js", function() {
        if (window.angular_conroller == null) {          
        window.angular_conroller = angular.module('common_c_app', ["checklist-model"])
        .controller('fightersApp', ['$scope', function ($scope) {
        $scope.checkAll = function() {
          $scope.fighters.selected_f = angular.copy($scope.fighters);
        };
        $scope.uncheckAll = function() {
          $scope.fighters.selected_f = [];
        };
          $scope.fighters = json.users;
          $scope.fighters.selected_f = [];
          _.each($scope.fighters, function(element, index, list) {
            element.checked = false;
          });
        }]);
        
      }
      angular.bootstrap(document, ['common_c_app']);
      });

    } else {
      console.log("fail1");
    }
  }).fail(function() {
    console.log("fail2");
  });
  /*Если нажимали - скрывает таблицу*/
  } else {
    $(".get-all").toggleClass("unclick");
    $(".get-all").text("а можно всех посмотреть?");
    if (!$(".vCard-start").hasClass("unclick")) {
      $(".vCard-start").trigger("click");
    }
    $(".vCard-start").hide("slow");
    $(".search_wrap").addClass("hidden");
  }
  $("table.common-contacts").toggleClass("hidden");
  //console.log("Hello");
});

$(".vCard-start").click(function() {
  /*Если не нажимали - делает видимыми кнопки для экспорта vCard*/
  if ($(".vCard-start").hasClass("unclick")) {
    $(".vCard-start").text("глаза мои б никого не видели!");
    $(".vCard-get").prop('disabled', true);
    $(".vCard-get-all").show("slow");
    $(".vCard-get-none").show("slow");
    $(".vCard-get").show("slow");
    $("table.common-contacts tbody td.ids").addClass("hidden");
    $("table.common-contacts tbody td.inputs").removeClass("hidden");
    /*второе назначение - скрыть всё.*/
  } else {
    $(".vCard-start").html("Кого хотите посмотреть?");
    $(".vCard-get-all").hide("slow");
    $(".vCard-get-none").hide("slow");
    $(".vCard-get").hide("slow");
    $("table.common-contacts tbody td.ids").removeClass("hidden");
    $("table.common-contacts tbody td.inputs").addClass("hidden");

    if (!$(".vCard-get").hasClass("unclick")) {
      $(".vCard-get").trigger("click");
    }
  }
  $(".vCard-start").toggleClass("unclick");
});

/*сделать все выбранными*/
$(".vCard-get-all").click(function() {
  $('input[type=checkbox][name=vCard_check]').each(function(){
    this.setAttribute("checked", "checked")
    $(this).parent().html($(this).parent().html());
    $(".vCard-get").prop('disabled', false);
  });
});
/*убрать выбор везде*/
$(".vCard-get-none").click(function() {
  $('input[type=checkbox][name=vCard_check]').each(function(){
    this.removeAttribute("checked")
    $(this).parent().html($(this).parent().html()); //перерисовка
    $(".vCard-get").prop('disabled', true);
  });
});

/*немного магии при выборе конкретного, чтобы всё работало*/
$('#page-container').on('change', 'input[type=checkbox][name=vCard_check]', function(event) // вешаем обработчик на все ссылки, даже созданные после загрузки страницы
{
  if (this.hasAttribute("checked")) {
    this.removeAttribute("checked");
    if ($('input[type=checkbox][name=vCard_check][checked]')[0] == undefined) {
      $(".vCard-get").prop('disabled', true);
    }
  } else {
    this.setAttribute("checked", "checked");
    $(".vCard-get").prop('disabled', false);
  }
});


/*показать контакты выбранных людей*/
$(".vCard-get").click(function() {
  if ($(".vCard-get").hasClass("unclick")) {
    $(".vCard-get").text("Скрыть контакты");
    $(".vCard-category").show("slow");
    $(".vCard-make").show("slow");
    var ids = [];
    _.each($('input[type=checkbox][name=vCard_check][checked]'), function(element, index, list) {
      ids.push($(element).val()*1);
    });
    var data = {action: "get_full_info", ids: ids}
    $.ajax({
        type: "POST",
        url: "/handlers/user.php",
        dataType: "json",
        data:  $.param(data)
      }).done(function(json) {
        var user_ids = [];
        _.each(json.users, function(element, index, list) {
          user_ids.push(element.vk_id);
        });
        if (json.result == "Success") {
          $("table.common-contacts").hide("slow");
          var data2 = {user_ids: user_ids, fields: ["photo_100", "photo_200", "domain"]}
          $.ajax({
              type: "GET",
              url: "https://api.vk.com/method/users.get",
              dataType: "jsonp",
              data:  $.param(data2)
            }).done(function(json2) {
              console.log(json2);
              console.log(json);
              if ($("table.direct-contacts")[0]==undefined){
                $(".table-container").append('<table class="table direct-contacts">\
                  <thead><tr>\
                  <th>#</th>\
                  <th>фото</th>\
                  <th>данные</th>\
                  </tr></thead>\
                  <tbody>\
                </tbody></table>')
              }
              var table_body = $("#page-container table.direct-contacts tbody")[0];

              _.each(json.users, function(element, index, list) {
                var this_card = "";
                var vk_data = _.findWhere(json2.response, {domain: element.vk_id});
                if (vk_data == undefined) {
                  vk_data = {photo_100: "http://vk.com/images/camera_b.gif",
                  domain: element.vk_id}
                  console.log(element.surname + ' ' + element.vk_id);
                }
                this_card +="<tr class='"+element.id+"'>"+
                  "<td>"+element.id+"</td>"+
                  "<td><img src='"+vk_data.photo_100+"'/></td>"+
                  "<td><ul>";
                this_card += "<li><strong>ФИО:</strong> "+element.surname + " ";
                if (element.maiden_name != null) {
                  this_card += "("+element.maiden_name+") ";
                }
                this_card += element.name + " ";

                if (element.second_name != null) {
                  this_card += element.second_name + " ";
                }
                this_card += "</li>";

                if (element.phone != null) {
                  var tel = element.phone;
                  this_card += "<li><strong>Телефон:</strong><a href='tel:+7"+tel+"'> "+
                  "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9]+
                  "</a></li>";
                }
                if (element.second_phone != null) {
                  var tel = element.second_phone;
                  this_card += "<li><strong>Телефон:</strong><a href='tel:+7"+tel+"'> "+
                  "+7 ("+tel[0]+tel[1]+tel[2]+") "+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+"-"+tel[8]+tel[9]+
                  "</a></li>";
                }
                if (element.email != null) {
                  this_card += "<li><strong>e-mail:</strong><a href='mailto:"+element.email+"'> "+
                  element.email+
                  "</a></li>";                
                }
                this_card += "<li><strong>vk:</strong> <a target='_blank' href='//vk.com/"+
                  vk_data.domain+"'>vk.com/"+vk_data.domain+"</a></li>";
                this_card += "</ul></td></tr>";

                $(table_body).append(this_card);
              });

              /*генерация vCard*/
              $(".vCard-make").click(function() {
                var card = "";
                _.each(json.users, function(element, index, list) {
                  var this_card = "";
                  this_card += "BEGIN:VCARD\n";
                  this_card += "VERSION:3.0\n";
           
                  this_card += "FN:" + element.name + " " + element.surname + "\n";
                  this_card += "N:" + element.surname;
                  if (element.maiden_name != null) {
                    this_card += ","+element.maiden_name;
                  }
                  this_card += ";"+element.name+";";
                  if (element.second_name != null) {
                    this_card += element.second_name;
                  }
                  this_card +=";;\n";
                  
                  var vk_data=_.findWhere(json2.response, {domain: element.vk_id});
                  if (vk_data != undefined) {
                    this_card += "PHOTO;VALUE=uri:"+vk_data.photo_200+"\n";
                  }

                  if (element.birthdate != null) {
                    this_card += "BDAY:"+element.birthdate+"\n";
                  }

                  if (element.phone != null) {
                    var tel = element.phone;
                    this_card += "TEL;TYPE=MAIN:"+
                    "+7-"+tel[0]+tel[1]+tel[2]+"-"+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+tel[8]+tel[9]+
                    "\n";
                  }
                  if (element.second_phone != null) {
                    var tel = element.second_phone;
                    this_card += "TEL;TYPE=CELL:"+
                    "+7-"+tel[0]+tel[1]+tel[2]+"-"+tel[3]+tel[4]+tel[5]+"-"+tel[6]+tel[7]+tel[8]+tel[9]+
                    "\n";
                  }

                  if (element.email != null) {
                    this_card += "EMAIL;TYPE=INTERNET:"+element.email+"\n";
                  }

                  if ($(".vCard-category").val() != "") {
                    this_card += "CATEGORIES:"+$(".vCard-category").val()+"\n";
                  }
                  this_card += "END:VCARD\n\n";
                  card += this_card;
                });

                /*запись полученной строки в файл*/
                var blob = new Blob([card], {type: "text/plain;charset=utf-8"});
                saveAs(blob, "contacts.vcf");

              });
            }).fail(function() {
              alert("Fail.");
              $("table.common-contacts").show("slow");
            });

        } else {
          alert("No.");
        }
      }).fail(function() {
        alert("Fail.");
      });

      /*если на кнопке написано "скрыть контакты*/
  } else {
    $(".vCard-get").text("Показать контакты");
    $(".vCard-category").hide("slow");
    $(".vCard-make").hide("slow");
    $("table.common-contacts").show("slow");
    $("table.direct-contacts").remove();
  }
  $(".vCard-get").toggleClass("unclick");
});


      clearInterval(intID);
  }
}, 50);
