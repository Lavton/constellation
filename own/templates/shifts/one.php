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
        <details ng-open="open_apply()">
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
              Обычные: <input type="checkbox" ng-model="adding.nonprof" ng-init="adding.nonprof=true">
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
                    <ul>
                      <li ng-repeat="user in adding.vk_likes">
                        <img ng-src="{{user.photo_50}}"/> <input type="text" disabled ng-model="user.domain" size="{{(user.domain).length}}"> {{user.first_name}} {{user.last_name}}
                      </li>
                    </ul>
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

</div>

<br/><br/><a href="#" class="shift_priv"><<предыдщая</a> &nbsp; &nbsp;
<a href="#" class="shift_next pull-right">следующая>></a>


<?php
}
?>
