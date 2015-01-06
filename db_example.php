<html>
<head lang="ru">
    <meta charset="UTF-8">
</head>
  <body>
<?php
// Соединяемся, выбираем базу данных
$link = mysql_connect('127.0.0.1', 'lavton', 'qwerty')
    or die('Не удалось соединиться: ' . mysql_error());
echo 'Соединение успешно установлено';
mysql_select_db('constellation') or die('Не удалось выбрать базу данных');

// Выполняем SQL-запрос
@mysql_query("Set charset utf8");
@mysql_query("Set character_set_client = utf8");
@mysql_query("Set character_set_connection = utf8");
@mysql_query("Set character_set_results = utf8");
@mysql_query("Set collation_connection = utf8_general_ci");

$query = 'SELECT * FROM groups';

$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

// Выводим результаты в html
echo "<table>\n";
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";

// Освобождаем память от результата
mysql_free_result($result);

// Закрываем соединение
mysql_close($link);
?>
</body>
</html>