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
    $query = 'SELECT id, parent_id, name, place, start_time, end_time, contact, comments, lastUpdated FROM events WHERE (end_time >= CURRENT_TIMESTAMP AND visibility <= 2) ORDER BY start_time;';
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {
      if (isset($line["parent_id"])) {
        $quer = "SELECT name FROM events where visibility <= 2 AND id=".$line["parent_id"].";";
        $ret = mysqli_query($link, $quer) or die('Запрос не удался: ');
        $res = mysqli_fetch_array($ret, MYSQL_ASSOC);
      } else {
        $res = array();
      }
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
if (isset($res["name"])) {
    echo '('.$res["name"].')';
}
?> 
DTSTART;TZID=Europe/Moscow;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["start_time"]))?> 
DTEND;TZID=Europe/Moscow;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["end_time"]))?> 
UID:event-<?=$line[id]?>@spo-sozvezdie.hol.es
LAST-MODIFIED;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["lastUpdated"]))?> 
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