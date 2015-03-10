<?php
if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
  /*просмотр смены*/
?>
<div ng-cloak ng-controller="oneShiftApp">
  <div class="shift-info hidden">
    <div class="col-xs-12">
      <h2>{{shift.name}}
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
        <button type="button" class="btn btn-primary text-right" ng-click="editShiftInfo()">Редактировать</button>
        <?php } ?>
      </h2>
      <hr>
      <span class="saved">  (Изменения сохранены)</span>
    </div>
    <div class="row own-row">
      <div class="col-xs-5 info-str">
        <ul>
          <li ng-show="shift.start_date"><strong>Дата начала:</strong> {{shift.start_date | date: 'dd.MM.yyyy'}} </li>
          <li ng-show="shift.finish_date"><strong>Дата окончания:</strong> {{shift.finish_date | date: 'dd.MM.yyyy'}} </li>
          <li ng-show="shift.place"><strong>Место:</strong> {{shift.place}} </li>
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
          <li ng-show="shift.visibility"><strong>Виден для: </strong>{{shift.visibility}} ({{groups[shift.visibility]}})</li>
          <?php } ?>   
          <li ng-show="shift.comments"><strong>Комментарии:</strong><br/> {{shift.comments}} </li>     
        </ul>
      </div>
      <div class="col-xs-7 info-str" ng-show="(shift.today <= shift.st_date)">
        <h3>Записаться на смену</h3>
        <details ng-open="open2">
           <summary>Записаться</summary>
        <form ng-submit="guessAdd()">
        <input type="submit" class="btn btn-primary text-right" value="Записаться"></input>

          <ul>
            <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?>
            <li><b>Кого добавить?</b> (как комсостав, можно добавить другого. Хотите добавиться сами - просто проигнорируйте поле) <br/>
              vk.com/<input type="text" ng-model="adding.smbdy" placeholder="домен VK"/> 
            </li>
            <?php }?>
            <li> <i>С какой вероятностью вы поедете на смену?</i>  (будет видно всем)<br/>
              {{adding.prob}} <input type="range" ng-model="adding.prob" ng-init="adding.prob=100" min="0" max="100" scale="1" style="width: 70%"/><br/>
            </li>

            <li> <i>Социальный статус детей</i> (будет видно всем) <br/>
              Социалка: <input type="checkbox" ng-model="adding.soc" ng-init="adding.soc=true"> &nbsp; <br/> 
              Домашние: <input type="checkbox" ng-model="adding.nonsoc" ng-init="adding.nonsoc=true">
            </li>
            <li> <i>Работа на профильных детях</i> (будет видно всем)<br/>
              Профильники: <input type="checkbox" ng-model="adding.prof" ng-init="adding.prof=true"> &nbsp; <br/> 
              Непрофильники: <input type="checkbox" ng-model="adding.nonprof" ng-init="adding.nonprof=true">
            </li>
            <li> <i>Желаемый возраст детей</i> (будет видно всем)<br/>
              От: {{adding.min_age}} <input type="range" ng-model="adding.min_age" ng-init="adding.min_age=4" min="4" max={{adding.max_age}} scale="1" style="width: 40%"/><br/>
              До: {{adding.max_age}} <input type="range" ng-model="adding.max_age" ng-init="adding.max_age=17" min={{adding.min_age}} max="17" scale="1" style="width: 40%"/>
            </li>
            <li> 
              <i>С кем бы вы хотели работать?</i> (до 3х человек, будет видно комсоставу и тому, кого вы указали)<br/>
              <div class="row own-row">
                <div class="col-xs-5">
                  vk.com/<input type="text" placeholder="домен VK" ng-model="adding.like1" size="7"/><br/>
                  vk.com/<input type="text" placeholder="домен VK" ng-model="adding.like2" size="7"/><br/>
                  vk.com/<input type="text" placeholder="домен VK" ng-model="adding.like3" size="7"/><br/> <br/>
                </div>
                <div class="col-xs-7">
                  <div class="like_h" ng-show="adding.vk_likes && !adding.smbdy">
                    C вами хотели бы работать:
                    <table>
                      <tr ng-repeat="user in adding.vk_likes">
                        <td><img ng-src="{{user.photo_50}}"/>&nbsp; </td>
                        <td>
                          <a href={{"//vk.com/"+user.domain}} target="_blank">vk.com/</a><input type="text" disabled ng-model="user.domain" size="{{(user.domain).length}}"> <br> 
                          {{user.first_name}} {{user.last_name}} 
                          <span ng-show="user.fighter">(
                            <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) { ?>
                            <a href={{"/about/users/"+user.fighter}}>
                              <?php } ?>
                              боец
                            <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) { ?>
                            </a>
                              <?php } ?>
                          )</span>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </li>              
            
            <li> <i>С кем бы вы НЕ хотели работать?</i> (до 3х человек, будет видно только комсоставу)<br/>
              vk.com/<input type="text" placeholder="домен VK" ng-model="adding.dislike1"/><br/>
              vk.com/<input type="text" placeholder="домен VK" ng-model="adding.dislike2"/><br/>
              vk.com/<input type="text" placeholder="домен VK" ng-model="adding.dislike3"/><br/>
            </li>
            <li> <i>Комментарии</i> (любые. Про шанс поехать, про детей, про напарников. Будет видно только комсоставу) <br/>
              <textarea ng-model="adding.comments" cols=50 rows=5></textarea>
            </li>
          </ul>
        </form>
        </details>
      </div>
    </div>
  </div>


<?php /*редактируют лишь ком состав и админ*/
if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { 
  ?> 
  <div class="shift-edit hidden">
    <form ng-submit="submit()">
      <div class="col-xs-12">
        <h2>{{shift.name}}
          <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
          <input type="submit" class="btn btn-primary text-right" ng-click="editShiftInfo()" value="Сохранить"></input>
            <button type="button" class="btn btn-primary text-right" ng-click="resetInfo(); editShiftInfo()" >Отменить</input>
          <?php } ?>
        </h2>        
        <hr>
      </div>
      <div class="row own-row">
        <div class="col-xs-5 info-str">
          <ul>
            <li><strong>Дата начала:</strong> <input type="date" ng-model="shift.start_date" size="{{(shift.start_date).length}}" /> </li>
            <li><strong>Дата окончания:</strong> <input type="date" ng-model="shift.finish_date" size="{{(shift.finish_date).length}}" /> </li>
            <li><strong>Место:</strong> <input type="text" ng-model="shift.place" size="{{(shift.place).length}}" /> </li>
            <li><strong>Виден для: </strong><input type="number" min="1" max="7" ng-model="shift.visibility" size="{{(shift.visibility).length}}" /> ({{groups[shift.visibility]}})</li>
            <li><strong>Комментарии:</strong><br/> <textarea ng-model="shift.comments" cols=50 rows=5></textarea>  </li>
          </ul>
        </div>
      </div>
    </form>
  </div>
<?php } ?>
<?php
if (isset($_GET["id"]) && ($_GET["id"] != 0) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF))) {
  /*удалить из БД*/
?>
<button type="button" class="btn btn-danger kill-shift" ng-click="killShift()" >Удалить смену</button> 
<?php
}
?>
<hr>
<table class="table-bordered">
  <thead>
  <tr>
    <th></th>
    <th>ФИО</th>
    <th>Статус</th>
    <th>Вер.<br>поехать</th>
    <th>Хочет <br> работать<br>на</th>
    <th>Возраст</th>
    <th ng-show="myself.like_one || myself.like_two || myself.like_three">Хочет работать с</th>
    <th ng-show="myself.dislike_one || myself.dislike_two || myself.dislike_three">не хочет работать с</th>
    <th>Комментарии</th>
    <th>Последнее <br> обновление</th>

  </tr>
  </thead>
<tbody>
  <tr>
    <td><a href={{"//vk.com/"+myself.domain}} target="_blank"> <img ng-src="{{myself.photo_50}}"/></a> <br/> 
      ред&nbsp;-&nbsp;вать<br> удалить
    </td>
    <td>{{myself.first_name}}<br/> {{myself.last_name}} &nbsp;</td>
    <td>
      <span ng-show="myself.fighter_id">
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) { ?>
        <a href={{"/about/users/"+myself.fighter_id}}>
          <?php } ?>
          боец
        <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= FIGHTER)) { ?>
        </a>
          <?php } ?>
      </span>
      <span ng-hide="myself.fighter_id">кандидат</span> &nbsp;
    </td>
    <td>{{myself.probability}}%</td>
    <td>
      <span ng-show="myself.social/2">социальные</span><br>
      <span ng-show="myself.social%2">домашние</span><br>
      <span ng-show="myself.profile/2">профильные</span><br>
      <span ng-show="myself.profile%2">непрофильные</span><br>
    </td>
    <td>от {{myself.min_age}} до {{myself.max_age}}</td>
    <td ng-show="myself.like_one || myself.like_two || myself.like_three">
      <table>
        <tr ng-show="myself.like_one">
          <td><img ng-src="{{myself.like_1.photo_50}}"/>&nbsp; </td>
          <td>
            <a href={{"//vk.com/"+myself.like_1.domain}} target="_blank">vk.com/</a><input type="text" disabled ng-model="myself.like_1.domain" size="{{(myself.like_1.domain).length}}"> <br> 
            {{myself.like_1.first_name}} {{myself.like_1.last_name}} 
          </td>
        </tr>
        <tr ng-show="myself.like_two">
          <td><img ng-src="{{myself.like_2.photo_50}}"/>&nbsp; </td>
          <td>
            <a href={{"//vk.com/"+myself.like_2.domain}} target="_blank">vk.com/</a><input type="text" disabled ng-model="myself.like_2.domain" size="{{(myself.like_2.domain).length}}"> <br> 
            {{myself.like_2.first_name}} {{myself.like_2.last_name}} 
          </td>
        </tr>
        <tr ng-show="myself.like_three">
          <td><img ng-src="{{myself.like_3.photo_50}}"/>&nbsp; </td>
          <td>
            <a href={{"//vk.com/"+myself.like_3.domain}} target="_blank">vk.com/</a><input type="text" disabled ng-model="myself.like_3.domain" size="{{(myself.like_3.domain).length}}"> <br> 
            {{myself.like_3.first_name}} {{myself.like_3.last_name}} 
          </td>
        </tr>
      </table>
    </td>
    <td ng-show="myself.dislike_one || myself.dislike_two || myself.dislike_three">
      <table>
        <tr ng-show="myself.dislike_one">
          <td><img ng-src="{{myself.dislike_1.photo_50}}"/>&nbsp; </td>
          <td>
            <a href={{"//vk.com/"+myself.dislike_1.domain}} target="_blank">vk.com/</a><input type="text" disabled ng-model="myself.dislike_1.domain" size="{{(myself.dislike_1.domain).length}}"> <br> 
            {{myself.dislike_1.first_name}} {{myself.dislike_1.last_name}} 
          </td>
        </tr>
        <tr ng-show="myself.dislike_two">
          <td><img ng-src="{{myself.dislike_2.photo_50}}"/>&nbsp; </td>
          <td>
            <a href={{"//vk.com/"+myself.dislike_2.domain}} target="_blank">vk.com/</a><input type="text" disabled ng-model="myself.dislike_2.domain" size="{{(myself.dislike_2.domain).length}}"> <br> 
            {{myself.dislike_2.first_name}} {{myself.dislike_2.last_name}} 
          </td>
        </tr>
        <tr ng-show="myself.dislike_three">
          <td><img ng-src="{{myself.dislike_3.photo_50}}"/>&nbsp; </td>
          <td>
            <a href={{"//vk.com/"+myself.dislike_3.domain}} target="_blank">vk.com/</a><input type="text" disabled ng-model="myself.dislike_3.domain" size="{{(myself.dislike_3.domain).length}}"> <br> 
            {{myself.dislike_3.first_name}} {{myself.dislike_3.last_name}} 
          </td>
        </tr>
      </table>
    </td>
    <td>
      <textarea disabled ng-model="myself.comments"></textarea>
    </td>
    <td>{{myself.cr_time}} </td>
  </tr>

</tbody>
</table>
</div>

<br/><br/><a href="#" class="shift_priv"><<предыдщая</a> &nbsp; &nbsp;
<a href="#" class="shift_next pull-right">следующая>></a>


<?php
}
?>
