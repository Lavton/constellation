<?php
if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
  /*просмотр смены*/
?>
<div ng-cloak ng-controller="oneShiftAppEditDetach" id="shift-edit-detach">
      <h2>{{shift.name}}</h2>
      <div  ng-show="edit_detachment.in_id">
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

     <!--          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?>
                  <a ng-click="editDetachment(key)" href="">
                  редактировать комментарий
                </a> | 
              <?php } ?> -->
              <ul>
                <li ng-repeat="person in detachment.people">
                  <span ng-show="person.uid">
                    <!-- <a href={{"//vk.com/"+person.domain}} target="_blank"> <img ng-src="{{person.photo_50}}"/></a> -->
                  {{person.first_name}} {{person.last_name}}
                  </span> <span ng-hide="person.uid">{{person}}</span>
                </li>
              </ul>
              <div  class="table-bordered" ng-bind-html="detachment.bbcomments" ng-show="detachment.comments"></div>
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
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?>
        <ul ng-show="add_det">
          <li ng-show="myself.vk_id"> 
            <a href={{"//vk.com/"+myself.domain}} target="_blank"> <img ng-src="{{myself.photo_50}}"/></a>
            {{myself.first_name}} {{myself.last_name}}; vk.com/<input type="text" placeholder="домен VK" ng-model="myself.domain" readonly size="{{(myself.domain).length}}"/>
          </li>

          <li ng-repeat="want in all_apply">
            <a href={{"//vk.com/"+want.domain}} target="_blank"> <img ng-src="{{want.photo_50}}"/></a>
            {{want.first_name}} {{want.last_name}}; vk.com/<input type="text" placeholder="домен VK" ng-model="want.domain" readonly size="{{(want.domain).length}}"/>
          </li>
        </ul>
        <?php } ?>


      </div>

      <!-- добавиться (добавить) на смену -->
      <div class="col-xs-7 info-str">
        <button ng-click="newRanking(false)">Создать новую расстановку</button>
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?>
        <div class="table-bordered" ng-show="new_rank.ranking"> 
        <?php } else { ?>
        <div class="table-bordered" ng-show="detachments.length"> 
        <?php } ?>
          <h2>Расстановка № {{new_rank.ranking}}
            <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?>
              <button class="btn btn-primary text-right addDetachment" ng-click="addDetachment()" ng-init="add_det=false">добавить отряд в расстановку</button>
            <?php } ?>
          </h2>
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?>
          <form ng-show="add_det">
          <button class="btn btn-primary text-right" ng-click="addDetachmentSubmit()">Создать отряд</button>

            <button ng-click="addNewPersonDetach()">добавить человека</button><br>
            <i>Вставьте домен ВК или имя человека, если он не из со*</i><br>
            <span ng-repeat="key in newdetachment.fieldKeys">
              ссылка ВК: <input type="text" ng-model="newdetachment.people[key]" placeholder="домен VK"/> <br>
            </span>
            какие дети, комментарии, дополнения и т.п.<br>
            <textarea class="bbcode" ng-model="newdetachment.comments"></textarea>
          </form>
          <?php } ?>

          <!-- создание расстановки по отрядам -->
          <div  ng-show="edit_detachment.in_id">
            <textarea class="bbcode" ng-model="edit_detachment.comments"></textarea>
            <button ng-click="saveDetachComment()">сохранить комментарий</button>
          </div>
           <ul>
            <li ng-repeat="(key, detachment) in detachments" ng-show="detachment.ranking*1==new_rank.ranking*1">
              {{key+1}}
              <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?>
                  <a ng-click="editDetachment(key)" href="">
                  редактировать комментарий
                </a> | 
                <a ng-click="deleteDetachment(key)" href="">
                  удалить отряд
                </a>
              <?php } ?>
              <ul>
                <li ng-repeat="person in detachment.people">
                  <span ng-show="person.uid">
                    <a href={{"//vk.com/"+person.domain}} target="_blank"> <img ng-src="{{person.photo_50}}"/></a>
                  {{person.first_name}} {{person.last_name}}
                  </span> <span ng-hide="person.uid">{{person}}</span>
                </li>
              </ul>
              <div  class="table-bordered" ng-bind-html="detachment.bbcomments" ng-show="detachment.comments"></div>
            </li>
          </ul>

        </div><!-- расстановка -->
      </div>
    </div>
  </div>



<hr>

</div>

<br/><br/><a href="#" class="shift_priv" class="ajax-nav"><<предыдущая</a> &nbsp; &nbsp;
<a href="#" class="shift_next pull-right" class="ajax-nav">следующая>></a>


<?php
}
?>
