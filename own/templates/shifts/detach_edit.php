<?php if (isset($_GET[ "id"]) && (isset($_SESSION[ "current_group"]) && ($_SESSION[ "current_group"]>= COMMAND_STAFF))) { ?>
  <div ng-cloak ng-controller="oneShiftAppEditDetach" id="shift-edit-detach">
    <h2>{{shift.name}}, {{shift.fn_date.getFullYear()}} <span ng-show="shift.place">({{shift.place}})</span></h2>
    <div class="row" ng-hide="new_rank.ranking">
      <div class="col-md-3" ng-repeat="(index, value) in rankings" style="border: 4px outset green">
        <h3>Расстановка  № {{index}} 
         <a href="" ng-click="editRanking(index)"><img src="/own/images/edit.png" width="20px"></a><a href="" ng-click="deleteRanking(index)"><img src="/own/images/delete.png" width="20px"></a>
        </h3>
        <ul>
          <li ng-repeat="detachment in value">
            <ul>
              <li ng-repeat="person in detachment" ng-show="person.id">
                <span ng-show="person.user">
                  <a href="/about/users/{{person.id}}" target="_blank">
                    <img ng-src="{{person.photo}}" width="20">
                  </a>
                 {{person.first_name}} {{person.last_name}}
                </span>
                        <span ng-show="person.name">
                  <img src="/own/images/plus.png" width="20">
                 {{person.name}}
                </span>
              </li>
            </ul>
          </li>
        </ul>
        <br/>
        <div ng-show="_.toArray(value)[0][0].comments">
          <hr>
          Комментарии к расстановке:
          <div class="rank-comments-{{index}} table-bordered"></div>
        </div>
        <hr>
        <a href="" ng-click="publish(index)">опубликовать эту расстановку</a>
      </div>
    </div>
    <div class="shift-info">
      <div class="col-xs-12">
        <hr>
        <span class="saved">  (Изменения сохранены)</span>
      </div>
      <div class="row own-row">
        <div class="col-xs-5 info-str">
          <ul ng-show="add_det">
            <li ng-repeat="want in all_apply" ng-hide="want.have_in_det == true">
              <a href="/about/users/{{want.user}}" target="_blank"> <img ng-src="{{want.photo}}" width="30" /></a>
              {{want.first_name}} {{want.last_name}};
            </li>
          </ul>
        </div>
        <div class="col-xs-7 info-str">
          <!-- {{detachments | json}} <br> -->
          <button ng-click="newRanking()">Создать новую расстановку</button>
          <button ng-show="new_rank.ranking" ng-click="hideRanking()">Скрыть</button>
          <div class="table-bordered" ng-show="new_rank.ranking">
          <!-- {{new_rank | json}} -->
            <h2>Расстановка № {{new_rank.ranking}}
              <button class="btn btn-primary text-right addDetachment" ng-click="addDetachment()" ng-init="add_det=false">добавить отряд в расстановку</button>
            </h2>
            <form ng-show="add_det">
              <button class="btn btn-primary text-right" ng-click="addDetachmentSubmit()" ng-hide="window.isNumeric(newdetachment.editKey)">Создать отряд</button>
              <button class="btn btn-primary text-right" ng-click="addDetachmentSubmit(newdetachment.editKey)" ng-show="window.isNumeric(newdetachment.editKey)">Редактировать отряд</button>
              <br>
              <i>Поле для ввода id/имени</i>
              <br>
              <table>
                <tr>
                  <td>
                    <input class="vk_input" ng-model="newdetachment.newPerson">
                  </td>
                  <td> <img src="/own/images/check.png" width="40px" ng-click="okAddPerson()">
                  </td>
                </tr>
              </table>
              <span ng-repeat="key in newdetachment.fieldKeys">
              id на сайте/имя: <input type="text" ng-model="newdetachment.people[key]" placeholder="домен VK"/> <img src="/own/images/delete.png" width="15px" ng-click="deletePersonEdit(key)"> <br>
            </span>
            </form>
            <!-- создание расстановки по отрядам -->
            <ul>
              <li ng-repeat="(index, detachment) in rankings[new_rank.ranking]" ng-show="index!='null'">
                <a href="" ng-click="editDetachment(index, new_rank.ranking)"><img src="/own/images/edit.png" width="10px"></a>&nbsp;&nbsp;&nbsp;
                <a href="" ng-click="deleteDetachment(index, new_rank.ranking)"><img src="/own/images/delete.png" width="10px"></a>
                <ul>
                  <li ng-repeat="person in detachment" ng-show="person.id">
                    <span ng-show="person.user">
                  <a href="/about/users/{{person.id}}" target="_blank">
                    <img ng-src="{{person.photo}}" width="20">
                  </a>
                 {{person.first_name}} {{person.last_name}}
                </span>
                    <span ng-show="person.name">
                  <img src="/own/images/plus.png" width="20">
                 {{person.name}}
                </span>
                  </li>
                </ul>
              </li>
            </ul>
            <hr>
            <br>Комментарии к расстановке:
            <br>
            <textarea class="bbcode" ng-model="new_rank.comments">
            </textarea>
            <span class="saved">  (Изменения сохранены)</span>
            <button ng-click="saveComment(new_rank.ranking)">(сохранить комментарий)</button>
            <!-- расстановка -->
          </div>
        </div>
      </div>
      <hr>
    </div>
  </div>
  <?php } ?>
