BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//SPO SOzvezdie//spo-sozvezdie.hol.es//
CALSCALE:gregorian
X-WR-CALDESC:Календарь мероприятий бойцов 
X-WR-CALNAME:СОзвездие. бойцы
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
    $query = 'SELECT id, name, start_time, end_time, comments FROM events WHERE (end_time >= CURRENT_TIMESTAMP AND visibility <= 3) ORDER BY start_time;';
    $rt = mysqli_query($link, $query) or die('Запрос не удался: ');
    while ($line = mysqli_fetch_array($rt, MYSQL_ASSOC)) {

?>

BEGIN:VEVENT
SUMMARY:<?=$line[name]?> 
DTSTART;TZID=Europe/Moscow;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["start_time"]))?> 
DTEND;TZID=Europe/Moscow;VALUE=DATE-TIME:<?=date(DATE_ICAL, strtotime($line["end_time"]))?> 
UID:event-<?=$line[id]?>@spo-sozvezdie.hol.es
DESCRIPTION: <?=$line["comments"]?> ( http://spo-sozvezdie.hol.es/events/<?=$line["id"]?> )
LAST-MODIFIED;VALUE=DATE-TIME:20150508T211510Z
LOCATION:Непонятно, где
URL:spo-sozvezdie.hol.es/events/<?=$line[id]?>/
END:VEVENT
<?php
    }
    mysqli_close($link);
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=fighterCal.ics');
?>
END:VCALENDAR