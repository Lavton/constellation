<?php
if (isset($_GET["id"]) && (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= CANDIDATE))) {
  /*просмотр мероприятия*/
?>
  <div ng-cloak ng-controller="oneEventApp" id="event-one">
    <br>
    <!-- просмотр мероприятия -->
    <span class="scrl"></span>
    <div class="event-info hidden">
      <div class="col-xs-12">
        <h2><span ng-hide="event.base_id">{{event.name}}</span>
        <span ng-show="event.base_id"><abbr title="{{event.base_dis}}">{{event.name}}</abbr></span>
        <button type="button" ng-show="event.editable" class="btn btn-primary text-right" ng-click="editEventInfo()">Редактировать описание мероприятия</button>
      </h2>
        <hr>
        <span class="saved">  (Изменения сохранены)</span>
      </div>
      <div class="row own-row">
        <div class="col-xs-5 info-str">
          <ul>
            <li ng-show="event.parent_name"><strong>Головное мероприятие:</strong> <a target="_blank" href="{{'/events/'+event.parent_id}}">{{event.parent_name}}</a> 
            <span class="date {{event.parent_date}}">{{formatDate(event.parent_date)}}</span></li>
            <li ng-show="event.place || event.editable"><strong>Место:</strong> {{event.place}}</li>
            <li ng-show="event.start_time"><strong>Начало:</strong> <span class="date {{event.start_date}}">{{formatDate(event.start_date)}}</span> в {{event.start_time}} </li>
            <li ng-show="event.finish_time"><strong>Окончание:</strong> <span class="date {{event.finish_date}}">{{formatDate(event.finish_date)}}</span> в {{event.finish_time}} </li>
            <li ng-show="event.visibility"><strong>Виден для: </strong>{{event.visibility}} ({{window.groups[window.visibilities[event.visibility]].rus}})</li>
            <li ng-show="event.contact || event.editable"><strong>Контактное лицо:</strong> {{event.contact}} </li>
            <li ng-show="event.last_updated"><strong>Последнее обновление</strong> {{formatTimestamp(event.last_updated)}}</li>
            <li ng-show="editors"><strong>Редактирует(ют): </strong> 
            <a href="/about/users/{{editor.editor}}" ng-repeat="editor in editors">{{editor.first_name}} {{editor.last_name}}, </a></li>
            <li ng-show="event.comments"><strong>Комментарии:</strong>
              <br/>
              <div class="table-bordered bb-codes" ng-bind-html="event.bbcomments" ng-show="event.bbcomments"></div>
            <li ng-show="children"><strong>Дочерние мероприятия: </strong> 
              <ul>
                <li ng-repeat="child in children">
                  <a href="/events/{{child.id}}">{{child.name}}</a> 
                  <span class="date {{child.start_date}}">{{formatDate(child.start_date)}}</span>
                </li>
              </ul>
            </li>
          </ul>
        </div>
        <div class="col-xs-7 info-str">
          <button ng-click="applyToEvent()" ng-hide="IAmIn">Записаться на мероприятие</button>
          <br>
          <h4>Записавшиеся люди:</h4>
          <ul>
            <li ng-repeat="person in appliers"> <a href="//vk.com/{{person.domain}}" target="_blank"><img ng-src="{{person.photo}}" width="20"></a> 
             <a href="/about/users/{{person.user}}">{{person.IF}}</a> 
              <span ng-show="person.user==window.getCookie('fighter_id')*1"><img src="/own/images/delete.png" width="20" ng-click='deleteApply()'> </span>
            </li>
          </ul>
          <button ng-click="exportToVK()" ng-show="event.editable">Сгенерировать запись для ВКонтакте</button>
        <div ng-show="vk_export">
          <textarea ng-model="vk_export" rows="10" cols="80"></textarea> <br>
          Это шаблонная концовка новости про мероприятие. Добавьте, что хотите, 
          Скопируйте текст в буффер обмена и вставьте в нужные группы: <a href="https://vk.com/spo_sozvezdie" target="_blank">бойцов</a> или|и <a href="https://vk.com/sozvezdie_school" target="_blank">кандидатов</a>
        </div>
        </div>
      </div>
    </div>

    <!-- редактирование мероприятия -->
    <span ng-show="event.editable">
  <div class="event-edit hidden">
      <div class="col-xs-12">
        <h2>название* <input type="text" ng-model="newevent.name"/> 
          <input type="submit" class="btn btn-success text-right" ng-click="editEventSubmit()" value="Сохранить"></input>
            <button type="button" class="btn btn-primary text-right" ng-click="hideEdit()" >Отменить</input>
        </h2>        
        <hr>
      </div>
      <div class="row own-row">
    <form ng-submit="editEventSubmit()">
        <div class="col-xs-5 info-str">
          <ul>
            <li><span ng-hide="newevent.parent_id"> Базовое мероприятие:  <select ng-change="changeBase(newevent.base_id)" ng-model="newevent.base_id" ng-options="value.id as value.name for (key , value) in eventsBase"></select> </span></li>
            <li><span ng-hide="newevent.base_id"> Головное мероприятие: <select ng-model="newevent.parent_id" ng-options="value.id as value.name for (key , value) in pos_parents"></select></span> <br></li> <br>
            <li>место: <input ng-model="newevent.place" placeholder="место мероприятия" size=50 /> </li><br>
            <li>Дата начала*: <input type="text" class="date" ng-model="newevent.start_date">  {{newevent.start_date}}<br>
            Время начала*: <input type="text" ng-model="newevent.start_time"> {{newevent.start_time}}</li>
            <li>Дата окончания*: <input type="text" class="date" ng-model="newevent.finish_date">  {{newevent.finish_date}}<br>
            Время окончания*: <input type="text" ng-model="newevent.finish_time"> {{newevent.finish_time}} </li><br>
            <li>Контактое лицо: <input type="text" ng-model="newevent.contact"></li>
           <?php if (isset($_SESSION["current_group"]) && ($_SESSION["current_group"] >= COMMAND_STAFF)) { ?> 
             <li><strong>Виден для: </strong><input type="number" min="1" max="7" ng-model="newevent.visibility" size="{{(newevent.visibility).length}}" ng-init="newevent.visibility=3"/> ({{window.groups[window.visibilities[newevent.visibility]].rus}})</li>
           <?php } else { ?> 
             <li ng-show="event.visibility"><strong>Виден для: </strong>{{event.visibility}} ({{window.groups[window.visibilities[event.visibility]].rus}})</li>
           <?php } ?>

           <li>Описание: <br> <textarea ng-model="newevent.comments" class="bbcode" cols="20" rows="5"></textarea></li>
           <br>
          <input type="submit" class="btn btn-success text-right" value="Сохранить"></input>
          </ul>
        </div>
    </form>
        <div class="col-xs-7 info-str">
            <li>Редактируют: <br>
            <div>
             <button ng-click="addToEventEditors(personToAdd)">Добавить к редакторам</button>
             <input class="vk_input" ng-model="personToAdd"> <br>

              <ul>
                <li ng-repeat="person in editors"> 
                 <a href="/about/users/{{person.id}}">{{person.first_name}} {{person.last_name}}</a> 
                  <img src="/own/images/delete.png" width="20" ng-click="deleteFromEditors(person)">
                </li>
              </ul>

            </div>

            </li> <br>
            <hr>
         <button ng-click="applyToEvent(personToApply)">Записать на мероприятие</button>
         <input class="vk_input" ng-model="personToApply"> <br>

          <ul>
            <li ng-repeat="person in appliers"> <a href="//vk.com/{{person.domain}}" target="_blank"><img ng-src="{{person.photo}}" width="20"></a> 
             <a href="/about/users/{{person.user}}">{{person.IF}}</a> 
              <img src="/own/images/delete.png" width="20" ng-click="deleteApply(person)">
            </li>
          </ul>

        </div>
      </div>
  </div>
</span>
<hr>
    <!-- edit -->
    <button type="button" ng-show="event.editable" class="btn btn-danger kill-event" ng-click="killEvent()">Удалить мероприятие</button>
    <?php
}
?>
  </div>
  <br/>
  <br/>
  <a href="#" class="event_priv ajax-nav">
    &lt;&lt; предыдущий</a> &nbsp; &nbsp;
      <a href="#" class="event_next pull-right ajax-nav">следующий&gt;&gt;</a>
