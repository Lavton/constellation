BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//SPO SOzvezdie//spo-sozvezdie.hol.es//
CALSCALE:gregorian
X-WR-CALDESC:Календарь мероприятий кандидатов 
X-WR-CALNAME:СОзвездие. Кандидаты.
BEGIN:VTIMEZONE
TZID:Europe/Moscow
BEGIN:STANDARD
DTSTART;VALUE=DATE-TIME:20150327T030000
RDATE:20150426T010000
TZNAME:MSK
TZOFFSETFROM:+0300
TZOFFSETTO:+0400
END:STANDARD
END:VTIMEZONE
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/own/templates/php_globals.php');
require_once $_SERVER['DOCUMENT_ROOT'].'/own/passwords.php';
define('DATE_ICAL', 'Ymd\THis\Z');
$link = mysqli_connect( 
            Passwords::$db_host,  /* Хост, к которому мы подключаемся */ 
            Passwords::$db_user,       /* Имя пользователя */ 
            Passwords::$db_pass,   /* Используемый пароль */ 
            Passwords::$db_name);     /* База данных для запросов по умолчанию */ 

if (!$link) { 
   printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error()); 
   exit; 
}    
$link->set_charset("utf8");
    // поиск мероприятий
    $query = 'SELECT EM.id, EM.parent_id, EvM.name AS parent_name, EM.name, EM.place, EM.start_date, EM.start_time, 
    EM.finish_date, EM.finish_time, EE.contact, EM.comments, EM.last_updated 
    FROM EventsMain AS EM 
    LEFT JOIN EventsMain AS EvM ON EvM.id=EM.parent_id
    LEFT JOIN EventsEvents AS EE ON EE.id=EM.id
    WHERE (EM.finish_date >= CURRENT_DATE AND EM.visibility <= 2) ORDER BY EM.start_date;';
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
      $in = array(   '/\[b\](.*?)\[\/b\]/ms', 
               '/\[i\](.*?)\[\/i\]/ms',
               '/\[u\](.*?)\[\/u\]/ms',
               '/\[img\](.*?)\[\/img\]/ms',
               '/\[email\](.*?)\[\/email\]/ms',
               '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
               '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
               '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
               '/\[quote](.*?)\[\/quote\]/ms',
               '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
               '/\[list\](.*?)\[\/list\]/ms',
               '/\[\*\]\s?(.*?)/ms',
               '/\[br\]/ms'
      );
      // And replace them by...
      $out = array(  '\1',
               '\1',
               '\1',
               '',
               '\1',
               '\2 ( \1 )',
               '\2',
               '\2',
               '\1',
               '\2',
               '\1',
               '* \1',
               '\n'
      );
      $line["comments"] = preg_replace($in, $out, $line["comments"]);
      $line["comments"] = str_replace("\r", "", $line["comments"]);
      $line["comments"] = " ".preg_replace("/(\n)/", "\\n \n ", $line["comments"])."\\n \n ";

?>

BEGIN:VEVENT
SUMMARY:<?=$line["name"]?> <?php 
if (isset($line["parent_name"])) {
    echo '('.$line["parent_name"].')';
}
?> 
DTSTART;TZID=Europe/Moscow;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["start_date"]." ".$line["start_time"]))?> 
DTEND;TZID=Europe/Moscow;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["finish_date"]." ".$line["finish_time"]))?> 
UID:event-<?=$line[id]?>@spo-sozvezdie.hol.es
LAST-MODIFIED;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["last_updated"]))?> 
DESCRIPTION: <?=$line["comments"]?>
 ( http://spo-sozvezdie.hol.es/events/<?=$line["id"]?> ) \n 
<?php 
if ($line["contact"]) {
  echo " Контактное лицо: ".$line["contact"]."\\n \n";
}
?>
LOCATION: <?=$line["place"]?> 
URL:spo-sozvezdie.hol.es/events/<?=$line[id]?>/
END:VEVENT
<?php
    }
    mysqli_close($link);
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=candidateCal.ics');
?>
END:VCALENDAR