<?php if (isset($_GET[ "id"]) && (isset($_SESSION[ "current_group"]) && ($_SESSION[ "current_group"]>= COMMAND_STAFF))) { ?>
<div ng-cloak ng-controller="oneShiftAppEditDetach" id="shift-edit-detach">
  <h2>{{shift.time_name}}, {{shift.fn_date.getFullYear()}} <span ng-show="shift.place">({{shift.place}})</span></h2>
  <div ng-show="edit_detachment.in_id">
    <textarea class="bbcode" ng-model="edit_detachment.comments"></textarea>
    <button ng-click="saveDetachComment()">сохранить комментарий</button>
  </div>
  <div class="row" ng-hide="new_rank.ranking">
    <div class="col-md-3" ng-repeat="(index, value) in rankings" style="border: 4px outset green">
      <h2>Расстановка  № {{index}} </h2>
      <a href="" ng-click="editRanking(index)">(редактировать)</a>
      <a href="" ng-click="deleteRanking(index)">(удалить)</a>
      <ul>
        <li ng-repeat="(key, detachment) in value">
          {{key+1}}
          <a ng-click="deleteDetachment(index,key)" href="">
                  удалить отряд
                </a> / <a ng-click="editDetachment(index,key)" href="">
                  ред-ть ком-рий
                </a>
          <ul>
            <li ng-repeat="person in detachment.people">
              <span ng-show="person.uid">
                    <!-- <a href={{"//vk.com/"+person.domain}} target="_blank"> <img ng-src="{{person.photo_50}}"/></a> -->
                  {{person.first_name}} {{person.last_name}}
                  </span> <span ng-hide="person.uid">{{person}}</span>
            </li>
          </ul>
          <div class="table-bordered" ng-bind-html="detachment.bbcomments" ng-show="detachment.comments"></div>
        </li>
      </ul>
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
          <li ng-repeat="want in all_apply">
            <a href="//vk.com/{{want.domain}}" target="_blank"> <img ng-src="{{want.photo}}" /></a>
            {{want.first_name}} {{want.last_name}}; vk.com/
            <input type="text" placeholder="домен VK" ng-model="want.domain" readonly size="{{(want.domain).length}}" />
          </li>
        </ul>
      </div>
      <div class="col-xs-7 info-str">
        <button ng-click="newRanking(false)">Создать новую расстановку</button>
        <div class="table-bordered" ng-show="new_rank.ranking">
            <h2>Расстановка № {{new_rank.ranking}}
              <button class="btn btn-primary text-right addDetachment" ng-click="addDetachment()" ng-init="add_det=false">добавить отряд в расстановку</button>
            </h2>
            <form ng-show="add_det">
              <button class="btn btn-primary text-right" ng-click="addDetachmentSubmit()">Создать отряд</button>
              <br>
              <i>Вставьте домен ВК или имя человека, если он не из со*</i>
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
              ссылка ВК: <input type="text" ng-model="newdetachment.people[key]" placeholder="домен VK"/> <img src="/own/images/close.png" width="20px" ng-click="deletePersonEdit(key)"> <br>
            </span> какие дети, комментарии, дополнения и т.п.
              <br>
              <textarea class="bbcode" ng-model="newdetachment.comments"></textarea>
            </form>
            <!-- создание расстановки по отрядам -->
            <div ng-show="edit_detachment.in_id">
              <textarea class="bbcode" ng-model="edit_detachment.comments"></textarea>
              <button ng-click="saveDetachComment()">сохранить комментарий</button>
            </div>
            <ul>
              <li ng-repeat="(key, detachment) in detachments" ng-show="detachment.ranking*1==new_rank.ranking*1">
                {{key+1}}
                <a ng-click="editDetachment(key)" href="">
                  редактировать комментарий
                </a> |
                <a ng-click="deleteDetachment(key)" href="">
                  удалить отряд
                </a>
                <ul>
                  <li ng-repeat="person in detachment.people">
                    <span ng-show="person.uid">
                    <a href={{"//vk.com/"+person.domain}} target="_blank"> <img ng-src="{{person.photo_50}}"/></a>
                  {{person.first_name}} {{person.last_name}}
                  </span> <span ng-hide="person.uid">{{person}}</span>
                  </li>
                </ul>
                <div class="table-bordered" ng-bind-html="detachment.bbcomments" ng-show="detachment.comments"></div>
              </li>
            </ul>
          <!-- расстановка -->
        </div>
      </div>
    </div>
    <hr>
  </div>
  <?php } ?>
</div>
